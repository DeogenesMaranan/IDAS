<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AdminLoginLog.php';
require_once __DIR__ . '/../models/Profile.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../core/Response.php';

class AuthController extends BaseController
{
    public function login(): void
    {
        $input = Request::input();
        $email = trim($input['email'] ?? '');
        $emailLower = strtolower($email);
        $password = (string) ($input['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->renderLogin([
                'error' => 'Email and password are required.',
                'old' => ['email' => $email],
            ]);
            return;
        }

        $userModel = new User();

        // Allow login by email or by student/faculty ID (11 digits)
        $user = null;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = $userModel->findByEmail($email);
        } elseif (preg_match('/^\d{11}$/', $email)) {
            $user = $userModel->findByStudentFacultyId($email);
        }

        if (!$user) {
            $this->renderLogin([
                'error' => 'Invalid credentials.',
                'old' => ['email' => $email],
            ]);
            return;
        }

        if (!password_verify($password, $user['password_hash'])) {
            $this->renderLogin([
                'error' => 'Invalid credentials.',
                'old' => ['email' => $email],
            ]);
            return;
        }

        $profileModel = new Profile();
        $profile = $profileModel->findByUserId($user['id']);
        $fullName = $profile['full_name'] ?? '';

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'full_name' => $fullName,
        ];

        if (in_array($user['role'], ['ADMIN', 'SUPERADMIN'], true)) {
            $log = new AdminLoginLog();
            $log->admin_user_id = $user['id'];
            $log->create();
        }

        $_SESSION['flash'] = ['success' => 'Signed in successfully.'];
        Response::redirect('/IDSystem/');
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        session_start();
        $_SESSION['flash'] = ['success' => 'You have been logged out.'];
        Response::redirect('/IDSystem/');
    }

    public function showRegister(): void
    {
        Response::redirect('/IDSystem/?view=register');
    }

    public function register(): void
    {
        $input = Request::input();

        $fullName = trim($input['full_name'] ?? '');
        $role = strtoupper(trim($input['role'] ?? ''));
        $email = trim($input['email'] ?? '');
        $emailLower = strtolower($email);
        $password = (string) ($input['password'] ?? '');
        $passwordConfirmation = (string) ($input['password_confirmation'] ?? '');
        $studentFacultyId = trim($input['student_faculty_id'] ?? '');
        $department = trim($input['department'] ?? '');
        $year = trim($input['year'] ?? '');
        $courseGradeStrand = trim($input['course_grade_strand'] ?? '');

        $old = [
            'full_name' => $fullName,
            'email' => $email,
            'role' => $role !== '' ? $role : 'STUDENT',
            'student_faculty_id' => $studentFacultyId,
            'department' => $department,
            'year' => $year,
            'course_grade_strand' => $courseGradeStrand,
        ];

        $validRoles = ['STUDENT', 'FACULTY'];

        if (!in_array($role, $validRoles, true)) {
            $this->renderRegister([
                'error' => 'Please select a valid role (Student or Faculty).',
                'old' => $old,
            ]);
            return;
        }

        $requiredFields = [
            'Full name' => $fullName,
            'Email' => $email,
            'Password' => $password,
            'Confirm password' => $passwordConfirmation,
            'ID number' => $studentFacultyId,
            'Department' => $department,
            'Year' => $year,
            'Course / Grade / Strand' => $courseGradeStrand,
        ];

        foreach ($requiredFields as $label => $value) {
            if ($value === '') {
                $this->renderRegister([
                    'error' => $label . ' is required.',
                    'old' => $old,
                ]);
                return;
            }
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($emailLower, '@bec.edu.ph')) {
            $this->renderRegister([
                'error' => 'Please use a valid bec.edu.ph email address.',
                'old' => $old,
            ]);
            return;
        }

        if (!preg_match('/^\d{11}$/', $studentFacultyId)) {
            $this->renderRegister([
                'error' => 'ID number must be exactly 11 digits.',
                'old' => $old,
            ]);
            return;
        }

        if (strlen($password) < 8) {
            $this->renderRegister([
                'error' => 'Password must be at least 8 characters.',
                'old' => $old,
            ]);
            return;
        }

        if ($password !== $passwordConfirmation) {
            $this->renderRegister([
                'error' => 'Password and confirmation do not match.',
                'old' => $old,
            ]);
            return;
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $this->renderRegister([
                'error' => 'An account with this email already exists.',
                'old' => $old,
            ]);
            return;
        }

        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            $user = new User($pdo);
            $user->role = $role;
            $user->email = $email;
            $user->password_hash = password_hash($password, PASSWORD_DEFAULT);

            if (!$user->create()) {
                throw new RuntimeException('Failed to create user record.');
            }

            $profile = new Profile($pdo);
            $profile->user_id = $user->id;
            $profile->full_name = $fullName;
            $profile->student_faculty_id = $studentFacultyId;
            $profile->department = $department;
            $profile->year = $year;
            $profile->course_grade_strand = $courseGradeStrand;

            if (!$profile->create()) {
                throw new RuntimeException('Failed to create profile record.');
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $this->renderRegister([
                'error' => 'Could not complete registration. Please try again.',
                'old' => $old,
            ]);
            return;
        }

        $_SESSION['flash'] = ['success' => 'Account created successfully. Please log in.'];
        Response::redirect('/IDSystem/');
    }

    private function renderLogin(array $params = []): void
    {
        $sessionUser = $_SESSION['user'] ?? null;
        $defaults = [
            'title' => 'BEC ID Appointment Portal',
            'sessionUser' => $sessionUser,
            'error' => $params['error'] ?? null,
            'success' => $params['success'] ?? null,
            'old' => $params['old'] ?? ['email' => ''],
            'viewMode' => 'login',
        ];

        $this->view('home', $defaults);
    }

    private function renderRegister(array $params = []): void
    {
        $sessionUser = $_SESSION['user'] ?? null;
        $defaults = [
            'title' => 'Create Account | BEC ID Appointment Portal',
            'sessionUser' => $sessionUser,
            'error' => $params['error'] ?? null,
            'success' => $params['success'] ?? null,
            'old' => $params['old'] ?? [
                'full_name' => '',
                'email' => '',
                'role' => 'STUDENT',
                'student_faculty_id' => '',
                'department' => '',
                'year' => '',
                'course_grade_strand' => '',
            ],
            'viewMode' => 'register',
        ];

        $this->view('home', $defaults);
    }
}
