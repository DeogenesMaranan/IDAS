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

            <?php if (in_array($role, ['STUDENT', 'FACULTY'], true)): ?>
                <?php require __DIR__ . '/partials/user.php'; ?>
            <?php elseif (in_array($role, ['ADMIN', 'SUPERADMIN'], true)): ?>
                <?php require __DIR__ . '/partials/admin.php'; ?>
            <?php else: ?>
                <section id="home" class="spa-section <?php echo $active === 'home' ? '' : 'hidden'; ?>">
                    <h1 class="text-3xl font-bold mb-4">Home</h1>
                    <p>Welcome to the portal.</p>
                </section>
            <?php endif; ?>

        </div>
    </main>

    <script>
        window.__INITIAL_SPA_PAGE = '<?php echo addslashes($active); ?>';
    </script>
    <script src="/IDSystem/public/assets/js/spa.js"></script>
</body>
</html>
