<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? ''); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($title ?? ''); ?></h1>
    <p>Routing is working. Add more routes in app/routes/web.php.</p>
</body>
</html>
