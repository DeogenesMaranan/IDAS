<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

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

    protected function requireAuth(): bool
    {
        return AuthMiddleware::requireAuth();
    }

    protected function requireAdmin(): bool
    {
        return AuthMiddleware::requireRole(['ADMIN', 'SUPERADMIN']);
    }
}
