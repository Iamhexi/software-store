<?php
require_once __DIR__.'/../app/controller/UserController.php';
require_once __DIR__.'/../app/middleware/AuthenticationService.php';
require_once __DIR__.'/../app/middleware/AuthenticationService.php';
require_once __DIR__.'/../app/middleware/Endpoint.php';


$path = $_SERVER['REQUEST_URI'];
$endpoint = Endpoint::fromString(explode('/', $path)[2]); // TODO: remove query params from the path!
$method = strtolower($_SERVER['REQUEST_METHOD']);


// Obtaining a bearer token
$login = $_GET['login'];
$password = $_GET['password'];

// Authentication with a login and password to obtain a bearer token. Endpoint: Auth

// $authentication_service = new AuthenticationService;
// if (isset($login) && isset($password) && !empty($login) && !empty($password)) {
//     if ($method === 'post' && $endpoint === Endpoint::Auth) {
//         $result = $authentication_service->authenticate($login, $password);
//         if ($result === false)
//             Controller::send_response(401, 'Failure', 'Invalid login or/and password');
//         else
//             Controller::send_response(200, 'Success', $result);
//     } else {
//         Controller::send_response(405, 'Failure', 'Invalid method');
//     }
// } 

// else if ($endpoint === Endpoint::User && $method === 'post') { // registration
//     $controller = new UserController;
//     $controller->handle_request(); 
// }


// // Authentication with the obtained bearer token
// if (($token = $authentication_service->getBearerToken()) === null)
//     Controller::send_response(401, 'Failure', 'Invalid bearer token');

// if (!$authentication_service->verify_token($token))
//     Controller::send_response(401, 'Failure', 'Expired or non-existent bearer token');

// // Authorization
// $authorization_service = new AuthorizationService;
// if ($authorization_service->authorize($token, $endpoint))
//     Controller::send_response(403, 'Failure', 'Access forbidden. Insufficient privileges');



// Routing
$controller = match ($endpoint) {
    Endpoint::User => new UserController

    // TODO: add more implemented controllers here
};

// Handling the request
$controller->handle_request();