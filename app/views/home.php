<?php
$viewMode = isset($viewMode) ? $viewMode : 'login';
$isRegister = $viewMode === 'register';

$sessionUser = $sessionUser ?? null;
$error = $error ?? null;
$success = $success ?? null;
$old = $old ?? [];

$title = $title ?? ($isRegister ? 'Create Account | BEC ID Appointment Portal' : 'BEC ID Appointment Portal');

$oldEmail = htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8');
$oldFullName = htmlspecialchars($old['full_name'] ?? '', ENT_QUOTES, 'UTF-8');
$oldRole = strtoupper((string) ($old['role'] ?? 'STUDENT'));
$oldStudentFacultyId = htmlspecialchars($old['student_faculty_id'] ?? '', ENT_QUOTES, 'UTF-8');
$oldDepartment = htmlspecialchars($old['department'] ?? '', ENT_QUOTES, 'UTF-8');
$oldYear = htmlspecialchars($old['year'] ?? '', ENT_QUOTES, 'UTF-8');
$yearOptions = ['None', 'Irregular', '1st Year', '2nd Year', '3rd Year', '4th Year'];
$oldYearValue = strtolower(trim((string) ($old['year'] ?? '')));
$oldCourseGradeStrand = htmlspecialchars($old['course_grade_strand'] ?? '', ENT_QUOTES, 'UTF-8');
$required = '<span class="req">*</span>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
        <link rel="stylesheet" href="assets/css/home.css" />
</head>

<body>
<div class="toast-stack" aria-live="polite" aria-atomic="true">
    <?php if (!empty($error)): ?>
        <div class="toast toast-error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="toast toast-success" role="status"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
