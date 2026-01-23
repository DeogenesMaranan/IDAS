<?php

require_once __DIR__ . '/Database.php';

class Profile
{
    public string $user_id = '';
    public string $full_name = '';
    public string $student_faculty_id = '';
    public string $department = '';
    public string $year = '';
    public string $course_grade_strand = '';
    public string $updated_at = '';

    private \PDO $pdo;

    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function create(): bool
    {
        if ($this->user_id === '') {
            throw new InvalidArgumentException('user_id is required for profile creation');
        }

        $this->updated_at = $this->updated_at !== '' ? $this->updated_at : Database::now();

        $sql = 'INSERT INTO profiles (user_id, full_name, student_faculty_id, department, year, course_grade_strand, updated_at) VALUES (:user_id, :full_name, :student_faculty_id, :department, :year, :course_grade_strand, :updated_at)';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':user_id' => $this->user_id,
            ':full_name' => $this->full_name,
            ':student_faculty_id' => $this->student_faculty_id,
            ':department' => $this->department,
            ':year' => $this->year,
            ':course_grade_strand' => $this->course_grade_strand,
            ':updated_at' => $this->updated_at,
        ]);
    }

    public function findByUserId(string $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM profiles WHERE user_id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => $userId]);

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function update(): bool
    {
        if ($this->user_id === '') {
            throw new InvalidArgumentException('user_id is required for profile update');
        }

        $this->updated_at = Database::now();

        $sql = 'UPDATE profiles SET full_name = :full_name, student_faculty_id = :student_faculty_id, department = :department, year = :year, course_grade_strand = :course_grade_strand, updated_at = :updated_at WHERE user_id = :user_id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':full_name' => $this->full_name,
            ':student_faculty_id' => $this->student_faculty_id,
            ':department' => $this->department,
            ':year' => $this->year,
            ':course_grade_strand' => $this->course_grade_strand,
            ':updated_at' => $this->updated_at,
            ':user_id' => $this->user_id,
        ]);
    }

    public function delete(string $userId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM profiles WHERE user_id = :user_id');
        return $stmt->execute([':user_id' => $userId]);
    }
}
