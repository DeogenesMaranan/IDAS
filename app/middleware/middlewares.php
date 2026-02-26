<?php

declare(strict_types=1);

require_once __DIR__ . '/AuthMiddleware.php';

function mw_require_auth(): bool
{
    return AuthMiddleware::requireAuth();
}

function mw_require_admin(): bool
{
    return AuthMiddleware::requireRole(['ADMIN', 'SUPERADMIN']);
}
