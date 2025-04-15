<?php
session_start();
require_once '../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../public/dashboard.php');
    exit;
}

$id = $_POST['id'] ?? null;
$username = trim($_POST['username'] ?? '');

if ($id && $username !== '') {
    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ? AND role = 'user'");
    $stmt->execute([$username, $id]);
}

header('Location: ../public/manage_users.php');
