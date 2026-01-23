<?php
$sessionUser = $sessionUser ?? null;
$fullName = htmlspecialchars($fullName ?? '', ENT_QUOTES, 'UTF-8');
$role = htmlspecialchars($role ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($sessionUser['email'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($title ?? 'Dashboard', ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
    <main>
        <h1>Hello, <?php echo $fullName !== '' ? $fullName : $email; ?>!</h1>
        <p>Your role: <?php echo $role !== '' ? $role : 'Unknown'; ?></p>
        <form action="/IDSystem/logout" method="POST">
            <button type="submit">Logout</button>
        </form>
    </main>
</body>
</html>
