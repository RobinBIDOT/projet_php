<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id && ctype_digit($id)) {
    $stmt = $pdo->prepare("UPDATE tasks SET is_done = TRUE, updated_at = NOW() WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

header("Location: ../public/dashboard.php");
exit;
