<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../controllers/HomeController.php';

$router = new Router();

$router->get('/', function () {
    $controller = new HomeController();
    $controller->index();
});

return $router;
