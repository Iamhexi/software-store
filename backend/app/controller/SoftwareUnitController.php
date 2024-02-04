<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/RatingController.php';
require_once __DIR__ . '/SoftwareVersionController.php';
require_once __DIR__ . '/ExecutableController.php';
require_once __DIR__ . '/StatueViolationReportController.php';
require_once __DIR__ . '/SourceCodeController.php';
require_once __DIR__ . '/../model/repository/SoftwareUnitRepository.php';
require_once __DIR__ . '/../model/repository/CategoryRepository.php';

class SoftwareUnitController extends Controller {
    private SoftwareUnitRepository $software_unit_repository;
    private CategoryRepository $category_repository;

    public function __construct(
        SoftwareUnitRepository $software_unit_repository = new SoftwareUnitRepository, 
        CategoryRepository $category_repository = new CategoryRepository()
    ) {
        $this->software_unit_repository = $software_unit_repository;
        $this->category_repository = $category_repository;
    }

    public function get(Request $request): Response {
         
        $location = $request->get_path_parameter(2);
        if ($location === 'rating') {
            $rating_controller = new RatingController;
            return $rating_controller->get($request);
        } else if ($location === 'version') {
            if ($request->get_path_parameter(4) == 'source_code') {
                $source_code_controller = new SourceCodeController;
                return $source_code_controller->get($request);
            }

            $software_version_controller = new SoftwareVersionController;
            return $software_version_controller->get($request);
        } else if ($request->get_path_parameter(2) === Endpoint::StatuteViolationReport->value) {
            $statue_violation_report_controller = new StatueViolationReportController();
            return $statue_violation_report_controller->get($request);
        } else if ($request->get_path_parameter(2) === 'executable') {
            $executable_controller = new ExecutableController();
            return $executable_controller->get($request);
        }

        $software_unit_id = $request->get_path_parameter(1);

        if ($this->requests_filtered($request)) {
            $parameters = $this->get_requested_parameter_name($request);
            $software_units = $this->software_unit_repository->find_by($parameters);
            if ($software_units === [])
            {
                $parameters_string = '';
                foreach ($parameters as $key => $value) {
                    $parameters_string .= $key . ': ' . $value . ', ';
                }
                return new Response(404, 'failure', 'Could not find any software units matching the given criterion: '. $parameters_string);
            }

            return new Response(200, 'success', $software_units);
        } 
        else if ($this->requests_single($request)) {

            if (!$this->is_id_valid($software_unit_id))
                return new Response(400, 'failure', 'Invalid software unit id. Id must be a positive integer.');
            $software_unit = $this->software_unit_repository->find($software_unit_id);
            if ($software_unit === null)
                return new Response(404, 'failure', 'Could not find software unit with the given id ' . $software_unit_id);
            return new Response(200, 'success', $software_unit);

        } 
        else if ($this->requests_all($request)){
            $software_units = $this->software_unit_repository->find_all();
            if ($software_units === [])
                return new Response(404, 'failure', 'Could not find any software units');
            return new Response(200, 'success', $software_units);
        }

        return new Response(400, 'failure', 'Invalid request.');
    }

    private function requests_all(Request $request): bool {
        $category_id = $request->get_query_parameter('category_id');
        $author_id = $request->get_query_parameter('author_id');
        $is_blocked = $request->get_query_parameter('is_blocked');
        $name = $request->get_query_parameter('name');

        if ($category_id !== null || $author_id !== null || $is_blocked !== null || $name !== null)
            return false;
        return true;
    }

    private function requests_filtered(Request $request): bool {
        $category_id = $request->get_query_parameter('category_id');
        $author_id = $request->get_query_parameter('author_id');
        $is_blocked = $request->get_query_parameter('is_blocked');
        $name = $request->get_query_parameter('name');

        if ($category_id === null && $author_id === null && $is_blocked === null && $name === null)
            return false;
        return true;
    }

    private function get_requested_parameter_name(Request $request) : array {
        $category_id = $request->get_query_parameter('category_id');
        $author_id = $request->get_query_parameter('author_id');
        $is_blocked = $request->get_query_parameter('is_blocked');
        $name = $request->get_query_parameter('name');

        $parameters_to_search = [];

        if ($category_id !== null)
            $parameters_to_search['category_id'] = $category_id;
        if ($author_id !== null)
            $parameters_to_search['author_id'] = $author_id;
        if ($is_blocked !== null)
            $parameters_to_search['is_blocked'] = $is_blocked;
        if ($name !== null)
            $parameters_to_search['name'] = $name;

        return $parameters_to_search;
    }

    private function requests_single(Request $request): bool {
        $software_id = $request->get_path_parameter(1);

        if ($software_id === null)
            return false;
        return true;
    }

    private function is_id_valid(mixed $id): bool {
        if (!is_numeric($id) || $id < 0)
            return false;
        return true;
    }

