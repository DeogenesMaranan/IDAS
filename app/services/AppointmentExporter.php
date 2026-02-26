<?php

require_once __DIR__ . '/../models/Appointment.php';

class AppointmentExporter
{
    /**
     * Export given appointments as CSV to php://output
     */
    public static function toCsv(array $appointments): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=appointments_export_' . date('Ymd_His') . '.csv');
        $output = fopen('php://output', 'w');
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

        foreach ($appointments as $appt) {
            $reason = $appt['reason'] ?? '';
            $id_picture_url = $appt['id_picture_url'] ?? '';
            $signature_image = $appt['signature_image'] ?? '';
            $contact_person_name = $appt['contact_person_name'] ?? '';
            $contact_person_address = $appt['contact_person_address'] ?? '';
            $contact_person_number = $appt['contact_person_number'] ?? '';
            $created_at = $appt['created_at'] ?? '';
            $updated_at = $appt['updated_at'] ?? '';

            if ($reason === '' || $created_at === '' || $id_picture_url === '' || $signature_image === '' || $contact_person_name === '' || $contact_person_address === '' || $contact_person_number === '' || $updated_at === '') {
                $apptModel = new Appointment();
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
                $appt['full_name'] ?? '',
                $appt['department'] ?? '',
                $appt['scheduled_at'] ?? '',
                $appt['id_type'] ?? '',
                $appt['status'] ?? '',
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
