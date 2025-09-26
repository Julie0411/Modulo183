<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    if ($username !== '') {
        $username = substr($username, 0, 50);
        setcookie('minisocial_username', $username, [
            'expires' => time() + 60*60*24*30,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        header('Location: feed.php');
        exit;
    } else {
        $error = 'Inserisci uno username valido.';
    }
}
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - MiniSocial</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-title mb-3">Accedi a MiniSocial</h3>
            <?php if (!empty($error)): ?>
              <div class="alert alert-danger"><?=htmlspecialchars($error)?></div>
            <?php endif; ?>
            <form method="post">
              <div class="mb-3">
                <label for="username" class="form-label">Nome utente</label>
                <input type="text" class="form-control" id="username" name="username" maxlength="50" required>
              </div>
              <button type="submit" class="btn btn-primary">Entra</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
