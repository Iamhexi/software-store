<?php
require_once __DIR__ . '/../model/repository/TokenRepository.php';
require_once __DIR__ . '/../model/repository/UserRepository.php';
require_once __DIR__ . '/Endpoint.php';

class AuthorizationService {
    private TokenRepository $token_repository;
    private TokenRepository $user_repository;


    public function authorize(Token $token, Endpoint $endpoint): bool {
        $user_id = $this->token_repository->find($token->token)->user_id;
        if ($user_id === null)
            return false;

        $user = $this->user_repository->find($user_id);
        if ($user === null)
            return false;

        return $this->has_access($user->account_type, $endpoint);
    }

    private function has_access(AccountType $role, Endpoint $endpoint): bool {
        $allowed_for_software_author = [
            Endpoint::Auth => ['get', 'post'],
            Endpoint::User => ['get'],
            Endpoint::Software => ['get', 'post', 'put', 'delete'],
            Endpoint::SoftwareVersion => ['get', 'post', 'put', 'delete'],
            Endpoint::Review => ['get', 'post', 'put', 'delete'],
            Endpoint::Download => ['get', 'post'],
            Endpoint::BugReport => ['get', 'post']
            // TODO: add other more privileges
        ];

        $allowed_for_client = [
            Endpoint::Auth => ['get', 'post'],
            Endpoint::User => ['get'],
            Endpoint::Review => ['get', 'post', 'put', 'delete'],
            Endpoint::Download => ['get', 'post'],
        ];

        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        switch ($role) {
            case AccountType::ADMIN:
                return true;
            case AccountType::SOFTWARE_AUTHOR:
                if (key_exists($endpoint, $allowed_for_software_author)) {
                    $allowed_methods = $allowed_for_software_author[$endpoint];
                    return in_array($request_method, $allowed_methods);
                } else
                    return false;    
                
            case AccountType::CLIENT:   
                if (key_exists($endpoint, $allowed_for_client)) {
                    $allowed_methods = $allowed_for_client[$endpoint];
                    return in_array($request_method, $allowed_methods);
                } else
                    return false;
            
            case AccountType::GUEST:
                if ($endpoint === Endpoint::Auth && $request_method === 'post') // login
                    return true;
                else if ($endpoint === Endpoint::User && $request_method === 'post') // register
                    return true;
                return false;

            default:
                return false;
        }
    }

}