<?php

declare(strict_types=1);

class Request
{
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($scriptDir !== '' && strpos($path, $scriptDir) === 0) {
            $path = substr($path, strlen($scriptDir)) ?: '/';
        }

        $normalized = '/' . trim($path, '/');

        if ($normalized === '//' || $normalized === '') {
            return '/';
        }

        $normalized = rtrim($normalized, '/');

        return $normalized === '' ? '/' : $normalized;
    }

    public static function query(): array
    {
        return $_GET ?? [];
    }

    public static function input(): array
    {
        return $_POST ?? [];
    }
}
