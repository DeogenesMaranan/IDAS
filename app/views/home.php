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
$oldYearValue = strtolower(trim((string) ($old['year'] ?? '')));
$oldCourseGradeStrand = htmlspecialchars($old['course_grade_strand'] ?? '', ENT_QUOTES, 'UTF-8');
$yearOptions = ['None', 'Irregular', '1st Year', '2nd Year', '3rd Year', '4th Year'];
$required = '<span class="text-red-600 font-bold">*</span>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require __DIR__ . '/head.php'; ?>
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
</head>

<body class="antialiased bg-gray-50 overflow-hidden text-xl">

<?php require __DIR__ . '/components/toast.php'; ?>

<div class="h-screen lg:grid lg:grid-cols-2">

    <!-- LEFT PANEL -->
    <div class="relative bg-gradient-to-b from-red-900 to-red-800 text-white p-12 flex items-center"
         style="background-image:url('/IDSystem/public/assets/images/bg-image.jpg'); background-size:cover; background-position:center;">
        <div class="absolute inset-0 bg-red-900/60 backdrop-blur-sm"></div>

        <div class="relative z-10 max-w-lg">
            <div class="flex items-center gap-5">
                <img src="/IDSystem/public/assets/images/BEC-logo.png" class="w-20 h-20 rounded-full bg-white p-1">
                <div>
                    <h1 class="text-4xl font-bold leading-tight">
                        Batangas Eastern<br>Colleges
                    </h1>
                    <p class="text-lg opacity-90">
                        ID Appointment Scheduling Portal
                    </p>
                </div>
            </div>

            <p class="mt-6 text-lg">
                Book appointments online for school ID creation and renewal.
                Fast, convenient, and secure.
            </p>

            <ul class="mt-4 space-y-2 text-lg">
                <li>Easy online booking</li>
                <li>Real-time appointment tracking</li>
                <li>Secure and reliable</li>
            </ul>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="flex flex-col items-center justify-center p-10 gap-5">

        <div class="w-full max-w-xl h-[600px] bg-white rounded-2xl shadow-xl flex flex-col text-lg">

            <!-- HEADER -->
            <div class="p-8 border-b">
                <h2 class="flex justify-center text-5xl font-bold text-gray-800">
                    <?php echo $isRegister ? 'Create Account' : 'Welcome Back'; ?>
                </h2>
                <p class="flex justify-center text-lg text-gray-500 mt-2">
                    <?php echo $isRegister ? 'Fill in your details to register' : 'Sign in to your account to continue'; ?>
                </p>
            </div>

            <!-- FORM -->
            <div class="flex-1 overflow-y-auto px-8 py-6">
                <?php if ($isRegister): ?>
                <form id="registerForm" method="POST" action="/IDSystem/register" class="space-y-5">

                    <div>
                        <label class="text-lg font-medium">Full Name <?php echo $required; ?></label>
                        <input type="text" name="full_name" value="<?php echo $oldFullName; ?>" required
                               placeholder="e.g. Juan Dela Cruz"
                               class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                    </div>

                    <div>
                        <label class="text-lg font-medium">Role <?php echo $required; ?></label>
                        <select name="role"
                                class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                            <option value="STUDENT" <?php echo $oldRole === 'STUDENT' ? 'selected' : ''; ?>>Student</option>
                            <option value="FACULTY" <?php echo $oldRole === 'FACULTY' ? 'selected' : ''; ?>>Faculty</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-lg font-medium">Student / Faculty ID <?php echo $required; ?></label>
                        <input type="text" name="student_faculty_id" value="<?php echo $oldStudentFacultyId; ?>" required
                               maxlength="11" placeholder="e.g. 201800123"
                               class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                    </div>

                    <div>
                        <label class="text-lg font-medium">Email <?php echo $required; ?></label>
                        <input type="email" name="email" value="<?php echo $oldEmail; ?>" required
                               placeholder="name@bec.edu.ph"
                               class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                    </div>

                    <div>
                        <label class="text-lg font-medium">Department <?php echo $required; ?></label>
                        <input type="text" name="department" value="<?php echo $oldDepartment; ?>" required
                               placeholder="e.g. Computer Science"
                               class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                    </div>

                    <div>
                        <label class="text-lg font-medium">Course / Grade / Strand <?php echo $required; ?></label>
                        <input type="text" name="course_grade_strand" value="<?php echo $oldCourseGradeStrand; ?>" required
                               placeholder="e.g. BSCS / 3rd Year"
                               class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                    </div>

                    <div>
                        <label class="text-lg font-medium">Year <?php echo $required; ?></label>
                        <select name="year"
                                class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                            <?php foreach ($yearOptions as $year): ?>
                                <option value="<?php echo $year; ?>" <?php echo strtolower($year) === $oldYearValue ? 'selected' : ''; ?>>
                                    <?php echo $year; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="text-lg font-medium">Password <?php echo $required; ?></label>
                        <input type="password" name="password" required
                               placeholder="Choose a strong password"
                               class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                    </div>

                    <div>
                        <label class="text-lg font-medium">Confirm Password <?php echo $required; ?></label>
                        <input type="password" name="password_confirmation" required
                               placeholder="Repeat your password"
                               class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                    </div>
                </form>
                <?php else: ?>
                <div class="h-full flex items-center justify-center">
                    <form id="loginForm" method="POST" action="/IDSystem/login" class="space-y-5 w-full">
                        <div>
                            <label class="text-lg font-medium">Email or ID Number <?php echo $required; ?></label>
                            <input type="text" name="email" value="<?php echo $oldEmail; ?>" required
                                placeholder="Email or ID Number"
                                class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                        </div>

                        <div>
                            <label class="text-lg font-medium">Password <?php echo $required; ?></label>
                            <input type="password" name="password" required
                                placeholder="Your password"
                                class="w-full border rounded-xl px-4 py-3 text-lg focus:outline-none focus:ring-4 focus:ring-red-200 focus:border-red-600">
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>

            <!-- FOOTER -->
            <div class="p-8 border-t">
                <button form="<?php echo $isRegister ? 'registerForm' : 'loginForm'; ?>"
                        class="w-full bg-red-600 text-white py-4 rounded-xl text-xl hover:bg-red-700 transition">
                    <?php echo $isRegister ? 'Create Account' : 'Login'; ?>
                </button>

                <div class="mt-5 text-lg text-center">
                    <?php if ($isRegister): ?>
                        <div class="flex justify-end">
                            <a href="./?view=login" class="text-orange-600 font-medium switch-view">
                                Already have an account? Login
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="flex justify-between">
                            <a href="forgot.php" class="text-gray-500">Forgot password?</a>
                            <a href="./?view=register" class="text-orange-600 font-medium switch-view">
                                Don’t Have an Account? Register
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <p class="text-base text-gray-500 text-center max-w-xl">
            Need help? Contact IT Support at
            <a href="mailto:itsupport@bec.edu.ph" class="text-red-600 font-medium hover:underline">
                itsupport@bec.edu.ph
            </a>
        </p>
    </div>
</div>

<script>
(() => {
    // Toast animation
    document.querySelectorAll('.toast').forEach(t => {
        t.classList.add('opacity-100', 'translate-y-0');
        setTimeout(() => t.classList.remove('opacity-100', 'translate-y-0'), 4200);
        setTimeout(() => t.remove(), 4700);
    });

    // Remove query string after page load
    if (window.location.search.includes('view=')) {
        const url = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, url);
    }

    // Optional: Smooth switch view without page reload
    document.querySelectorAll('.switch-view').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const href = link.getAttribute('href');
            window.location.href = href; // load page, then query will disappear
        });
    });
})();
</script>

</body>
</html>
