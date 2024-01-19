<?php

require_once __DIR__ . '/../../../app/model/Exectuable.php';

use PHPUnit\Framework\TestCase;

class ExecutableTest extends TestCase {

    private Executable $executable;

    protected function setUp(): void {
        $this->executable = new Executable(
            executable_id: 1,
            version_id: 2,
            target_architecture: 'Linux x86_64',
            date_compiled: new DateTime(),
            filepath: '/path/to/executable'
        );
    }

    /**
     * @test
     */
    // public function testCompileCPPExecutable(): void {

    //     $this->assertTrue($this->executable->compile());
    // }

    // public function testCompilePythonExecutable(): void {
    //     $this->assertTrue($this->executable->compile());
    // }

    // public function testCompileUnsupportedLanguage(): void {
    //     $this->expectException(Exception::class);
    //     $this->expectExceptionMessage("Target language Java is not supported yet.");
    //     $this->executable->compile();
    // }

    // public function testGetDownloadLink(): void {
    //     $this->assertTrue($this->executable->getDownloadLink() === '/path/to/executable');
    // }

    public function testMagicMethods(): void
    {
        $this->assertEquals(1, $this->executable->__get('executable_id'));
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Property invalid_property does not exist");
        $this->executable->__get('invalid_property');
    }

}
