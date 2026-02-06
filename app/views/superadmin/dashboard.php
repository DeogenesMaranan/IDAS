<?php
$sessionUser = $sessionUser ?? null;
$fullName = htmlspecialchars($fullName ?? '', ENT_QUOTES, 'UTF-8');
$role = htmlspecialchars($role ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($sessionUser['email'] ?? '', ENT_QUOTES, 'UTF-8');
$active = $active ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require __DIR__ . '/../head.php'; ?>
    <title><?php echo htmlspecialchars($title ?? 'Super Admin Dashboard', ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
    <?php require __DIR__ . '/../role_sidebar.php'; ?>
    <main class="ml-[340px] p-4">
        <h1>Super Admin</h1>
        <p>Welcome, <?php echo $fullName !== '' ? $fullName : $email; ?></p>

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="flash">
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <section id="dashboard">
            <h2>Overview</h2>
            <p>Super admin overview.</p>
        </section>

        <section id="appointments">
            <h2>Appointments</h2>
            <p>Manage appointments.</p>
        </section>

        <section id="settings">
            <h2>Settings</h2>
            <p>Application settings.</p>
        </section>

        <section id="manage_users">
            <h2>Manage Users</h2>
            <p>Create, edit, or remove user accounts.</p>
        </section>

        <form action="/IDSystem/logout" method="POST">
            <button type="submit">Logout</button>
        </form>
    </main>
</body>
</html>
