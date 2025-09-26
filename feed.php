<?php
require_once 'db.php';

$username = isset($_COOKIE['minisocial_username']) ? $_COOKIE['minisocial_username'] : null;
if (!$username) {
    header('Location: index.php');
    exit;
}

$mysqli = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim(isset($_POST['content']) ? $_POST['content'] : '');
    if ($content !== '') {
        $stmt = $mysqli->prepare('INSERT INTO Post (user_name, post_comment) VALUES (?, ?)');
        $stmt->bind_param('ss', $username, $content);
        $stmt->execute();
        $stmt->close();
        header('Location: feed.php');
        exit;
    } else {
        $error = 'Il post non può essere vuoto.';
    }
}

$result = $mysqli->query('SELECT post_id, user_name, post_comment FROM Post ORDER BY post_id DESC');
$posts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    $result->free();
}
$mysqli->close();
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
        <div class="alert alert-info">Nessun post ancora.</div>
    <?php else: ?>
        <?php foreach ($posts as $p): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">
                        <?=htmlspecialchars($p['user_name'])?> (ID #<?=$p['post_id']?>)
                    </h6>
                    <p class="card-text"><?=nl2br(htmlspecialchars($p['post_comment']))?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
