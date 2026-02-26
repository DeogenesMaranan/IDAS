<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/Profile.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../services/AppointmentService.php';

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

            $appointmentModel = new Appointment();
            $appointments = $appointmentModel->findByUserId($sessionUser['id']);

            $roleKey = strtoupper($sessionUser['role'] ?? '');

            // Determine active default per role and render SPA shell
            switch ($roleKey) {
                case 'FACULTY':
                    $activeDefault = 'book';
                    break;
                case 'ADMIN':
                    $activeDefault = 'dashboard';
                    break;
                case 'SUPERADMIN':
                    $activeDefault = 'dashboard';
                    break;
                case 'STUDENT':
                default:
                    $activeDefault = 'book';
                    break;
            }

            $this->view('spa', [
                'title' => 'Dashboard',
                'sessionUser' => $sessionUser,
                'fullName' => $fullName !== '' ? $fullName : $sessionUser['email'],
                'role' => $sessionUser['role'] ?? '',
                'appointments' => $appointments,
                'active' => $activeDefault,
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