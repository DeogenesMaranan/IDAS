<?php

declare(strict_types=1);

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        $normalizedMethod = strtoupper($method);
        $normalizedPath = $this->normalize($path);

        $handler = $this->routes[$normalizedMethod][$normalizedPath] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        if (is_array($handler) && isset($handler[0]) && is_array($handler[0]) && isset($handler[1]) && is_callable($handler[1])) {
            foreach ($handler[0] as $mw) {
                if (is_callable($mw)) {
                    $ok = $mw();
                } else {
                    $ok = true;
                }
                if ($ok === false) {
                    return;
                }
            }
            $handler[1]();
            return;
        }

        if (is_callable($handler)) {
            $handler();
            return;
        }

        http_response_code(500);
        echo 'Invalid route handler';
    }

    private function normalize(string $path): string
    {
        $normalized = '/' . trim($path, '/');
        return $normalized === '//' ? '/' : (rtrim($normalized, '/') ?: '/');
    }
}
