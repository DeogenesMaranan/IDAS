<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AdminLoginLog.php';
require_once __DIR__ . '/../core/Response.php';

class AuthController extends BaseController
{
    public function login(): void
    {
        $input = Request::input();
        $email = trim($input['email'] ?? '');
        $password = (string) ($input['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->renderLogin([
                'error' => 'Email and password are required.',
                'old' => ['email' => $email],
            ]);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            $this->renderLogin([
                'error' => 'Invalid credentials.',
                'old' => ['email' => $email],
            ]);
            return;
        }

        if (!password_verify($password, $user['password_hash'])) {
            $this->renderLogin([
                'error' => 'Invalid credentials.',
                'old' => ['email' => $email],
            ]);
            return;
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        if (in_array($user['role'], ['ADMIN', 'SUPERADMIN'], true)) {
            $log = new AdminLoginLog();
            $log->admin_user_id = $user['id'];
            $log->create();
        }

        $_SESSION['flash'] = ['success' => 'Signed in successfully.'];
        Response::redirect('/');
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        session_start();
        $_SESSION['flash'] = ['success' => 'You have been logged out.'];
        Response::redirect('/');
    }

    private function renderLogin(array $params = []): void
    {
        $sessionUser = $_SESSION['user'] ?? null;
        $defaults = [
            'title' => 'BEC ID Appointment Portal',
            'sessionUser' => $sessionUser,
            'error' => $params['error'] ?? null,
            'success' => $params['success'] ?? null,
            'old' => $params['old'] ?? ['email' => ''],
        ];

        $this->view('home', $defaults);
    }
}
