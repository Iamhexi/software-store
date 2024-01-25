<?php
require_once __DIR__ . '/../model/repository/TokenRepository.php';
require_once __DIR__ . '/../model/repository/UserRepository.php';
require_once __DIR__ . '/Endpoint.php';
require_once __DIR__ . '/../model/Identity.php';

class AuthorizationService {
    private TokenRepository $token_repository;
    private UserRepository $user_repository;

    public function __construct() {
        $this->token_repository = new TokenRepository;
        $this->user_repository = new UserRepository;
    }

    public function authorize(Request $request): bool {
        $token = $request->token;
        if ($token === null)
            return false;

        $endpoint = $request->endpoint;
        $identity = $request->identity;
        $request_method = $request->method;

        return $this->authorize_using_data($token, $endpoint, $request_method);
    }

    private function authorize_using_data(Token $token, Endpoint $endpoint, string $request_method): bool {
        $user_id =  $this->token_repository->find($token->token)->user_id;
        if ($user_id === null)
            return false;

        $user = $this->user_repository->find($user_id);
        if ($user === null)
            return false;



        return $this->has_access($user->account_type, $endpoint, $request_method);
    }

    private function has_access(AccountType $role, Endpoint $endpoint, string $request_method): bool {

        if (!in_array($request_method, ['get', 'post', 'put', 'delete', 'patch']))
            return false;

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
            Endpoint::StatuteViolationReport->value => ['post'],
        ];

        $allowed_for_client = [
                Endpoint::Auth->value => ['get', 'post'],
                Endpoint::User->value => ['get'],
                Endpoint::Review->value => ['get', 'post', 'put', 'delete'],
                Endpoint::Download->value => ['get', 'post'],
                Endpoint::Rating->value => ['get','post','put','delete'],
                Endpoint::StatuteViolationReport->value => ['post'], 
                Endpoint::Software->value => ['get'], 
                Endpoint::SoftwareVersion->value => ['get'], 
                Endpoint::Category->value => ['get'], 
                Endpoint::AccountChangeRequest->value => ['get','post'], 
                Endpoint::BugReport->value => ['post'], 
        ];

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