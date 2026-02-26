<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../services/AppointmentService.php';
require_once __DIR__ . '/../services/AppointmentExporter.php';

class AppointmentController extends BaseController
{
    public function listAppointmentsAjax(): void
    {

        $input = Request::input();
        $search = trim((string)($input['search'] ?? ''));
        $status = trim((string)($input['status'] ?? ''));

        $appointments = Appointment::getAllWithProfile();
        $filtered = [];
        foreach ($appointments as $appt) {
            $match = true;
            if ($search !== '') {
                $match = stripos($appt['full_name'] ?? '', $search) !== false || stripos($appt['id'] ?? '', $search) !== false;
            }
            if ($match && $status !== '') {
                $match = ($appt['status'] ?? '') === $status;
            }
            if ($match) $filtered[] = $appt;
        }

        if ($search === '') {
            if ($status === '') {
                $filtered = $appointments;
            } else {
                $filtered = array_filter($appointments, function ($appt) use ($status) {
                    return ($appt['status'] ?? '') === $status;
                });
            }
        }

        $this->json(array_values($filtered));
    }

    public function dailyCounts(): void
    {
        
        $query = Request::query();
        $start = trim((string)($query['start'] ?? date('Y-m-d')));
        $days = (int)($query['days'] ?? 30);
        $days = max(1, min(365, $days));

        $appointmentModel = new Appointment();
        $this->json($appointmentModel->getDailyCounts($start, $days));
    }

    public function slotCounts(): void
    {
        
        $query = Request::query();
        $date = trim((string)($query['date'] ?? ''));
        if ($date === '') {
            $this->json(['error' => 'Missing date'], 400);
            return;
        }

        $appointmentModel = new Appointment();
        $this->json($appointmentModel->getSlotCounts($date));
    }

    public function rescheduleAppointment(): void
    {
        
        $input = Request::input();
        $id = trim((string)($input['id'] ?? ''));
        $date = trim((string)($input['date'] ?? ''));
        $time = trim((string)($input['time'] ?? ''));
        if ($id === '' || $date === '' || $time === '') {
            $this->json(['error' => 'Missing fields'], 400);
            return;
        }
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser)) { $this->json(['error' => 'Not authenticated'], 401); return; }

        $svc = new AppointmentService();
        $scheduledAt = $date . ' ' . $time;
        if ($svc->reschedule($id, $scheduledAt, (string)$sessionUser['id'])) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to reschedule appointment'], 500);
        }
    }

    public function viewAppointment(): void
    {
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

    public function approveAppointment(): void
    {
        $input = Request::input();
        $id = trim((string)($input['id'] ?? ''));
        if ($id === '') {
            $this->json(['error' => 'Missing appointment ID'], 400);
            return;
        }
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser)) { $this->json(['error' => 'Not authenticated'], 401); return; }

        $svc = new AppointmentService();
        if ($svc->changeStatus($id, 'APPROVED', (string)$sessionUser['id'])) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to approve appointment'], 500);
        }
    }

    public function cancelAppointment(): void
    {
        $input = Request::input();
        $id = trim((string)($input['id'] ?? ''));
        if ($id === '') {
            $this->json(['error' => 'Missing appointment ID'], 400);
            return;
        }
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser)) { $this->json(['error' => 'Not authenticated'], 401); return; }

        $svc = new AppointmentService();
        if ($svc->changeStatus($id, 'CANCELED', (string)$sessionUser['id'])) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to cancel appointment'], 500);
        }
    }

    public function exportAppointmentsExcel(): void
    {
        $search = trim((string)($_GET['search'] ?? ''));
        $status = trim((string)($_GET['status'] ?? ''));
        $appointments = Appointment::getAllWithProfile();
        $filtered = [];
        foreach ($appointments as $appt) {
            $match = true;
            if ($search !== '') {
                $match = stripos($appt['full_name'] ?? '', $search) !== false || stripos($appt['id'] ?? '', $search) !== false;
            }
            if ($match && $status !== '') {
                $match = ($appt['status'] ?? '') === $status;
            }
            if ($match) $filtered[] = $appt;
        }
        if ($search === '') {
            if ($status === '') {
                $filtered = $appointments;
            } else {
                $filtered = array_filter($appointments, function ($appt) use ($status) {
                    return ($appt['status'] ?? '') === $status;
                });
            }
        }

        AppointmentExporter::toCsv($filtered);
    }

    public function completeAppointment(): void
    {
        $input = Request::input();
        $id = trim((string)($input['id'] ?? ''));
        if ($id === '') {
            $this->json(['error' => 'Missing appointment ID'], 400);
            return;
        }
        $sessionUser = $_SESSION['user'] ?? null;
        if (empty($sessionUser)) { $this->json(['error' => 'Not authenticated'], 401); return; }

        $svc = new AppointmentService();
        if ($svc->changeStatus($id, 'COMPLETED', (string)$sessionUser['id'])) {
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to mark as completed'], 500);
        }
    }
}
