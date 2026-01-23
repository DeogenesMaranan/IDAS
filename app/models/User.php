<?php

require_once __DIR__ . '/Database.php';

class User
{
    public string $id = '';
    public string $role = '';
    public string $email = '';
    public string $password_hash = '';
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

        $sql = 'INSERT INTO users (id, role, email, password_hash, created_at, updated_at) VALUES (:id, :role, :email, :password_hash, :created_at, :updated_at)';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => $this->id,
            ':role' => $this->role,
            ':email' => $this->email,
            ':password_hash' => $this->password_hash,
            ':created_at' => $this->created_at,
            ':updated_at' => $this->updated_at,
        ]);
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function update(): bool
    {
        if ($this->id === '') {
            throw new InvalidArgumentException('User ID is required for update');
        }

        $this->updated_at = Database::now();

        $sql = 'UPDATE users SET role = :role, email = :email, password_hash = :password_hash, updated_at = :updated_at WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':role' => $this->role,
            ':email' => $this->email,
            ':password_hash' => $this->password_hash,
            ':updated_at' => $this->updated_at,
            ':id' => $this->id,
        ]);
    }

    public function delete(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