</div>
<div class="container">

    <div class="left-panel">
        <div class="overlay"></div>

        <div class="branding">
            <img src="assets/images/BEC-logo.png" class="logo" alt="BEC Logo" />
            <h1>Batangas Eastern<br>Colleges</h1>
        </div>

        <p class="subtitle">ID Appointment Scheduling Portal</p>

        <p class="description">
            Book appointments online for school ID creation and renewal.<br>
            Fast, convenient, and secure.
        </p>

        <ul class="features">
            <li>Easy online booking</li>
            <li>Real-time appointment tracking</li>
            <li>Secure and reliable</li>
        </ul>
    </div>

    <div class="right-panel">
        <div class="login-box">

            <h2 class="title"><?php echo $isRegister ? 'Create Account' : 'Welcome Back'; ?></h2>
            <p class="info-text"><?php echo $isRegister ? 'Fill in your details to register' : 'Sign in to your account to continue'; ?></p>

            <?php if (!empty($sessionUser)): ?>
                <div class="session-box">
                    <p class="session-text">Signed in as <strong><?php echo htmlspecialchars($sessionUser['email'], ENT_QUOTES, 'UTF-8'); ?></strong> (<?php echo htmlspecialchars($sessionUser['role'], ENT_QUOTES, 'UTF-8'); ?>)</p>
                    <form action="/IDSystem/logout" method="POST">
                        <button type="submit" class="btn-secondary">Logout</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($isRegister): ?>
                <div class="form-scroll">
                    <form id="registerForm" method="POST" action="/IDSystem/register" novalidate>
                        <label class="input-label" for="fullName">Full name <?php echo $required; ?></label>
                        <input type="text" name="full_name" id="fullName" class="input-field" placeholder="Juan D. Cruz" value="<?php echo $oldFullName; ?>" required>

                        <label class="input-label" for="roleSelect">Role <?php echo $required; ?></label>
                        <select name="role" id="roleSelect" class="input-field" required>
                            <option value="STUDENT" <?php echo $oldRole === 'STUDENT' ? 'selected' : ''; ?>>Student</option>
                            <option value="FACULTY" <?php echo $oldRole === 'FACULTY' ? 'selected' : ''; ?>>Faculty</option>
                        </select>

                        <label class="input-label" for="studentFacultyId">Student / Faculty ID number <?php echo $required; ?></label>
                        <input type="text" name="student_faculty_id" id="studentFacultyId" class="input-field" placeholder="e.g., 20252026001" value="<?php echo $oldStudentFacultyId; ?>" required pattern="\d{11}" inputmode="numeric" maxlength="11" title="ID number must be exactly 11 digits" oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11);">

                        <label class="input-label" for="emailInput">Email <?php echo $required; ?></label>
                        <input type="email" name="email" id="emailInput" class="input-field" placeholder="you@bec.edu.ph" value="<?php echo $oldEmail; ?>" required pattern="^[^@\s]+@bec\.edu\.ph$" title="Email must end with bec.edu.ph">

                        <label class="input-label" for="departmentInput">Department <?php echo $required; ?></label>
                        <input type="text" name="department" id="departmentInput" class="input-field" placeholder="e.g., College of Education" value="<?php echo $oldDepartment; ?>" required>

                        <label class="input-label" for="courseInput">Course / Grade / Strand <?php echo $required; ?></label>
                        <input type="text" name="course_grade_strand" id="courseInput" class="input-field" placeholder="e.g., BSIT, SHS-ABM, 7" value="<?php echo $oldCourseGradeStrand; ?>" required>

                        <label class="input-label" for="yearInput">Year <?php echo $required; ?></label>
                        <select name="year" id="yearInput" class="input-field" required>
                            <?php foreach ($yearOptions as $yearOption): ?>
                                <?php $selected = strtolower($yearOption) === $oldYearValue ? 'selected' : ''; ?>
                                <option value="<?php echo htmlspecialchars($yearOption, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($yearOption, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label class="input-label" for="passwordInput">Password <?php echo $required; ?></label>
                        <input type="password" name="password" id="passwordInput" class="input-field" placeholder="At least 8 characters" required>

                        <label class="input-label" for="passwordConfirmInput">Confirm password <?php echo $required; ?></label>
                        <input type="password" name="password_confirmation" id="passwordConfirmInput" class="input-field" placeholder="Re-enter password" required>
 
                        <button type="submit" class="btn-main" id="registerBtn">Create account</button>
                    </form>
                </div>

                <div class="links links-right" id="linksBox">
                    <a href="./" class="register" id="loginLink">Already have an account? <b>Login</b></a>
                </div>
            <?php else: ?>
                <div class="form-scroll">
                    <form id="loginForm" method="POST" action="/IDSystem/login" novalidate>
                        <label class="input-label" id="userLabel">Email or ID Number <?php echo $required; ?></label>
                        <input type="text" name="email" class="input-field" id="userInput" placeholder="yourname@bec.edu.ph or ID Number" value="<?php echo $oldEmail; ?>" required>

                        <label class="input-label" id="passLabel">Password <?php echo $required; ?></label>
                        <input type="password" name="password" class="input-field" id="passInput" placeholder="Password" required>

                        <button type="submit" class="btn-main" id="loginBtn">Login</button>
                    </form>
                </div>

                <div class="links" id="linksBox">
                    <a href="forgot.php" class="forgot">Forgot password?</a>
                    <a href="./?view=register" class="register" id="registerLink">Don't have an account? <b>Register</b></a>
                </div>
            <?php endif; ?>

            <p class="support">
                Need help? Contact IT Support at
                <a href="mailto:itsupport@bec.edu.ph" class="support-email">itsupport@bec.edu.ph</a>
            </p>

        </div>
    </div>

</div>

</body>
</html>
<script>
(() => {
    const toasts = Array.from(document.querySelectorAll('.toast'));
    toasts.forEach((toast) => {
        requestAnimationFrame(() => toast.classList.add('show'));
        setTimeout(() => toast.classList.remove('show'), 4200);
        setTimeout(() => toast.remove(), 4700);
    });
})();
</script>
