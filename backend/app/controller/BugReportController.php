<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../model/repository/BugRepository.php';
require_once __DIR__ . '/../model/repository/UserRepository.php';
require_once __DIR__ . '/../model/repository/SoftwareVersionRepository.php';

class BugReportController extends Controller {
    private Repository $bugReport_repository;
    private Repository $user_repository;
    private Repository $softwareVersion_repository;

    public function __construct(Repository $bugReport_repository = new BugReportRepository, 
                                Repository $userRepository_repository = new UserRepository, 
                                Repository $softwareVersion_repository = new SoftwareVersionRepository) {
        $this->bugReport_repository = $bugReport_repository;
        $this->user_repository = $userRepository_repository;
        $this->softwareVersion_repository = $softwareVersion_repository;
    }

    private function exists(mixed $value): bool {
        return isset($value) && !empty($value);
    }

    private function isCorrectPrimaryKey(mixed $key) : bool {
        return $this->exists($key) && is_numeric($key) && $key >= 0;
    }

    protected function get(Request $request) : Response{
        $bugReport_id = $request->id;

        if (!$this->exists($bugReport_id))
            return new Response(200, 'Success', $this->bugReport_repository->find_all());
        else if (!$this->isCorrectPrimaryKey($bugReport_id))
            return new Response(400, 'Failure', 'Invalid id');
        else {
            $bugReport = $this->bugReport_repository->find($bugReport_id);
            if ($bugReport === null)
                return new Response(404, 'Failure', 'User not found');
            else
                return new Response(200, 'Success', $bugReport);
        }
    }

    protected function post(Request $request) : Response{
        $user_id = $request->get_body_parameter('user_id');
        $version_id = $request->get_body_parameter('version_id');
        $bug_description = $request->get_body_parameter('bug_description');
        
        if ($user_id === NULL)
            return new Response(400, 'failure','Cannot insert an bug report without user_id: '. $user_id);
        else if ($version_id === NULL)
            return new Response(400, 'failure','Cannot insert an bug report without version ID');
        else if ($bug_description)
            return new Response(400, 'failure','Cannot insert an bug report without description');

        
        $user = $this->user_repository->find($user_id);

        if ($user === null)
            return new Response(400, 'failure','Could not find user with the given user_id: '. $user_id);

        $softwareVersion = $this->softwareVersion_repository->find($version_id);

        if ($softwareVersion === null)
            return new Response(400, 'failure','Could not find software version with the given version_id: '. $version_id);

        $bug_report = new BugReport(
            report_id: NULL,
            version_id: $version_id,
            user_id: $user_id,
            title: $request->get_body_parameter('title'),
            description_of_steps_to_get_bug: $request->get_body_parameter('description_of_steps_to_get_bug'),
            bug_description: $request->get_body_parameter('bug_description'),
            date_added: new DateTime(),
            review_status: 'Pending'
        );

        if (!$this->bugReport_repository->save($bug_report))
            return new Response(500, 'failure', 'Could not insert bug report');
        return new Response(201,'success', $bug_report);
    }

    public function put(Request $request): Response {
        $report_id = $request->id;
        $bug_description = $request->get_body_parameter('bug_description');
        $review_status = $request->get_body_parameter('review_status');
        $description_of_steps_to_get_bug = $request->get_body_parameter('description_of_steps_to_get_bug');

        if ($report_id === NULL)
            return new Response(400, 'failure','Cannot update an bug report without report id');

        $bug_report = $this->bugReport_repository->find($report_id);

        if ($bug_report === null)
            return new Response(400, 'failure', 'Could not find bug report with given report_id: '. $report_id);
        else if ($bug_description === null && $review_status === null && $description_of_steps_to_get_bug === null)
            return new Response(400, 'failure', 'Cannot update an bug report without a bug description or review status or description_of_steps_to_get_bug');

        if ($bug_description !== null)
            $bug_report->bug_description = $bug_description;
        if ($review_status !== null)
            $bug_report->review_status = RequestStatus::from($review_status);
        if ($description_of_steps_to_get_bug !== null)
            $bug_report->description_of_steps_to_get_bug = $description_of_steps_to_get_bug;

        if (!$this->bugReport_repository->save($bug_report))
            return new Response(500, 'failure', 'Could not update bug report');
        return new Response(200, 'success', $bug_report);

    }

    public function delete(Request $request): Response {
        $report_id = $request->id;

        if ($report_id === null)
            return new Response(400, 'failure', 'Cannot delete bug report without a report id');

        $bug_report = $this->bugReport_repository->find($report_id);
        if ($bug_report === null)
            return new Response(404, 'failure', 'Could not find bug report with the given report_id: '. $report_id);

        if (!$this->bugReport_repository->delete($report_id))
            return new Response(500, 'failure', 'Could not delete bug report');
        return new Response(200, 'success', 'Bug report has been deleted with the given report_id ' . $report_id);
    }
}