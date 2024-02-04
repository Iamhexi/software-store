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

        return $this->get_single_source_code($version_id, $software_id);

    }

    private function is_primary_key(mixed $potential_key): bool {
        return is_numeric($potential_key) && $potential_key >= 0;
    }

    private function exists(mixed $variable): bool {
        return isset($variable) && !empty($variable);
    }

    private function get_single_source_code(int $version_id, int $software_id): ?Response {
        $source_code = $this->source_code_repository->find_by(['version_id' => $version_id]);
        if ($source_code === [])
            return new Response(404, 'Failure', "Could not find source code with the version id = $version_id");
    
        $source_code = $source_code[0];
        $directory = $this->read_directory($source_code->filepath);


        if ($directory === [])
            return new Response(200, 'success', "The directory {$source_code->filepath} is empty");

        return new Response(200, 'Success', $directory);

    }

    // Creates an array containing names of the files in the directory with its contents.
    // $path_to_directory: string - absolute path to the directory
    private function read_directory(string $path_to_directory): array {

        $files = array_diff(scandir($path_to_directory), array('.', '..'));
        $files_contents = [];
        foreach ($files as $file) {
            $files_contents[$file] = file_get_contents($path_to_directory . '/' . $file);
        }
        return $files_contents;
    }

    
    public function post(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $version_id = $request->get_path_parameter(3);

        if (!$this->exists($software_id)) 
            return new Response(400, 'failure', 'Missing software id. Could not provide source could without the corresponding software id');
        elseif (!$this->is_primary_key($software_id))
            return new Response(400, 'failure', 'Incorrect software id. It has to be a non-negative integer.');
        elseif (!$this->exists($version_id))
            return new Response(400, 'failure', 'Missing version id');
        elseif (!$this->is_primary_key($version_id))
            return new Response(400, 'failure', 'Incorrect version id. It has to be a non-negative integer.');

        $versions = $this->software_version_repository->find_by(['version_id' => $version_id]);
        if ($versions === [])
            return new Response(404, 'failure', "There is no software version corresponding to the provided software version id $version_id");

        $version = $versions[0];
        $source_code = $version->generate_source_code();

        if ($this->source_code_repository->save($source_code)) {
            $path = $source_code->filepath;
            if (!is_dir($path) && !mkdir($path, 0777, true))
                return new Response(500, 'failure', 'Could not create the directory for the source code');
            return new Response(201, 'success', $source_code);
        }
        return new Response(500, 'failure', 'Could not save the source code');
    }

    public function put(Request $request): Response {
        // TODO: implement this
    }

    public function delete(Request $request): Response {
        // TODO: implement this
    }
}