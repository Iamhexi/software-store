<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../model/repository/StatuteViolationReportRepository.php';
require_once __DIR__ . '/../model/RequestStatus.php';

// `api/software/{software_id}/statute_violation_report/`
class StatueViolationReportController extends Controller {
    private StatuteViolationReportRepository $statute_violation_report_repository;

    public function __construct(StatuteViolationReportRepository $statute_violation_report_repository = new StatuteViolationReportRepository()) {
        $this->statute_violation_report_repository = $statute_violation_report_repository;
    }

    public function get(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $report_id = $request->get_path_parameter(3);

        if ($software_id === null)
            return new Response(400, 'failure', 'Software_id is null');
        if ($report_id === null)
        {
            echo 'HERE';
            $reports = $this->statute_violation_report_repository->find_All_by_id($software_id);
            if ($reports === [])
                return new Response(404, 'failure', 'Could not find any reports');
            return new Response(200, 'success', $reports);
        }
        else if ($this->isCorrectPrimaryKey($report_id)) {
            $report = $this->statute_violation_report_repository->find($report_id);
            if ($report === null)
                return new Response(404, 'failure', 'Could not find report with given report_id:'. $report_id);
            return new Response(200, 'success', $report);
        }
        else
            return new Response(400, 'failure', 'Wrong report_id');
    }

    public function post(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $user_id = $request->authority->user_id;
        $rule_point = $request->get_body_parameter('rule_point');
        $description = $request->get_body_parameter('description');

        if ($software_id === null)
            return new Response(400, 'failure', 'Cannot insert a statute_violation_report without a software id');
        else if ($user_id === null)
            return new Response(400, 'failure', 'Cannot insert a statute_violation_report without a user id');
        else if ($rule_point === null || $description === null)
            return new Response(400, 'failure', 'Cannot insert a statute_violation_report without a rule_point OR description');

        $result = $this->statute_violation_report_repository->save(new StatuteViolationReport(
            report_id: null,
            software_id: $software_id,
            user_id: $user_id,
            rule_point: $rule_point,
            description: $description,
            date_added: new DateTime(),
            review_status: RequestStatus::Pending
        ));

        if ($result)
            return new Response(200, 'success', 'Statute_violation_report has been added');
        else
            return new Response(500, 'failure', 'Could not insert Statute_violation_report');

    }

    public function put(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $user_id = $request->authority->user_id;
        $rule_point = $request->get_body_parameter('rule_point');
        $description = $request->get_body_parameter('description');
        $report_id = $request->get_path_parameter(3);
        $review_status = $request->get_body_parameter('review_status');
        try {
            $review_status_chaned = RequestStatus::convert_string_to_request_status($review_status);
        } catch (Exception $e) {
            return new Response(400, 'Failure','Wrong review_status');
        }

        if (!$this->isCorrectPrimaryKey($report_id))
            return new Response(400, 'Failure','Could not update statute_violation_report without correct report_id');
        if ($software_id === null)
            return new Response(400, 'failure', 'Cannot update a statute_violation_report without a software id');
        else if ($user_id === null)
            return new Response(400, 'failure', 'Cannot update a statute_violation_report without a user id');
        else if ($rule_point === null || $description === null || $review_status === null)
            return new Response(400, 'failure', 'Cannot update a statute_violation_report without a rule_point OR description OR review_status');
        else {
            $report = $this->statute_violation_report_repository->find($report_id);
            if ($report === null)
                return new Response(500, 'Failure', "Statute_violation_report with id = $report_id does not exist, thus, cannot be updated" );
            if ($report->user_id !== $user_id)
                return new Response(401, 'failure', 'Cannot update a statute_violation_report if you are not an owner');
            if ($this->statute_violation_report_repository->update(new StatuteViolationReport(
                report_id: $report_id,
                software_id: -1,
                user_id: -1,
                rule_point: $rule_point,
                description: $description,
                date_added: new DateTime(),
                review_status: RequestStatus::convert_string_to_request_status($review_status)
            )))
                return new Response(201, 'Success', 'Statute_violation_report updated');
            else
                return new Response(500, 'Failure', 'Could not update statute_violation_report');
        }
    }

    public function delete(Request $request): Response {
        $software_id = $request->get_path_parameter(1);
        $user_id = $request->authority->user_id;
        $report_id = $request->get_path_parameter(3);

        if (!$this->isCorrectPrimaryKey($report_id))
            return new Response(400, 'Failure','Could not update statute_violation_report without correct report_id');
        if ($software_id === null)
            return new Response(400, 'failure', 'Cannot update a statute_violation_report without a software id');
        else if ($user_id === null)
            return new Response(400, 'failure', 'Cannot update a statute_violation_report without a user id');
        else {
            $report = $this->statute_violation_report_repository->find($report_id);
            if ($report === null)
                return new Response(500, 'Failure', "Statute_violation_report with id = $report_id does not exist, thus, cannot be deleted" );
            if ($report->user_id !== $user_id)
                return new Response(401, 'failure', 'Cannot delete a statute_violation_report if you are not an owner');
            if ($this->statute_violation_report_repository->delete($report_id))
                return new Response(201, 'Success', 'Statute_violation_report deleted');
            else
                return new Response(500, 'Failure', 'Could not delete statute_violation_report');
        }
    }

    private function isCorrectPrimaryKey(mixed $key) : bool {
        return $this->exists($key) && is_numeric($key) && $key >= 0;
    }
    private function exists(mixed $value): bool {
        return isset($value) && !empty($value);
    }

}