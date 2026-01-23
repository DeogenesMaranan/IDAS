<?php

require_once __DIR__ . '/Database.php';

class Appointment
{
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
}
