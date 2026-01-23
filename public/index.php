<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/core/Request.php';

$router = require __DIR__ . '/../app/routes/web.php';

$router->dispatch(Request::method(), Request::path());
