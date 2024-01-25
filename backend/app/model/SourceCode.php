<?php 
require_once __DIR__.'/../Config.php';
require_once __DIR__.'/JsonSerializableness.php';
require_once __DIR__.'/Architecture.php';
require_once __DIR__.'/Executable.php';

class SourceCode implements JsonSerializable {
    use JsonSerializableness;

    public function __construct(
        private ?int $code_id,
        private int $version_id,
        private string $filepath
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");
        
        return $this->$propertyName;
    }

    public function compile(Architecture $architecture): ?Executable {
        
        $language = $this->detect_programming_language();
        $sourcePath = $this->filepath;

        $executable_filepath = $this->filepath . '/executable_' . $architecture->value . '.exe';
        $executable = new Executable(null, $this->version_id, $architecture->value, new DateTime(), $executable_filepath);

        if ($language === 'C++') {
            switch ($architecture) {
                case Architecture::Linux_x86_64:
                    $command = "g++ -static -static-libgcc -static-libstdc++ -o {$this->filepath} {$sourcePath}/src/*.cpp";
                    break;

                case Architecture::Windows_x86_64:
                    $command = "x86_64-w64-mingw32-g++ -static -static-libgcc -static-libstdc++ -o {$this->filepath} {$sourcePath}/src/*.cpp";
                    break;

                case Architecture::Linux_ARM64:
                    $command = "aarch64-linux-gnu-g++ -static -static-libgcc -static-libstdc++ -o {$this->filepath} {$sourcePath}/src/*.cpp";
                    break;

                default:
                    throw new Exception("Target architecture {$this->target_architecture} with C++ is not supported yet.");
            }
        } else if ($language === 'Python') {
            switch ($architecture) {
                case Architecture::Linux_x86_64:
                    $command = "pyinstaller --onefile {$sourcePath}/*.py --name {$this->filepath}";
                    break;

                default:
                    throw new Exception("Target architecture {$this->target_architecture} with Python is not supported yet.");
            }
        }

        if (!isset($command))
           throw new Exception("The given programming language $language is not supported yet.");

        system($command, $returnCode);
        
        if ($returnCode === 0)
            return $executable;
        return null;
    }

    private function detect_programming_language(): string {
        $files = scandir($this->filepath);
        foreach ($files as $file) {
            
            if ($file === '.' || $file === '..')
                continue;

            $file = strtolower($file);
            $file_extension = explode('.', $file)[1];

            return match($file_extension) { // first matched files decides
                'cpp', 'cxx', 'h', 'hpp' => 'C++',
                'py' => 'Python',
                default => 'Unrecognised language'
            };
        }

    }

    public function __toString(): string {
        return 'FilePath: ' . $this->filepath;  
    }

}