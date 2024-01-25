<?php 
require_once __DIR__.'/../model/repository/SourceCodeRepository.php';
require_once __DIR__.'/Controller.php';

// /api/software/{softwareId}/version/{versionId}/source_code

class SourceCodeController extends Controller {
    private Repository $source_code_repository;
    private Repository $software_version_repository;

    public function __construct(
        Repository $source_code_repository = new SourceCodeRepository,
        Repository $software_version_repository = new SoftwareVersionRepository
    ) {
        $this->source_code_repository = $source_code_repository;
        $this->software_version_repository = $software_version_repository;
    }

    public function get(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $version_id = $request->get_path_parameter(3);

        if (!$this->exists($software_id)) 
            return new Response(400, 'failure', 'Missing source code id. Could not provide source could without the corresponding software id');
        elseif (!$this->is_primary_key($software_id))
            return new Response(400, 'failure', 'Incorrect software id. It has to be a non-negative integer.');
        elseif (!$this->exists($software_id))
            return new Response(400, 'failure', 'Missing source code id');
        elseif (!$this->is_primary_key($version_id))
            return new Response(400, 'failure', 'Incorrect version id. It has to be a non-negative integer.');

        return $this->get_single_source_code($version_id);

    }

    private function is_primary_key(mixed $potential_key): bool {
        return is_numeric($potential_key) && $potential_key >= 0;
    }

    private function exists(mixed $variable): bool {
        return isset($variable) && !empty($variable);
    }

    private function get_single_source_code(int $version_id): ?Response {
        $source_code = $this->source_code_repository->find_by(['version_id' => $version_id]);
        if ($source_code === [])
        return new Response(404, 'Failure', "Could not find source code with the version id = $version_id");
    
        $source_code = $source_code[0];

        // $file = $source_code->filepath;
        // if (file_exists($file)) {
        //     header('Content-Description: File Transfer');
        //     header('Content-Type: application/octet-stream');
        //     header('Content-Disposition: attachment; filename="'.basename($file).'"');
        //     header('Expires: 0');
        //     header('Cache-Control: must-revalidate');
        //     header('Pragma: public');
        //     header('Content-Length: ' . filesize($file));
        //     readfile($file);
        //     exit;
        // }
        // TODO: add source code of all the files in the directory given in $source_code->filepath

        return new Response(200, 'Success', $source_code);

    }

    
    public function post(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $version_id = $request->get_path_parameter(3);
        $filepath = $request->get_body_parameter('filepath');

        if (!$this->exists($software_id)) 
            return new Response(400, 'failure', 'Missing source code id. Could not provide source could without the corresponding software id');
        elseif (!$this->is_primary_key($software_id))
            return new Response(400, 'failure', 'Incorrect software id. It has to be a non-negative integer.');
        elseif (!$this->exists($software_id))
            return new Response(400, 'failure', 'Missing source code id');
        elseif (!$this->is_primary_key($version_id))
            return new Response(400, 'failure', 'Incorrect version id. It has to be a non-negative integer.');
        elseif(!$this->exists($filepath))

        $version = $this->software_version_repository->find($version_id);
        if ($version === null)
            return new Response(404, 'failure', "There is no software version corresponding to the provided software version id $version_id");
        return new Response(201, 'success', $version->generate_source_code());
    }

    public function put(Request $request): Response {
        // TODO: implement this
    }

    public function delete(Request $request): Response {
        // TODO: implement this
    }
}