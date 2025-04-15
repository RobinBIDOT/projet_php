<?php
require_once '../config/db.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header('Location: ../public/register.php?error=missing');
    exit;
}

if (strlen($password) < 6) {
    header('Location: ../public/register.php?error=weak');
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashedPassword]);
    header('Location: ../public/index.php?registered=1');
} catch (PDOException $e) {
    header('Location: ../public/register.php?error=exists');
}
