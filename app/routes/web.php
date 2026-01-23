<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$router = new Router();

$router->get('/', function () {
    $controller = new HomeController();
    $controller->index();
});

$router->post('/login', function () {
    $controller = new AuthController();
    $controller->login();
});

$router->post('/logout', function () {
    $controller = new AuthController();
    $controller->logout();
});

return $router;
