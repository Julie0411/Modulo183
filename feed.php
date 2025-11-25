<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$mysqli = db_connect();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $delete_id = intval($_POST['delete_post_id']);

    $stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();

    header("Location: feed.php"); // reload per evitare doppio submit
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['content'])) {
    $content = trim($_POST['content']);
    $stmt = $mysqli->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
    if (!$stmt) {
        die("Prepare fallito: " . $mysqli->error);
    }
    $stmt->bind_param("is", $user_id, $content);
    if (!$stmt->execute()) {
        $error = "Errore nel salvare il post: " . $stmt->error;
    } else {
        header("Location: feed.php");
        exit;
    }
}

$posts = [];
$result = $mysqli->query("
    SELECT posts.id AS post_id, posts.content AS post_comment, posts.user_id, users.username AS user_name
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.id DESC
");

if ($result) {
    $posts = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feed - MiniSocial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h3>Ciao, <?=htmlspecialchars($username)?> 👋</h3>
    <a href="logout.php" class="btn btn-sm btn-outline-secondary mb-3">Logout</a>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Crea un nuovo post</h5>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="3" required></textarea>
                </div>
                <button class="btn btn-primary">Pubblica</button>
            </form>
        </div>
    </div>

    <h5>Feed</h5>
    <?php if (empty($posts)): ?>
        <div class="alert alert-info">Ancora nessun post.</div>
    <?php else: ?>
        <?php foreach ($posts as $p): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        <?=htmlspecialchars($p['user_name'])?> (ID #<?=$p['post_id']?>)
                    </h6>
                    <p class="card-text"><?=nl2br(htmlspecialchars($p['post_comment']))?></p>

                    <?php if ($p['user_id'] == $user_id): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_post_id" value="<?= $p['post_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
