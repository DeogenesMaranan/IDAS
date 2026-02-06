<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/Profile.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../core/Response.php';

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

    public function storeAppointment(): void
    {
        $sessionUser = $_SESSION['user'] ?? null;

        if (empty($sessionUser)) {
            $_SESSION['flash'] = ['error' => 'You must be signed in to create an appointment.'];
            Response::redirect('/IDSystem/');
            return;
        }

        $input = Request::input();
        $reason = trim((string) ($input['reason'] ?? ''));
        $scheduledAt = trim((string) ($input['scheduled_at'] ?? ''));

        if ($reason === '') {
            $_SESSION['flash'] = ['error' => 'Reason is required.'];
            Response::redirect('/IDSystem/');
            return;
        }

        $appointment = new Appointment();
        $appointment->user_id = $sessionUser['id'];
        $appointment->reason = $reason;
        $appointment->scheduled_at = $scheduledAt;
        $appointment->status = 'PENDING';

        if ($appointment->create()) {
            $_SESSION['flash'] = ['success' => 'Appointment created successfully.'];
        } else {
            $_SESSION['flash'] = ['error' => 'Failed to create appointment.'];
        }

        Response::redirect('/IDSystem/');
    }

    public function student(): void
    {
        $sessionUser = $_SESSION['user'] ?? null;

        if (empty($sessionUser)) {
            $this->json(['error' => 'Not authenticated'], 401);
            return;
        }

        $profileModel = new Profile();
        $profile = $profileModel->findByUserId((string) $sessionUser['id']);

        $data = [
            'id' => $sessionUser['id'] ?? null,
            'email' => $sessionUser['email'] ?? null,
            'full_name' => $sessionUser['full_name'] ?? ($profile['full_name'] ?? null),
            'student_id' => $profile['student_faculty_id'] ?? null,
            'department' => $profile['department'] ?? null,
            'course_grade_strand' => $profile['course_grade_strand'] ?? null,
            'year' => $profile['year'] ?? null,
            'role' => $sessionUser['role'] ?? null,
        ];

        $this->json($data);
    }
}