    public function post(Request $request): Response {
        if ($request->get_path_parameter(2) === 'rating') {
            $rating_controller = new RatingController;
            return $rating_controller->post($request);
        } else if ($request->get_path_parameter(2) === 'version') {
            if ($request->get_path_parameter(4) == 'source_code') {
                $source_code_controller = new SourceCodeController;
                return $source_code_controller->post($request);
            }
            $software_version_controller = new SoftwareVersionController;
            return $software_version_controller->post($request);
        }
        else if ($request->get_path_parameter(2) === Endpoint::StatuteViolationReport->value) {
            $statue_violation_report_controller = new StatueViolationReportController();
            return $statue_violation_report_controller->post($request);
        }
        
        $name = $request->get_body_parameter('name');
        $author_id = $request->identity->user_id;
        $description = $request->get_body_parameter('description');

        if ($name === null)
            return new Response(400, 'failure', 'Cannot insert a software unit without a name');
        else if ($description === null)
            return new Response(400, 'failure', 'Cannot insert a software unit without a description');
        else if ($author_id === null)
            return new Response(400, 'failure', 'Cannot insert a software unit without an author id');


        $software = $this->software_unit_repository->find_by(['name' => $name]);
        if ($software !== [])
            return new Response(404, 'failure', 'Could not add software with the given name ' . $name . ' as this name already exists');

        $software = new SoftwareUnit(
            software_id: null,
            author_id: $author_id,
            name: $name,
            description: $description,
            link_to_graphic: '',
            is_blocked: false,
        );

        if (!$this->software_unit_repository->save($software))
            return new Response(500, 'failure', 'Could not insert software unit with the given name ' . $name);
        return new Response(201, 'success', 'Software has been created');
    }

    public function put(Request $request): Response {
        if ($request->get_path_parameter(2) === 'rating') {
            $rating_controller = new RatingController;
            return $rating_controller->put($request);
        } else if ($request->get_path_parameter(2) === 'version') {
            $software_version_controller = new SoftwareVersionController;
            return $software_version_controller->put($request);
        }
        else if ($request->get_path_parameter(2) === Endpoint::StatuteViolationReport->value) {
            $statue_violation_report_controller = new StatueViolationReportController();
            return $statue_violation_report_controller->put($request);
        }

        $software_unit_id = $request->get_path_parameter(1);
        $name = $request->get_body_parameter('name');
        $description = $request->get_body_parameter('description');
        $link_to_graphic = $request->get_body_parameter('link_to_graphic');
        $is_blocked = $request->get_body_parameter('is_blocked');

        if ($software_unit_id === null)
            return new Response(400, 'failure', 'Cannot update a software unit without an id');
        else if ($name === null && $description === null && $link_to_graphic === null && $is_blocked === null)
            return new Response(400, 'failure', 'Cannot update a software unit without a name, description, is_blocked or link to any graphic ');

        $software_unit = $this->software_unit_repository->find($software_unit_id);
        if ($software_unit === null)
            return new Response(404, 'failure', 'Could not find software unit with the given id ' . $software_unit_id);

        if ($name !== null)
            $software_unit->name = $name;
        if ($description !== null)
            $software_unit->description = $description;
        if ($link_to_graphic !== null)
            $software_unit->link_to_graphic = $link_to_graphic;
        if ($is_blocked !== null) {
            if ($is_blocked === 'true' || $is_blocked == 1)
                $software_unit->is_blocked = true;
            else if ($is_blocked === 'false' || $is_blocked == 0)
                $software_unit->is_blocked = false;
        }

        if (!$this->software_unit_repository->save($software_unit))
            return new Response(500, 'failure', 'Could not update software unit with the given id ' . $software_unit_id);
        return new Response(200, 'success', 'Software has been deleted');
    }

    public function delete(Request $request): Response {
        if ($request->get_path_parameter(2) === 'rating') {
            $rating_controller = new RatingController;
            return $rating_controller->delete($request);
        } else if ($request->get_path_parameter(2) === 'version') {
            $software_version_controller = new SoftwareVersionController;
            return $software_version_controller->delete($request);
        }
        else if ($request->get_path_parameter(2) === Endpoint::StatuteViolationReport->value) {
            $statue_violation_report_controller = new StatueViolationReportController();
            return $statue_violation_report_controller->delete($request);
        }

        $software_unit_id = $request->get_path_parameter(1);

        if ($software_unit_id === null)
            return new Response(400, 'failure', 'Cannot delete a software unit without an id');

        $software_unit = $this->software_unit_repository->find($software_unit_id);
        if ($software_unit === null)
            return new Response(404, 'failure', 'Could not find software unit with the given id ' . $software_unit_id);

        if (!$this->software_unit_repository->delete($software_unit_id))
            return new Response(500, 'failure', 'Could not delete software unit with the given id ' . $software_unit_id);
        return new Response(200, 'success', 'Software has been deleted');
    }
}