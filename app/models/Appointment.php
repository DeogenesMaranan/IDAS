<?php

require_once __DIR__ . '/Database.php';

class Appointment
{
    public static function getAllWithProfile(): array
    {
        $pdo = Database::getConnection();
        $sql = 'SELECT a.id, p.full_name, p.department, a.scheduled_at, a.status, p.student_faculty_id AS id_type, a.reason
            FROM appointments a
            LEFT JOIN profiles p ON a.user_id = p.user_id
            ORDER BY a.scheduled_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public string $id = '';
    public string $user_id = '';
    public string $reason = '';
    public ?string $id_picture_url = null;
    public ?string $signature_image = null;
    public ?string $contact_person_name = null;
    public ?string $contact_person_address = null;
    public ?string $contact_person_number = null;
    public string $scheduled_at = '';
    public string $status = '';
    public string $created_at = '';
    public string $updated_at = '';

    public \PDO $pdo;

    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function create(): bool
    {
        if ($this->id === '') {
            $this->id = Database::uuidV4();
        }

        if ($this->created_at === '') {
            $this->created_at = Database::now();
        }

        $this->updated_at = $this->updated_at !== '' ? $this->updated_at : $this->created_at;

        $sql = 'INSERT INTO appointments (id, user_id, reason, id_picture_url, signature_image, contact_person_name, contact_person_address, contact_person_number, scheduled_at, status, created_at, updated_at) VALUES (:id, :user_id, :reason, :id_picture_url, :signature_image, :contact_person_name, :contact_person_address, :contact_person_number, :scheduled_at, :status, :created_at, :updated_at)';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $this->id,
            ':user_id' => $this->user_id,
            ':reason' => $this->reason,
            ':id_picture_url' => $this->id_picture_url,
            ':signature_image' => $this->signature_image,
            ':contact_person_name' => $this->contact_person_name,
            ':contact_person_address' => $this->contact_person_address,
            ':contact_person_number' => $this->contact_person_number,
            ':scheduled_at' => $this->scheduled_at,
            ':status' => $this->status,
            ':created_at' => $this->created_at,
            ':updated_at' => $this->updated_at,
        ]);
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM appointments WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function findByUserId(string $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM appointments WHERE user_id = :user_id ORDER BY scheduled_at DESC');
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function update(): bool
    {
        if ($this->id === '') {
            throw new InvalidArgumentException('Appointment ID is required for update');
        }

        $this->updated_at = Database::now();

        $sql = 'UPDATE appointments SET user_id = :user_id, reason = :reason, id_picture_url = :id_picture_url, signature_image = :signature_image, contact_person_name = :contact_person_name, contact_person_address = :contact_person_address, contact_person_number = :contact_person_number, scheduled_at = :scheduled_at, status = :status, updated_at = :updated_at WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':user_id' => $this->user_id,
            ':reason' => $this->reason,
            ':id_picture_url' => $this->id_picture_url,
            ':signature_image' => $this->signature_image,
            ':contact_person_name' => $this->contact_person_name,
            ':contact_person_address' => $this->contact_person_address,
            ':contact_person_number' => $this->contact_person_number,
            ':scheduled_at' => $this->scheduled_at,
            ':status' => $this->status,
            ':updated_at' => $this->updated_at,
            ':id' => $this->id,
        ]);
    }

    public function delete(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM appointments WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function changeStatus(string $id, string $newStatus, string $changedBy): bool
    {
        try {
            $this->pdo->beginTransaction();
            $appt = $this->findById($id);
            if (!$appt) {
                $this->pdo->rollBack();
                return false;
            }
            $oldStatus = $appt['status'];

            $stmt = $this->pdo->prepare('UPDATE appointments SET status = :status, updated_at = :updated_at WHERE id = :id');
            $updatedAt = Database::now();
            $stmt->execute([':status' => $newStatus, ':updated_at' => $updatedAt, ':id' => $id]);

            $stmt2 = $this->pdo->prepare('INSERT INTO appointment_status_history (appointment_id, old_status, new_status, changed_by, changed_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt2->execute([$id, $oldStatus, $newStatus, $changedBy]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    }

    public function rescheduleWithHistory(string $id, string $datetime, string $changedBy): bool
    {
        try {
            $this->pdo->beginTransaction();
            $appt = $this->findById($id);
            if (!$appt) {
                $this->pdo->rollBack();
                return false;
            }

            $stmt = $this->pdo->prepare('UPDATE appointments SET scheduled_at = :scheduled_at, status = :status, updated_at = :updated_at WHERE id = :id');
            $updatedAt = Database::now();
            $stmt->execute([':scheduled_at' => $datetime, ':status' => 'RESCHEDULED', ':updated_at' => $updatedAt, ':id' => $id]);

            $stmt2 = $this->pdo->prepare('INSERT INTO appointment_status_history (appointment_id, old_status, new_status, changed_by, changed_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt2->execute([$id, $appt['status'], 'RESCHEDULED', $changedBy]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    }

    public function getDailyCounts(string $start, int $days = 30): array
    {
        $stmt = $this->pdo->prepare("SELECT DATE(scheduled_at) AS d, COUNT(*) AS c FROM appointments WHERE DATE(scheduled_at) >= :start AND status = 'APPROVED' GROUP BY DATE(scheduled_at)");
        $stmt->execute([':start' => $start]);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['d']] = (int)$r['c'];
        }

        $result = [];
        $dt = new DateTime($start);
        for ($i = 0; $i < $days; $i++) {
            $dstr = $dt->format('Y-m-d');
            $result[$dstr] = $map[$dstr] ?? 0;
            $dt->modify('+1 day');
        }

        return $result;
    }

    public function getSlotCounts(string $date): array
    {
        $stmt = $this->pdo->prepare("SELECT DATE_FORMAT(scheduled_at, '%H:%i') AS t, COUNT(*) AS c FROM appointments WHERE DATE(scheduled_at) = :date AND status = 'APPROVED' GROUP BY t");
        $stmt->execute([':date' => $date]);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['t']] = (int)$r['c'];
        }
        return $map;
    }
}
