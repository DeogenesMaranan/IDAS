<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Response.php';

class BaseController
{
    protected function view(string $view, array $params = []): void
    {
        Response::view($view, $params);
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        Response::json($data, $statusCode);
    }
}
