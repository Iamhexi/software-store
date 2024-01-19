<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../model/repository/SoftwareUnitRepository.php';

class SoftwareUnitController extends Controller {
    private SoftwareUnitRepository $software_unit_repository;

    public function __construct(SoftwareUnitRepository $software_unit_repository = new SoftwareUnitRepository) {
        $this->software_unit_repository = $software_unit_repository;
    }

    public function get(Request $request): Response {
        $software_unit_id = $request->get_path_parameter(1);
        $name = $request->get_query_parameter('name');

        if ($software_unit_id === null && $name === null) {
            $software_units = $this->software_unit_repository->find_all();
            if ($software_units === null)
                return new Response(404, 'failure', 'Could not find any software units');
            return new Response(200, 'success', $software_units);
        
        } else if ($name !== null) {
            $software_unit = $this->software_unit_repository->find_by('name', $name);
            if ($software_unit === null)
                return new Response(404, 'failure', 'Could not find software unit with the given name ' . $name);
            return new Response(200, 'success', $software_unit);
        }

        $software_unit = $this->software_unit_repository->find($software_unit_id);
        if ($software_unit === null)
            return new Response(404, 'failure', 'Could not find software unit with the given id ' . $software_unit_id);

        return new Response(200, 'success', $software_unit);
    }

    public function post(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $name = $request->get_body_parameter('name');
        $author_id = $request->get_body_parameter('author_id');
        $description = $request->get_body_parameter('description');

        if ($software_id === null)
            return new Response(400, 'failure', 'Cannot insert a software unit without an id');
        else if ($name === null)
            return new Response(400, 'failure', 'Cannot insert a software unit without a name');
        else if ($description === null)
            return new Response(400, 'failure', 'Cannot insert a software unit without a description');
        else if ($author_id === null)
            return new Response(400, 'failure', 'Cannot insert a software unit without an author id');


        $software = $this->software_unit_repository->find_by('name', $name);
        if ($software !== null)
            return new Response(404, 'failure', 'Could add software with the given name ' . $name . ' as this name already exists');

        $software = new SoftwareUnit(
            software_id: $software_id,
            author_id: $author_id,
            name: $name,
            description: $description,
            link_to_graphic: '',
            is_blocked: false
        );

        if (!$this->software_unit_repository->save($software))
            return new Response(500, 'failure', 'Could not insert software unit with the given name ' . $name);
        return new Response(201, 'success', $software);
    }

    public function put(Request $request): Response {
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
            if ($is_blocked === 'true')
                $software_unit->is_blocked = true;
            else if ($is_blocked === 'false')
                $software_unit->is_blocked = false;
        }

        if (!$this->software_unit_repository->save($software_unit))
            return new Response(500, 'failure', 'Could not update software unit with the given id ' . $software_unit_id);
        return new Response(200, 'success', $software_unit);
    }

    public function delete(Request $request): Response {
        $software_unit_id = $request->get_path_parameter(1);

        if ($software_unit_id === null)
            return new Response(400, 'failure', 'Cannot delete a software unit without an id');

        $software_unit = $this->software_unit_repository->find($software_unit_id);
        if ($software_unit === null)
            return new Response(404, 'failure', 'Could not find software unit with the given id ' . $software_unit_id);

        if (!$this->software_unit_repository->delete($software_unit_id))
            return new Response(500, 'failure', 'Could not delete software unit with the given id ' . $software_unit_id);
        return new Response(200, 'success', $software_unit);
    }
}