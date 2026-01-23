<?php

require_once __DIR__ . '/Database.php';

class AppointmentStatusHistory
{
    public int $id = 0;
    public string $appointment_id = '';
    public string $old_status = '';
    public string $new_status = '';
    public string $changed_by = '';
    public string $changed_at = '';

    private \PDO $pdo;

    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function create(): bool
    {
        if ($this->changed_at === '') {
            $this->changed_at = Database::now();
        }

        $sql = 'INSERT INTO appointment_status_history (appointment_id, old_status, new_status, changed_by, changed_at) VALUES (:appointment_id, :old_status, :new_status, :changed_by, :changed_at)';
        $stmt = $this->pdo->prepare($sql);

        $result = $stmt->execute([
            ':appointment_id' => $this->appointment_id,
            ':old_status' => $this->old_status,
            ':new_status' => $this->new_status,
            ':changed_by' => $this->changed_by,
            ':changed_at' => $this->changed_at,
        ]);

        if ($result) {
            $this->id = (int) $this->pdo->lastInsertId();
        }

        return $result;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM appointment_status_history WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function findByAppointmentId(string $appointmentId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM appointment_status_history WHERE appointment_id = :appointment_id ORDER BY changed_at DESC');
        $stmt->execute([':appointment_id' => $appointmentId]);

        return $stmt->fetchAll();
    }

    public function update(): bool
    {
        if ($this->id === 0) {
            throw new InvalidArgumentException('ID is required for update');
        }

        $sql = 'UPDATE appointment_status_history SET appointment_id = :appointment_id, old_status = :old_status, new_status = :new_status, changed_by = :changed_by, changed_at = :changed_at WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':appointment_id' => $this->appointment_id,
            ':old_status' => $this->old_status,
            ':new_status' => $this->new_status,
            ':changed_by' => $this->changed_by,
            ':changed_at' => $this->changed_at,
            ':id' => $this->id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM appointment_status_history WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
