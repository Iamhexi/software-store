<?php
require_once __DIR__.'/../app/controller/UserController.php';
require_once __DIR__.'/../app/controller/CategoryController.php';
require_once __DIR__.'/../app/middleware/AuthenticationService.php';
require_once __DIR__.'/../app/middleware/AuthorizationService.php';
require_once __DIR__.'/../app/middleware/RequestHandler.php';

$request = RequestHandler::get_request();
$method = $request->method;
$endpoint = $request->endpoint;


switch ($endpoint) {
    case Endpoint::Auth: // authentication with a login and password to obtain a bearer token
        $login = $request->get_query_parameter('login');
        $password = $request->get_query_parameter('password');
        
        if ($login === null || $password === null)
            Controller::send_response(400, 'Failure', 'Missing login or/and password');

        if ($method !== 'post') 
            Controller::send_response(405, 'Failure', 'Invalid method');

        $authentication_service = new AuthenticationService;
        $result = $authentication_service->authenticate($login, $password);
        if ($result === false)
            Controller::send_response(401, 'Failure', 'Invalid login or/and password');
        else
            Controller::send_response(200, 'Success', $result);

        break;

    case Endpoint::User:
        if ($method === 'post') { // handles only registration (post) without a bearer token, otherwise requires a bearer token
            $controller = new UserController;
            $controller->handle_request($request);
        } 
        break;

    default:
        break;
}

// Authentication with the obtained bearer token
$authentication_service = new AuthenticationService;
if (($token = $authentication_service->get_bearer_token()) === null)
    Controller::send_response(401, 'Failure', 'Missing or invalid bearer token');

if (!$authentication_service->verify_token($token))
    Controller::send_response(401, 'Failure', 'Expired bearer token');

// Authorization
$authorization_service = new AuthorizationService;
if (!$authorization_service->authorize($token, $endpoint))
    Controller::send_response(403, 'Failure', 'Access forbidden. Insufficient privileges');


// Routing
$controller = match ($endpoint) {
    Endpoint::User => new UserController,
    Endpoint::Category => new CategoryController,

    // TODO: add more implemented controllers here
};

// Handling the request
$response = $controller->handle_request($request);
Controller::send_response($response->code, $response->message, $response->data);