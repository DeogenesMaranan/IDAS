<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Profile.php';
require_once __DIR__ . '/../core/Request.php';

class UserController extends BaseController
{
    public function student(): void
    {
        if (!$this->requireAuth()) return;

        $sessionUser = $_SESSION['user'] ?? null;
        $profileModel = new Profile();
        $profile = $profileModel->findByUserId((string) $sessionUser['id']);

        $data = [
            'id' => $sessionUser['id'] ?? null,
            'email' => $sessionUser['email'] ?? null,
            'full_name' => $sessionUser['full_name'] ?? ($profile['full_name'] ?? null),
            'student_id' => $profile['student_faculty_id'] ?? null,
            'department' => $profile['department'] ?? null,
            'course_grade_strand' => $profile['course_grade_strand'] ?? null,
            'year' => $profile['year'] ?? null,
            'role' => $sessionUser['role'] ?? null,
        ];

        $this->json($data);
    }
}
