<?php
session_start();
require_once '../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../public/dashboard.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username !== '' && $password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
    $stmt->execute([$username, $hash]);
}

header('Location: ../public/manage_users.php');
