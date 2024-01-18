<?php
require_once __DIR__ . '/../model/repository/TokenRepository.php';
require_once __DIR__ . '/../model/repository/UserRepository.php';
require_once __DIR__ . '/Endpoint.php';

class AuthorizationService {
    private TokenRepository $token_repository;
    private UserRepository $user_repository;

    public function __construct() {
        $this->token_repository = new TokenRepository;
        $this->user_repository = new UserRepository;
    }

    public function authorize(Token $token, Endpoint $endpoint): bool {
        $user_id =  $this->token_repository->find($token->token)->user_id;
        if ($user_id === null)
            return false;

        $user = $this->user_repository->find($user_id);
        if ($user === null)
            return false;

        return $this->has_access($user->account_type, $endpoint);
    }

    private function has_access(AccountType $role, Endpoint $endpoint): bool {

        $allowed_for_software_author = [
            Endpoint::Software->value => ['get', 'post', 'put', 'delete'],
            Endpoint::SoftwareVersion->value => ['get', 'post', 'put', 'delete'],
            Endpoint::Review->value => ['get', 'post', 'put', 'delete'],
            Endpoint::SourceCode->value => ['get', 'post', 'put', 'delete'],
            Endpoint::Download->value => ['get', 'post'],
            Endpoint::BugReport->value => ['get', 'post', 'put'],
            Endpoint::User->value => ['get'],
            Endpoint::Auth->value => ['get', 'post'],
            Endpoint::Category->value => ['get'],
            Endpoint::Rating->value => ['get','post','put','delete'],
            Endpoint::StatuteViolationRequest->value => ['post'],
        ];

        $allowed_for_client = [
                Endpoint::Auth->value => ['get', 'post'],
                Endpoint::User->value => ['get'],
                Endpoint::Review->value => ['get', 'post', 'put', 'delete'],
                Endpoint::Download->value => ['get', 'post'],
                Endpoint::Rating->value => ['get','post','put','delete'],
                Endpoint::StatuteViolationRequest->value => ['post'], 
                Endpoint::Software->value => ['get'], 
                Endpoint::SoftwareVersion->value => ['get'], 
                Endpoint::Category->value => ['get'], 
                Endpoint::AccountChangeRequest->value => ['get','post'], 
                Endpoint::BugReport->value => ['post'], 
        ];


        $request_method = strtolower($_SERVER['REQUEST_METHOD']);

        switch ($role) {
            case AccountType::ADMIN:
                return true;
            case AccountType::SOFTWARE_AUTHOR:
                if (key_exists($endpoint->value, $allowed_for_software_author)) {
                    $allowed_methods = $allowed_for_software_author[$endpoint->value];
                    return in_array($request_method, $allowed_methods);
                } else
                    return false;    
                
            case AccountType::CLIENT:   
                if (key_exists($endpoint->value, $allowed_for_client)) {
                    $allowed_methods = $allowed_for_client[$endpoint->value];
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