<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/Profile.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../core/Response.php';

class HomeController extends BaseController
{
    /**
     * Admin AJAX appointment list handler
     */
    public function listAppointmentsAjax(): void
    {
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser) || !in_array($sessionUser['role'], ['ADMIN', 'SUPERADMIN'])) {
            $this->json(['error' => 'Unauthorized'], 403);
            return;
        }
        $input = Request::input();
        $search = trim((string)($input['search'] ?? ''));
        $status = trim((string)($input['status'] ?? ''));
        $appointments = Appointment::getAllWithProfile();
        $filtered = [];
        foreach ($appointments as $appt) {
            $match = true;
            if ($search !== '') {
                $match = stripos($appt['full_name'], $search) !== false || stripos($appt['id'], $search) !== false;
            }
            if ($match && $status !== '') {
                $match = $appt['status'] === $status;
            }
            if ($match) $filtered[] = $appt;
        }
        // If search is empty, show all (filtered by status if set)
        if ($search === '') {
            if ($status === '') {
                $filtered = $appointments;
            } else {
                $filtered = array_filter($appointments, function($appt) use ($status) {
                    return $appt['status'] === $status;
                });
            }
        }
        $this->json(array_values($filtered));
    }
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
    /**
     * Admin view appointment handler
     */
    public function viewAppointment(): void
    {
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser) || !in_array($sessionUser['role'], ['ADMIN', 'SUPERADMIN'])) {
            $this->json(['error' => 'Unauthorized'], 403);
            return;
        }
        $input = Request::input();
        $id = trim((string)($input['id'] ?? ''));
        if ($id === '') {
            $this->json(['error' => 'Missing appointment ID'], 400);
            return;
        }
        $appointmentModel = new Appointment();
        $appt = $appointmentModel->findById($id);
        if (!$appt) {
            $this->json(['error' => 'Appointment not found'], 404);
            return;
        }
        $this->json($appt);
    }

    /**
     * Admin approve appointment handler
     */
    public function approveAppointment(): void
    {
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser) || !in_array($sessionUser['role'], ['ADMIN', 'SUPERADMIN'])) {
            $this->json(['error' => 'Unauthorized'], 403);
            return;
        }
        $input = Request::input();
        $id = trim((string)($input['id'] ?? ''));
        if ($id === '') {
            $this->json(['error' => 'Missing appointment ID'], 400);
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
        $appointmentModel->scheduled_at = $appt['scheduled_at'];
        $appointmentModel->status = 'APPROVED';
        $appointmentModel->created_at = $appt['created_at'];
        $appointmentModel->updated_at = '';
        if ($appointmentModel->update()) {
            $pdo = $appointmentModel->pdo;
            $stmt = $pdo->prepare('INSERT INTO appointment_status_history (appointment_id, old_status, new_status, changed_by, changed_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$id, $oldStatus, 'APPROVED', $sessionUser['id']]);
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to approve appointment'], 500);
        }
    }

    /**
     * Admin cancel appointment handler
     */
    public function cancelAppointment(): void
    {
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser) || !in_array($sessionUser['role'], ['ADMIN', 'SUPERADMIN'])) {
            $this->json(['error' => 'Unauthorized'], 403);
            return;
        }
        $input = Request::input();
        $id = trim((string)($input['id'] ?? ''));
        if ($id === '') {
            $this->json(['error' => 'Missing appointment ID'], 400);
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
        $appointmentModel->scheduled_at = $appt['scheduled_at'];
        $appointmentModel->status = 'CANCELED';
        $appointmentModel->created_at = $appt['created_at'];
        $appointmentModel->updated_at = '';
        if ($appointmentModel->update()) {
            $pdo = $appointmentModel->pdo;
            $stmt = $pdo->prepare('INSERT INTO appointment_status_history (appointment_id, old_status, new_status, changed_by, changed_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$id, $oldStatus, 'CANCELED', $sessionUser['id']]);
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to cancel appointment'], 500);
        }
    }

    /**
     * Export filtered appointments to Excel (CSV for compatibility)
     */
    public function exportAppointmentsExcel(): void
    {
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser) || !in_array($sessionUser['role'], ['ADMIN', 'SUPERADMIN'])) {
            http_response_code(403);
            echo 'Unauthorized';
            return;
        }
        $search = trim((string)($_GET['search'] ?? ''));
        $status = trim((string)($_GET['status'] ?? ''));
        $appointments = \Appointment::getAllWithProfile();
        $filtered = [];
        foreach ($appointments as $appt) {
            $match = true;
            if ($search !== '') {
                $match = stripos($appt['full_name'], $search) !== false || stripos($appt['id'], $search) !== false;
            }
            if ($match && $status !== '') {
                $match = $appt['status'] === $status;
            }
            if ($match) $filtered[] = $appt;
        }
        if ($search === '') {
            if ($status === '') {
                $filtered = $appointments;
            } else {
                $filtered = array_filter($appointments, function($appt) use ($status) {
                    return $appt['status'] === $status;
                });
            }
        }
        // Output as CSV (Excel-compatible)
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=appointments_export_' . date('Ymd_His') . '.csv');
        $output = fopen('php://output', 'w');
        // Output header row (all fields)
        fputcsv($output, [
            'Ref Number',
            'Name',
            'Department',
            'Date & Time',
            'ID Type',
            'Status',
            'Reason',
            'ID Picture URL',
            'Signature Image',
            'Contact Person Name',
            'Contact Person Address',
            'Contact Person Number',
            'Created At',
            'Updated At',
        ]);
        foreach ($filtered as $appt) {
            // Try to get extra fields if not present in getAllWithProfile
            $reason = $appt['reason'] ?? '';
            $id_picture_url = $appt['id_picture_url'] ?? '';
            $signature_image = $appt['signature_image'] ?? '';
            $contact_person_name = $appt['contact_person_name'] ?? '';
            $contact_person_address = $appt['contact_person_address'] ?? '';
            $contact_person_number = $appt['contact_person_number'] ?? '';
            $created_at = $appt['created_at'] ?? '';
            $updated_at = $appt['updated_at'] ?? '';
            // If any are missing, fetch full row
            if ($reason === '' || $created_at === '' || $id_picture_url === '' || $signature_image === '' || $contact_person_name === '' || $contact_person_address === '' || $contact_person_number === '' || $updated_at === '') {
                $apptModel = new \Appointment();
                $full = $apptModel->findById($appt['id']);
                if ($full) {
                    $reason = $full['reason'] ?? '';
                    $id_picture_url = $full['id_picture_url'] ?? '';
                    $signature_image = $full['signature_image'] ?? '';
                    $contact_person_name = $full['contact_person_name'] ?? '';
                    $contact_person_address = $full['contact_person_address'] ?? '';
                    $contact_person_number = $full['contact_person_number'] ?? '';
                    $created_at = $full['created_at'] ?? '';
                    $updated_at = $full['updated_at'] ?? '';
                }
            }
            fputcsv($output, [
                $appt['id'],
                $appt['full_name'],
                $appt['department'],
                $appt['scheduled_at'],
                $appt['id_type'],
                $appt['status'],
                $reason,
                $id_picture_url,
                $signature_image,
                $contact_person_name,
                $contact_person_address,
                $contact_person_number,
                $created_at,
                $updated_at,
            ]);
        }
        fclose($output);
        exit;
    }
}
