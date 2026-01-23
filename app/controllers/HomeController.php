<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Request.php';

class HomeController extends BaseController
{
    public function index(): void
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        $this->view('home', [
            'title' => 'BEC ID Appointment Portal',
            'sessionUser' => $_SESSION['user'] ?? null,
            'error' => $flash['error'] ?? null,
            'success' => $flash['success'] ?? null,
            'old' => ['email' => '', 'role' => 'STUDENT'],
        ]);
    }
}
