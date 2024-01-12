<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../../app/Config.php';
require_once __DIR__ . '/../../../app/model/SoftwareUnit.php';
require_once __DIR__ . '/../../../app/model/SoftwareVersion.php';

class SoftwareUnitTest extends TestCase
{
    private SoftwareUnit $softwareUnit;

    protected function setUp(): void
    {
        $this->softwareUnit = new SoftwareUnit(
            software_id: 1,
            author_id: 1,
            name: 'Name',
            description: 'some description',
            link_to_graphic: 'https://www.boredpanda.com/blog/wp-content/uploads/2022/04/raccoon-memes-instagram-624ae8dc2666e__700.jpg',
            is_blocked: false
            );
    }


    /**
     * @test
     */
    public function testBlock(): void {
        $this->softwareUnit->block();
        $this->assertTrue($this->softwareUnit->is_blocked);
    }

    /**
     * @test
     */
    public function testUnBlock(): void {
        $this->softwareUnit->unblock();
        $this->assertFalse($this->softwareUnit->is_blocked);
    }


    /**
     * @test
     */
    // public function testReleaseNewVersion(): void {

    //     $expectedRequest = new SoftwareVersion(
    //         version_id: null,
    //         software_id: $this->softwareUnit->software_id,
    //         description: 'New version description',
    //         date_added: new DateTime(),
    //         major_version: 1,
    //         minor_version: 1,
    //         patch_version: null
    //     );

            //TODO: DATE CHECK ??
    //     $this->assertEquals($expectedRequest, $this->softwareUnit->release_new_version('New version description',1,1));
    // }

    /**
     * @test
     */
    public function testSetInvalidLinkToGraphic(): void {

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid graphic link. Must be a valid URL to an image file ending with jpg, png et cetera.");
        $this->softwareUnit->link_to_graphic = 'httpcs://google.com';
    }

    /**
     * @test
     */
    public function testSetSoftwareid(): void {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Cannot change the software_id");
        $this->softwareUnit->software_id = 2;
    }

    /**
     * @test
     */
    public function testMagicGet(): void {

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Property non_existent_property does not exist");
        $nonExistentProperty = $this->softwareUnit->__get('non_existent_property');
    }

}
