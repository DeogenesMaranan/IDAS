<?php
$sessionUser = $sessionUser ?? null;
$fullName = htmlspecialchars($fullName ?? '', ENT_QUOTES, 'UTF-8');
$role = strtoupper($role ?? ($sessionUser['role'] ?? ''));
$email = htmlspecialchars($sessionUser['email'] ?? '', ENT_QUOTES, 'UTF-8');
$active = $active ?? ($role === 'STUDENT' || $role === 'FACULTY' ? 'book' : 'dashboard');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require __DIR__ . '/head.php'; ?>
    <title><?php echo htmlspecialchars($title ?? 'BEC Portal', ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        .hidden { display: none; }
    </style>
</head>
<body>
    <?php require __DIR__ . '/role_sidebar.php'; ?>

    <main class="ml-[340px] p-6" id="spa-root">
        <div id="spa-content">

            <!-- Book (student/faculty) -->
            <section id="book" class="spa-section <?php echo $active === 'book' ? '' : 'hidden'; ?>">
                <h1 class="text-3xl font-bold mb-4">Book Appointment</h1>
                <form action="/IDSystem/appointments" method="POST">
                    <div class="mb-3">
                        <label for="reason">Reason</label>
                        <input id="reason" name="reason" required class="w-full border rounded px-3 py-2" />
                    </div>
                    <div class="mb-3">
                        <label for="scheduled_at">Scheduled at</label>
                        <input id="scheduled_at" name="scheduled_at" type="datetime-local" class="w-full border rounded px-3 py-2" />
                    </div>
                    <div>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Create Appointment</button>
                    </div>
                </form>
            </section>

            <!-- Schedules -->
            <section id="schedules" class="spa-section <?php echo $active === 'schedules' ? '' : 'hidden'; ?>">
                <h1 class="text-3xl font-bold mb-4">My Schedules</h1>
                <p>Prototype schedule list will appear here.</p>
            </section>

            <!-- Profile -->
            <section id="profile" class="spa-section <?php echo $active === 'profile' ? '' : 'hidden'; ?>">
                <h1 class="text-3xl font-bold mb-4">Profile</h1>
                <p><strong>Name:</strong> <?php echo $fullName !== '' ? $fullName : $email; ?></p>
                <p><strong>Email:</strong> <?php echo $email; ?></p>
            </section>

            <!-- Admin: Dashboard -->
            <section id="dashboard" class="spa-section <?php echo $active === 'dashboard' ? '' : 'hidden'; ?>">
                <h1 class="text-3xl font-bold mb-4">Dashboard</h1>
                <p>Overview and quick stats (prototype).</p>
            </section>

            <!-- Admin: Appointments -->
            <section id="appointments" class="spa-section <?php echo $active === 'appointments' ? '' : 'hidden'; ?>">
                <h1 class="text-3xl font-bold mb-4">Appointments</h1>
                <p>Manage appointment requests (prototype).</p>
            </section>

            <!-- Admin: Settings -->
            <section id="settings" class="spa-section <?php echo $active === 'settings' ? '' : 'hidden'; ?>">
                <h1 class="text-3xl font-bold mb-4">Settings</h1>
                <p>Application settings (prototype).</p>
            </section>

            <!-- Superadmin: Manage Users -->
            <section id="manage_users" class="spa-section <?php echo $active === 'manage_users' ? '' : 'hidden'; ?>">
                <h1 class="text-3xl font-bold mb-4">Manage Users</h1>
                <p>Create, edit, or remove users (prototype).</p>
            </section>

        </div>
    </main>

    <script>
        window.__INITIAL_SPA_PAGE = '<?php echo addslashes($active); ?>';
    </script>
    <script src="/IDSystem/public/assets/js/spa.js"></script>
</body>
</html>
