<?php

require_once __DIR__ . '/Database.php';

class AdminLoginLog
{
    public int $id = 0;
    public string $admin_user_id = '';
    public string $logged_in_at = '';

    private \PDO $pdo;

    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function create(): bool
    {
        if ($this->logged_in_at === '') {
            $this->logged_in_at = Database::now();
        }

        $sql = 'INSERT INTO admin_login_logs (admin_user_id, logged_in_at) VALUES (:admin_user_id, :logged_in_at)';
        $stmt = $this->pdo->prepare($sql);

        $result = $stmt->execute([
            ':admin_user_id' => $this->admin_user_id,
            ':logged_in_at' => $this->logged_in_at,
        ]);

        if ($result) {
            $this->id = (int) $this->pdo->lastInsertId();
        }

        return $result;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_login_logs WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function findByAdminUserId(string $adminUserId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_login_logs WHERE admin_user_id = :admin_user_id ORDER BY logged_in_at DESC');
        $stmt->execute([':admin_user_id' => $adminUserId]);

        return $stmt->fetchAll();
    }

    public function update(): bool
    {
        if ($this->id === 0) {
            throw new InvalidArgumentException('ID is required for update');
        }

        $sql = 'UPDATE admin_login_logs SET admin_user_id = :admin_user_id, logged_in_at = :logged_in_at WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':admin_user_id' => $this->admin_user_id,
            ':logged_in_at' => $this->logged_in_at,
            ':id' => $this->id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM admin_login_logs WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
