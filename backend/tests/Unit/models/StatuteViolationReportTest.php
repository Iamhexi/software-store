<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../../../app/Config.php';
require_once __DIR__ . '/../../../app/model/StatuteViolationReport.php';

class StatuteViolationReportTest extends TestCase
{
    private StatuteViolationReport $statuteViolationRequest;

    protected function setUp(): void
    {
        $this->statuteViolationRequest = new StatuteViolationReport(
            report_id: 1,
            software_id: 1,
            description: 'some description',
            rule_point: 1,
            date_added: new DateTime(),
            review_status: 'review Status'
            );
    }


    /**
     * @test
     */
    public function testGetMagicMethod(): void
    {
        $this->assertEquals($this->statuteViolationRequest->date_added->format(CONFIG::DB_DATETIME_FORMAT), $this->statuteViolationRequest->__get('date_added'));
        $this->assertEquals(1, $this->statuteViolationRequest->__get('report_id'));
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Property invalid_property does not exist");
        $this->statuteViolationRequest->__get('invalid_property');
    }


}
