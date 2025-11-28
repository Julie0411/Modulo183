<?php
require_once 'db.php';
session_start();

$mysqli = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');

    if ($username !== '' && $password !== '') {

        $password_hash = hash('sha256', $password);

        $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Username già in uso.';
        } else {

            $stmt = $mysqli->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
            $stmt->bind_param('ss', $username, $password_hash);
            $stmt->execute();

            header('Location: index.php');
            exit;
        }

        $stmt->close();
    } else {
        $error = 'Compila tutti i campi.';
    }
}
?>

<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Registrazione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h3>Registrati</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label>Username</label>
            <input class="form-control" name="username" required maxlength="50">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input class="form-control" name="password" required type="password">
        </div>

        <button class="btn btn-primary" href="feed.php">Registrati</button>
    </form>

    <a href="index.php" class="btn btn-link">Vai al login</a>
</div>
</body>
</html>
