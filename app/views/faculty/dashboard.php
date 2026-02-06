<?php
$sessionUser = $sessionUser ?? null;
$fullName = htmlspecialchars($fullName ?? '', ENT_QUOTES, 'UTF-8');
$role = htmlspecialchars($role ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($sessionUser['email'] ?? '', ENT_QUOTES, 'UTF-8');
$appointments = $appointments ?? [];
$active = $active ?? 'book';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require __DIR__ . '/../head.php'; ?>
    <title><?php echo htmlspecialchars($title ?? 'Faculty Dashboard', ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
    <?php require __DIR__ . '/../role_sidebar.php'; ?>
    <main class="ml-[340px] p-4">
        <h1>Hello, <?php echo $fullName !== '' ? $fullName : $email; ?>!</h1>
        <p>Your role: <?php echo $role !== '' ? $role : 'Unknown'; ?></p>

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="flash">
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <section id="book">
            <h2>Book Appointment</h2>
            <form action="/IDSystem/appointments" method="POST">
                <div>
                    <label for="reason">Reason</label>
                    <input id="reason" name="reason" required />
                </div>
                <div>
                    <label for="scheduled_at">Scheduled at</label>
                    <input id="scheduled_at" name="scheduled_at" type="datetime-local" />
                </div>
                <div>
                    <button type="submit">Create Appointment</button>
                </div>
            </form>
        </section>

        <section id="schedules">
            <h2>My Schedules</h2>
            <?php if (empty($appointments)): ?>
                <p>No appointments yet.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($appointments as $a): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($a['reason'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            — <?php echo htmlspecialchars($a['scheduled_at'] ?? 'Not set', ENT_QUOTES, 'UTF-8'); ?>
                            (<?php echo htmlspecialchars($a['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section id="profile">
            <h2>Profile</h2>
            <p>Email: <?php echo $email; ?></p>
        </section>

        <form action="/IDSystem/logout" method="POST">
            <button type="submit">Logout</button>
        </form>
    </main>
</body>
</html>
