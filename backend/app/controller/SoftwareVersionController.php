<?php
require_once __DIR__ . '/../model/repository/SoftwareVersionRepository.php';
require_once __DIR__ . '/../model/repository/SoftwareUnitRepository.php';
require_once __DIR__ . '/Controller.php';

// `api/software/{softwareId}/version/{versionId}`
class SoftwareVersionController extends Controller {
    private Repository $software_version_repository;
    private Repository $software_unit_repository;

    public function __construct(Repository $software_version_repository = new SoftwareVersionRepository, Repository $software_unit_repository = new SoftwareUnitRepository) {
        $this->software_version_repository = $software_version_repository;
        $this->software_unit_repository = $software_unit_repository;
    }

    public function get(Request $request): Response {
        $software_id = intval($request->get_path_parameter(1));
        $version_id = $request->get_path_parameter(3);

        $major_version = $request->get_query_parameter('major_version');
        $minor_version = $request->get_query_parameter('minor_version');
        $patch_version = $request->get_query_parameter('patch_version');

        if ($software_id === null)
            return new Response(400, 'Failure', 'Missing software id');
        else if (!is_numeric($software_id) || $software_id < 0)
            return new Response(400, 'Failure', 'Invalid software id. It must be a positive integer');

        // two equal signs handle both endpoint name with front front slash and without it slash at the end
        else if ($version_id == null) {

            if ($this->has_filter($request)) {
                return $this->handle_get_with_filter($software_id, new Version($major_version, $minor_version, $patch_version));

            } else { // return all software versions of the software
              
                $software_versions = $this->software_version_repository->find_by(['software_id' => $software_id]);
                
                if ($software_versions === null)
                    return new Response(404, 'Failure', 'Software versions not found');

                return new Response(200, 'Success', $software_versions);

            }
        } else if ($version_id != null && is_numeric($version_id) && $version_id > 0) { // get single version
            $software_version = $this->software_version_repository->find($version_id);
            if ($software_version === null)
                return new Response(404, 'Failure', 'Software version not found');
            else if ($software_version->software_id !== $software_id)
                return new Response(400, 'Failure', 'Software version does not belong to the software');
            else
                return new Response(200, 'Success', $software_version);
        
        } else if ($major_version === null)
            return new Response(400, 'Failure', 'Missing major version');
        else if (!is_numeric($major_version) || $major_version < 0)
            return new Response(400, 'Failure', 'Invalid major version');
        else if ($minor_version === null)
            return new Response(400, 'Failure', 'Missing minor version');
        else if (!is_numeric($minor_version) || $minor_version < 0)
            return new Response(400, 'Failure', 'Invalid minor version');
        else if ($patch_version === null)
            return new Response(400, 'Failure', 'Missing patch version');
        else if (!is_numeric($patch_version) || $patch_version < 0)
            return new Response(400, 'Failure', 'Invalid patch version');
        else if ($this->has_filter($request)){
            $this->handle_get_with_filter($software_id, new Version($major_version, $minor_version, $patch_version));
        }            

        
    }

    private function handle_get_with_filter(int $software_id, Version $version): Response {
        $software_version = $this->software_version_repository->find_by(['version' => $version]);
        if ($software_version === null)
            return new Response(404, 'Failure', 'Software version not found');
        else if ($software_version[0]->software_id !== $software_id)
            return new Response(400, 'Failure', 'Software version does not belong to the software');
        return new Response(200, 'Success', $software_version);
    }

    private function has_filter(Request $request): bool {
        return $request->get_query_parameter('major_version') !== null
            || $request->get_query_parameter('minor_version') !== null;
    }
    
    public function post(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        
        $major_version = $request->get_body_parameter('major_version');
        $minor_version = $request->get_body_parameter('minor_version');
        $description = $request->get_body_parameter('description'); // optional
        $patch_version = $request->get_body_parameter('patch_version'); // optional

        if ($software_id === null)
            return new Response(400, 'Failure', 'Missing software id. To add a new software version, you must specify the software id');
        else if (!is_numeric($software_id) || $software_id < 0)
            return new Response(400, 'Failure', 'Invalid software id. It must be a positive integer');
        else if ($major_version === null)
            return new Response(400, 'Failure', 'Missing major version');
        else if (!is_numeric($major_version) || $major_version < 0)
            return new Response(400, 'Failure', 'Invalid major version. It must be a positive integer');
        else if ($minor_version === null)
            return new Response(400, 'Failure', 'Missing minor version');
        else if (!is_numeric($minor_version) || $minor_version < 0)
            return new Response(400, 'Failure', 'Invalid minor version. It must be a positive integer');
        else {
            $software_version = $this->software_version_repository->find_by(['software_id' => $software_id]);
            
            $software_unit = $this->software_unit_repository->find($software_id);
            
            if ($software_unit === null)
                    return new Response(404, 'Failure', "No software unit has software id $software_id");
            
            $version = new Version(
                major: $major_version,
                minor: $minor_version,
                patch: !is_numeric($patch_version) || $patch_version < 0 ? null : $patch_version
            );

            $software_version = new SoftwareVersion(
                version_id: null,
                software_id: $software_id,
                description: $description ?? '',
                date_added: new DateTime(),
                version: $version
            );

            if ($this->software_version_repository->save($software_version))
                return new Response(201, 'Success', 'Software version created');
            else
                return new Response(500, 'Failure', 'Could not create a new software version. Internal error.');
                
        }
    }
    

    public function put(Request $request): Response {
        // TODO: implement this
    }

    public function delete(Request $request): Response {
        // TODO: implement this
    }

}