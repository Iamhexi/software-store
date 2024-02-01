<?php
require_once __DIR__.'/../app/controller/UserController.php';
require_once __DIR__.'/../app/controller/ReviewController.php';
require_once __DIR__.'/../app/controller/CategoryController.php';
require_once __DIR__.'/../app/controller/BugReportController.php';
require_once __DIR__.'/../app/controller/SoftwareUnitController.php';
require_once __DIR__.'/../app/middleware/AuthenticationService.php';
require_once __DIR__.'/../app/middleware/AuthorizationService.php';
require_once __DIR__.'/../app/middleware/RequestHandler.php';
$request = RequestHandler::get_request();
$method = $request->method;
$endpoint = $request->endpoint;

if ($request->get_path_parameter(0) === 'download') {
    if ($request->has_query_parameter('file') === false)
        Controller::send_response(400, 'Failure', 'Missing file query parameter');
    
    $file = $request->get_query_parameter('file');
    $file = urldecode($file);
    if (file_exists(__DIR__."/../resources/source_codes/$file") === false)
        Controller::send_response(404, 'Failure', 'File not found');
    // download the file
    $filename = explode('/', $file)[1];
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    header('Content-Disposition: attachment; filename='.$file);
    readfile(__DIR__."/../resources/source_codes/$file");
    exit();

}
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
        // Registration without a bearer token
        if ($method === 'post' && $request->get_path_parameter(2) === null) {
            $controller = new UserController;
            $response = $controller->handle_request($request);
            Controller::send_response($response->code, $response->message, $response->data);
        } 
        break;

    default:
        break;
}

// Authentication with the obtained bearer token
$authentication_service = new AuthenticationService;
if (($token = $request->token) === null)
    Controller::send_response(401, 'Failure', 'Missing or invalid bearer token');

if (!$authentication_service->verify_token($token))
    Controller::send_response(401, 'Failure', 'Expired bearer token');

// Authorization
$authorization_service = new AuthorizationService;
$identity = $authentication_service->get_indentity($token);

if (!$authorization_service->authorize($request))
    Controller::send_response(403, 'Failure', 'Access forbidden. Insufficient privileges');

// Copy token to request, to authorize some methods (ex. allow user to delete its raiting)
$request->identity = $identity;

// Routing
$controller = match ($endpoint) {
    Endpoint::User => new UserController,
    Endpoint::Category => new CategoryController,
    Endpoint::BugReport => new BugReportController,
    Endpoint::Software => new SoftwareUnitController,
    Endpoint::Review => new ReviewController,

    default => Controller::send_response(400, 'Failure', 'Wrong endpoint')
    // TODO: add more implemented controllers here
};

// Handling the request
$response = $controller->handle_request($request);
Controller::send_response($response->code, $response->message, $response->data);