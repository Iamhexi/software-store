<?php
require_once __DIR__.'/../app/controller/UserController.php';
    $controller = new UserController;
    $controller->handle_request();