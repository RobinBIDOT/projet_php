<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

$description = trim($_POST['description'] ?? '');

if ($description === '') {
    header("Location: ../public/dashboard.php?error=empty");
    exit;
}

$stmt = $pdo->prepare("INSERT INTO tasks (user_id, description) VALUES (?, ?)");
$stmt->execute([$_SESSION['user_id'], $description]);

header("Location: ../public/dashboard.php?success=added");
