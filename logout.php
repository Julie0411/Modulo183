<?php
setcookie('minisocial_username', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);
header('Location: index.php');
exit;
