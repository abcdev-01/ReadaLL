<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ReadAll — Members</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; }
        table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
        th, td { border: 1px solid #ccc; padding: .5rem .75rem; text-align: left; }
        th { background: #f5f5f5; }
        .error { color: #b00020; font-size: .9rem; }
        form { max-width: 420px; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input { width: 100%; padding: .4rem; }
        button { margin-top: 1rem; padding: .5rem 1rem; }
    </style>
</head>
<body>
    <h1>ReadAll — Members</h1>
    <p>
        <a href="/index.php?route=members">All members</a> |
        <a href="/index.php?route=members/create">Add member</a>
    </p>
    <?php require __DIR__ . '/' . $view . '.php'; ?>
</body>
</html>
