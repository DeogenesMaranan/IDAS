<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/Profile.php';

class HomeController extends BaseController
{
    public function index(): void
    {
        $sessionUser = $_SESSION['user'] ?? null;

        if (!empty($sessionUser)) {
            $profileModel = new Profile();
            $profile = $profileModel->findByUserId($sessionUser['id']);
            $fullName = $sessionUser['full_name'] ?? '';

            if ($fullName === '' && $profile !== null) {
                $fullName = (string) $profile['full_name'];
                $_SESSION['user']['full_name'] = $fullName;
                $sessionUser = $_SESSION['user'];
            }

            $this->view('dashboard', [
                'title' => 'Dashboard',
                'sessionUser' => $sessionUser,
                'fullName' => $fullName !== '' ? $fullName : $sessionUser['email'],
                'role' => $sessionUser['role'] ?? '',
            ]);
            return;
        }

        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        $query = Request::query();
        $viewParam = strtolower((string) ($query['view'] ?? ''));
        $viewMode = $viewParam === 'register' ? 'register' : 'login';

        $this->view('home', [
            'title' => 'BEC ID Appointment Portal',
            'sessionUser' => null,
            'error' => $flash['error'] ?? null,
            'success' => $flash['success'] ?? null,
            'old' => ['email' => '', 'role' => 'STUDENT'],
            'viewMode' => $viewMode,
        ]);
    }
}
