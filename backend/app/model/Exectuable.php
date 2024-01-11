<?php
require_once __DIR__.'/JsonSerializableness.php';

class Executable implements JsonSerializable {
    use JsonSerializableness;

    public function __construct(
        private ?int $executable_id,
        private int $version_id,
        private string $target_architecture,
        private DateTime $date_compiled,
        private string $filepath
    ) {}

    private function compile(): bool {
        
        // TODO: get source path and application name from SourceCode, SoftwareVersion, SourceCode
        $language = ''; // C++ or Python for now, SourceCode::determineProgrammingLanguage()
        $sourcePath = '';
        $applicationName = '';

        if ($language === 'C++') {
            switch ($this->target_architecture) {
                case 'Linux x86_64':
                    $command = "g++ -static -static-libgcc -static-libstdc++ -o {$this->filepath} {$sourcePath}/src/*.cpp";
                    break;

                case 'Windows x86_64':
                    $command = "x86_64-w64-mingw32-g++ -static -static-libgcc -static-libstdc++ -o {$this->filepath} src/*.cpp";
                    break;

                case 'Linux arm64':
                    $command = "g++ -static -static-libgcc -static-libstdc++ -o {$this->filepath} src/*.cpp";
                    break;

                default:
                    throw new Exception("Target architecture {$this->target_architecture} with C++ is not supported yet.");
            }
        } else if ($language === 'Python') {
            switch ($this->target_architecture) {
                case 'Linux x86_64':
                    $command = "pyinstaller --onefile {$sourcePath}/*.py --name {$this->filepath}";
                    break;

                default:
                    throw new Exception("Target architecture {$this->target_architecture} with Python is not supported yet.");
            }
        }

        system($command, $returnCode);
        $this->date_compiled = new DateTime();
        
        return $returnCode === 0;
    }

    private function getDownloadLink(): string {
        if (!file_exists($this->filepath))
            $this->compile();
        return $this->filepath;
    }

    public function __get(string $name): mixed {
        if ($name === 'download_link')
            return $this->getDownloadLink();
        else if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }

    public function __toString(): string {
        return "Executable: $this->filepath";
    }
}