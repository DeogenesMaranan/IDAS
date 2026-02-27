<?php

require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../config.php';

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
        $check = $this->checkAvailability($scheduledAt);
        if (!$check['ok']) return false;

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

    public function checkAvailability(string $scheduledAt): array
    {
        $dt = strtotime($scheduledAt);
        if ($dt === false) return ['ok' => false, 'reason' => 'INVALID'];
        $date = date('Y-m-d', $dt);
        $time = date('H:i', $dt);

        $daily = $this->model->getDailyCounts($date, 1);
        $bookedForDay = isset($daily[$date]) ? (int)$daily[$date] : 0;
        if ($bookedForDay >= (defined('MAX_AVAILABLE_PER_DAY') ? MAX_AVAILABLE_PER_DAY : 100)) return ['ok' => false, 'reason' => 'DAY_FULL'];

        $slotCounts = $this->model->getSlotCounts($date);
        $bookedForSlot = isset($slotCounts[$time]) ? (int)$slotCounts[$time] : 0;
        if ($bookedForSlot >= (defined('MAX_AVAILABLE_PER_SLOT') ? MAX_AVAILABLE_PER_SLOT : 100)) return ['ok' => false, 'reason' => 'SLOT_FULL'];

        return ['ok' => true];
    }
}
