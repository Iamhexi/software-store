<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../model/repository/AccountChangeRequestRepository.php';

class AccountChangeRequestController extends Controller {
    private AccountChangeRequestRepository $account_change_request_repository;
    private UserRepository $user_repository;
 
    public function __construct(AccountChangeRequestRepository $account_change_request_repository = new AccountChangeRequestRepository, UserRepository $user_repository = new UserRepository) {
        $this->account_change_request_repository = $account_change_request_repository;
        $this->user_repository = $user_repository;
    }

    public function get(Request $request): Response {
        $user_id = $request->get_path_parameter(1);
        if ($user_id === '')
        {
            $account_change_request = $this->account_change_request_repository->find_all();
            if  ($account_change_request === [])
                return new Response(400, 'failure', 'Account change requests not found');
            else
                return new Response(200, 'success', $account_change_request);
        }

        $account_change_request = $this->account_change_request_repository->find_by(['user_id' => $user_id]);
        if ($account_change_request === [])
            return new Response(404, 'failure', 'Could not find account change request with the given user id ' . $user_id);

        return new Response(200, 'success', $account_change_request);
    }

    public function post(Request $request): Response {
        $user_id = $request->get_path_parameter(1);
        $description = $request->get_body_parameter('description');

        if ($user_id === null)
            return new Response(400, 'failure', 'Cannot insert an account change request without a user id');
        else if ($description === null)
            return new Response(400, 'failure', 'Cannot insert an account change request without a description');

        $account_change_request = $this->account_change_request_repository->find_by(['user_id' => $user_id]);
        if ($account_change_request !== [])
            return new Response(400, 'failure', 'Cannot insert an account change request as one already exists for the user with the given user_id ' . $user_id);
        
        $user = $this->user_repository->find($user_id);
        if ($user === null) 
            return new Response(404, 'failure', 'Could not find user with the given user id ' . $user_id);
        if ($user->user_id !== $request->identity->user_id)
            return new Response(401, 'failure', 'Could not create an account change request, if you are not an owner of this account');


        try {
            $account_change_request = $user->generate_account_change_request($description);
        } catch (Exception $e) {
            return new Response(400, 'failure', $e->getMessage());
        }
        if (!$this->account_change_request_repository->save($account_change_request))
            return new Response(500, 'failure', 'Could not insert account change request with the given user id ' . $user_id);
        return new Response(201, 'success', $account_change_request);
    }

    public function put(Request $request): Response {
        $user_id = $request->get_path_parameter(1);
        $description = $request->get_body_parameter('description');
        $justification = $request->get_body_parameter('justification');
        $review_status = $request->get_body_parameter('review_status');

        if ($user_id === null)
            return new Response(400, 'failure', 'Cannot update an account change request without a user id');
        else if ($description === null && $review_status === null && $justification === null)
            return new Response(400, 'failure', 'Cannot update an account change request with neither a description nor review status, nor justification');

        $account_change_requests = $this->account_change_request_repository->find_by(['user_id' => $user_id]);
        if ($account_change_requests === [])
            return new Response(404, 'failure', 'Could not find account change request with the given user id ' . $user_id);

        if (count($account_change_requests) > 1)
            return new Response(500, 'failure', 'Found multiple account change requests with the given user id ' . $user_id);
        $account_change_request = $account_change_requests[0];

        if ($description !== null)
            $account_change_request->description = $description;
        if ($review_status !== null)
            $account_change_request->review_status = RequestStatus::from($review_status);
        if ($justification !== null)
            $account_change_request->justification = $justification;

        if (!$this->account_change_request_repository->save($account_change_request))
            return new Response(500, 'failure', 'Could not update account change request with the given user id ' . $user_id);
        return new Response(200, 'success', $account_change_request);

    }

    public function delete(Request $request): Response {
        $user_id = $request->get_path_parameter(1);

        if ($user_id === null)
            return new Response(400, 'failure', 'Cannot delete account change request without a user id');

        $account_change_request = $this->account_change_request_repository->find_by(['user_id' => $user_id]);
        if ($account_change_request === null)
            return new Response(404, 'failure', 'Could not find account change request with the given user id ' . $user_id);

        if (!$this->account_change_request_repository->delete($user_id))
            return new Response(500, 'failure', 'Could not delete account change request with the given user id ' . $user_id);
        return new Response(200, 'success', 'Account change request deleted with the given user id ' . $user_id);
    }
}