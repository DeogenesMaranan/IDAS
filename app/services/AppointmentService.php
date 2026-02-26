<?php

require_once __DIR__ . '/../models/Appointment.php';

class AppointmentService
{
    protected Appointment $model;

    public function __construct()
    {
        $this->model = new Appointment();
    }

    public function changeStatus(string $id, string $newStatus, string $changedBy): bool
    {
        return $this->model->changeStatus($id, $newStatus, $changedBy);
    }

    public function reschedule(string $id, string $datetime, string $changedBy): bool
    {
        return $this->model->rescheduleWithHistory($id, $datetime, $changedBy);
    }

    public function createAppointment(string $userId, string $reason, string $scheduledAt, array $opts = []): bool
    {
        $appt = new Appointment($this->model->pdo);
        $appt->user_id = $userId;
        $appt->reason = $reason;
        $appt->scheduled_at = $scheduledAt;
        $appt->status = $opts['status'] ?? 'PENDING';
        $appt->id_picture_url = $opts['id_picture_url'] ?? null;
        $appt->signature_image = $opts['signature_image'] ?? null;
        $appt->contact_person_name = $opts['contact_person_name'] ?? null;
        $appt->contact_person_address = $opts['contact_person_address'] ?? null;
        $appt->contact_person_number = $opts['contact_person_number'] ?? null;
        return $appt->create();
    }
}
