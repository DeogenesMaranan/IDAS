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
}
