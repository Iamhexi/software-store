<?php
require_once __DIR__.'/../model/repository/ExecutableRepository.php';
require_once __DIR__.'/../model/repository/SoftwareVersionRepository.php';
require_once __DIR__.'/../model/repository/SourceCodeRepository.php';
require_once __DIR__.'/Controller.php';


// /api/software/{software_id}/executable/{architecture} - download the newest executable for the given software and architecture
class ExecutableController extends Controller {
    private Repository $executable_repository;
    private Repository $software_version_repository;
    private Repository $source_code_repository;

    public function __construct(
        Repository $executable_repository = new ExecutableRepository,
        Repository $software_version_repository = new SoftwareVersionRepository,
        Repository $source_code_repository = new SourceCodeRepository
    ) {
        $this->executable_repository = $executable_repository;
        $this->software_version_repository = $software_version_repository;
        $this->source_code_repository = $source_code_repository;
    }

    public function get(Request $request): Response {
        
        $software_id = $request->get_path_parameter(1);
        if (!$this->exits($software_id))
            return new Response(400, 'Failure', "No software id was provided");
        else if (!$this->is_correct_primary_key($software_id))
            return new Response(400, 'Failure', "Invalid software id was provided. It has to be a positive integer");

        $architecture = $request->get_path_parameter(3);
        if (!$this->exits($architecture))
            return new Response(400, 'Failure', "No architecture was provided");
        else if (!$this->is_architecture_valid($architecture))
            return new Response(400, 'Failure', "Invalid architecture was provided. It has to be one of the following: Windows_x86_64, Linux_x86_64, Linux_ARM64");


        $versions = $this->software_version_repository->find_by(['software_id' => $software_id]);
        if ($versions === [])
            return new Response(404, 'Failure', "There is no versions for the software unit with the given id = $software_id was found");
        
        $number_of_rows = count($versions);
        $newest_version = $versions[$number_of_rows - 1]; // just a heuristic

        $rows = $this->source_code_repository->find_by(['version_id' => $newest_version->version_id]);
        if ($rows === [])
            return new Response(404, 'Failure', "There is no source code for the newest version of the software unit with the given id = $software_id was found");

        $source_code = $rows[0]; // just a heuristic

        $executables = $this->executable_repository->find_by(['version_id' => $newest_version->version_id, 'target_architecture' => $architecture]);

        if ($executables === []) {
            $architecture = Architecture::from($architecture);
            $executable = $source_code->compile($architecture);

            if ($executable === null)
                return new Response(500, 'Failure', "The executable could not be compiled due to an internal error");

            else if (!$this->executable_repository->save($executable))
                return new Response(500, 'Failure', "The executable could not be saved");

            $link = $this->generate_download_link($executable->filepath);
            return new Response(201, 'Success', $link);
        }

        $executable = $executables[0]; // just a heuristic
        
        
        $link = $this->generate_download_link($executable->filepath);
        return new Response(200, 'Success', $link);

    }

    private function generate_download_link(string $filepath): string {
        $filepath = substr($filepath, 1); // remove the first slash
        $path = explode('source_codes/', $filepath)[1];
        $path = urlencode($path);
        return Config::WEB_URL . "/api/download/?file=$path";
    }
        

    private function is_architecture_valid(string $architecture): bool {
        try { // This is not the valid use case of exceptions, but it is the easiest way to check if the architecture is valid
            Architecture::from($architecture);
            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    private function exits(mixed $value): bool {
        return isset($value) && !empty($value);
    }

    private function is_correct_primary_key (mixed $value): bool {
        return is_numeric($value) && $value > 0;
    }

    public function post(Request $request): Response {
        // TODO: Implement post method
    }

    public function put(Request $request): Response {
        // TODO: Implement put method
    }

    public function delete(Request $request): Response {
        // TODO: Implement delete method
    }

}