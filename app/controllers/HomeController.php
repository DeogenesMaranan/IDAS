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
        $appointmentDate = trim((string) ($input['appointment_date'] ?? ''));
        $timeSlot = trim((string) ($input['time_slot'] ?? ''));

        if ($reason === '') {
            $_SESSION['flash'] = ['error' => 'Reason is required.'];
            Response::redirect('/IDSystem/');
            return;
        }
        if ($appointmentDate === '' || $timeSlot === '') {
            $_SESSION['flash'] = ['error' => 'Date and time are required.'];
            Response::redirect('/IDSystem/');
            return;
        }

        // Combine date and time slot into a single datetime string
        $slotParts = explode('-', $timeSlot);
        $startTime = isset($slotParts[0]) ? $slotParts[0] : '08:00';
        $scheduledAt = $appointmentDate . ' ' . $startTime;

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
    /**
     * Admin reschedule appointment handler
     */
    public function rescheduleAppointment(): void
    {
        // Only allow admins/superadmins
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser) || !in_array($sessionUser['role'], ['ADMIN', 'SUPERADMIN'])) {
            $this->json(['error' => 'Unauthorized'], 403);
            return;
        }

        $input = Request::input();
        $id = trim((string)($input['id'] ?? ''));
        $date = trim((string)($input['date'] ?? ''));
        $time = trim((string)($input['time'] ?? ''));
        if ($id === '' || $date === '' || $time === '') {
            $this->json(['error' => 'Missing fields'], 400);
            return;
        }

        $appointmentModel = new Appointment();
        $appt = $appointmentModel->findById($id);
        if (!$appt) {
            $this->json(['error' => 'Appointment not found'], 404);
            return;
        }

        $oldStatus = $appt['status'];
        $appointmentModel->id = $id;
        $appointmentModel->user_id = $appt['user_id'];
        $appointmentModel->reason = $appt['reason'];
        $appointmentModel->id_picture_url = $appt['id_picture_url'];
        $appointmentModel->signature_image = $appt['signature_image'];
        $appointmentModel->contact_person_name = $appt['contact_person_name'];
        $appointmentModel->contact_person_address = $appt['contact_person_address'];
        $appointmentModel->contact_person_number = $appt['contact_person_number'];
        $appointmentModel->scheduled_at = $date . ' ' . $time;
        $appointmentModel->status = 'RESCHEDULED';
        $appointmentModel->created_at = $appt['created_at'];
        $appointmentModel->updated_at = '';

        if ($appointmentModel->update()) {
            // Log status change
            $pdo = $appointmentModel->pdo;
            $stmt = $pdo->prepare('INSERT INTO appointment_status_history (appointment_id, old_status, new_status, changed_by, changed_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$id, $oldStatus, 'RESCHEDULED', $sessionUser['id']]);
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to update appointment'], 500);
        }
    }
}
