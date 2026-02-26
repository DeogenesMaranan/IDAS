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
    <?php Response::partial('components/head'); ?>
    <title><?php echo htmlspecialchars($title ?? 'BEC Portal', ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        .hidden { display: none; }
    </style>
</head>
<body>
    <?php Response::partial('components/role_header', ['fullName' => $fullName, 'sessionUser' => $sessionUser, 'role' => $role]); ?>
    <?php Response::partial('components/role_sidebar', ['role' => $role, 'active' => $active]); ?>

    <main class="ml-[340px] px-8" id="spa-root">
        <div id="spa-content">

            <?php if (in_array($role, ['STUDENT', 'FACULTY'], true)): ?>
                <?php Response::partial('partials/user', ['active' => $active, 'sessionUser' => $sessionUser]); ?>
            <?php elseif (in_array($role, ['ADMIN', 'SUPERADMIN'], true)): ?>
                <?php Response::partial('partials/admin', ['active' => $active, 'role' => $role, 'sessionUser' => $sessionUser]); ?>
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
