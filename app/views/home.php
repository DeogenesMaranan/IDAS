<?php
$title = $title ?? 'BEC ID Appointment Portal';
$sessionUser = $sessionUser ?? null;
$error = $error ?? null;
$success = $success ?? null;
$old = $old ?? ['email' => ''];

$oldEmail = htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8');
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

            <h2 class="title">Welcome Back</h2>
            <p class="info-text">Sign in to your account to continue</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if (!empty($sessionUser)): ?>
                <div class="session-box">
                    <p class="session-text">Signed in as <strong><?php echo htmlspecialchars($sessionUser['email'], ENT_QUOTES, 'UTF-8'); ?></strong> (<?php echo htmlspecialchars($sessionUser['role'], ENT_QUOTES, 'UTF-8'); ?>)</p>
                    <form action="/logout" method="POST">
                        <button type="submit" class="btn-secondary">Logout</button>
                    </form>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="/login" novalidate>
                <label class="input-label" id="userLabel">Email or ID Number</label>
                <input type="text" name="email" class="input-field" id="userInput" placeholder="yourname@bec.edu.ph or ID Number" value="<?php echo $oldEmail; ?>" required>

                <label class="input-label" id="passLabel">Password</label>
                <input type="password" name="password" class="input-field" id="passInput" placeholder="Password" required>

                <button type="submit" class="btn-main" id="loginBtn">Login</button>
            </form>

            <div class="links" id="linksBox">
                <a href="/forgot.php" class="forgot">Forgot password?</a>
                <a href="/register_student.php" class="register" id="registerLink">Don't have an account? <b>Register</b></a>
            </div>

            <p class="support">
                Need help? Contact IT Support at
                <a href="mailto:itsupport@bec.edu.ph" class="support-email">itsupport@bec.edu.ph</a>
            </p>

        </div>
    </div>

</div>

</body>
</html>
