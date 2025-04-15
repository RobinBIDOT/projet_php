<?php
session_start();
require_once '../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../public/dashboard.php');
    exit;
}

$id = $_POST['id'] ?? null;

if ($id && ctype_digit($id)) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
    $stmt->execute([$id]);
}

header('Location: ../public/manage_users.php');
