<?php
require 'db.php';

$mysqli = db_connect();

$username = 'User1';
$password = '123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hash);
$stmt->execute();

echo "utente creato!"
?>
