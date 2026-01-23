<?php

require_once __DIR__ . '/Database.php';

class QrCode
{
    public string $id = '';
    public string $appointment_id = '';
    public string $qr_data = '';
    public string $generated_at = '';

    private \PDO $pdo;

    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function create(): bool
    {
        if ($this->id === '') {
            $this->id = Database::uuidV4();
        }

        $this->generated_at = $this->generated_at !== '' ? $this->generated_at : Database::now();

        $sql = 'INSERT INTO qr_codes (id, appointment_id, qr_data, generated_at) VALUES (:id, :appointment_id, :qr_data, :generated_at)';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $this->id,
            ':appointment_id' => $this->appointment_id,
            ':qr_data' => $this->qr_data,
            ':generated_at' => $this->generated_at,
        ]);
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM qr_codes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function findByAppointmentId(string $appointmentId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM qr_codes WHERE appointment_id = :appointment_id LIMIT 1');
        $stmt->execute([':appointment_id' => $appointmentId]);

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function update(): bool
    {
        if ($this->id === '') {
            throw new InvalidArgumentException('QR Code ID is required for update');
        }

        $sql = 'UPDATE qr_codes SET appointment_id = :appointment_id, qr_data = :qr_data, generated_at = :generated_at WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':appointment_id' => $this->appointment_id,
            ':qr_data' => $this->qr_data,
            ':generated_at' => $this->generated_at,
            ':id' => $this->id,
        ]);
    }

    public function delete(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM qr_codes WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
