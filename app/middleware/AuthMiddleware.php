<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Response.php';

class AuthMiddleware
{
    public static function isAuthenticated(): bool
    {
        return !empty($_SESSION['user']);
    }

    public static function hasRole(array $roles): bool
    {
        $user = $_SESSION['user'] ?? null;
        if (empty($user)) return false;
        return in_array($user['role'], $roles, true);
    }

    public static function requireAuth(): bool
    {
        if (!self::isAuthenticated()) {
            Response::json(['error' => 'Not authenticated'], 401);
            return false;
        }
        return true;
    }

    public static function requireRole(array $roles): bool
    {
        if (!self::hasRole($roles)) {
            Response::json(['error' => 'Unauthorized'], 403);
            return false;
        }
        return true;
    }
}
