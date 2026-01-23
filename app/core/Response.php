<?php

declare(strict_types=1);

class Response
{
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public static function view(string $view, array $params = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);
        extract($params, EXTR_SKIP);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
