<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/index.php');
    exit;
}

$id = $_POST['id'] ?? null;
$description = trim($_POST['description'] ?? '');

if ($id && $description !== '') {
    $stmt = $pdo->prepare("UPDATE tasks SET description = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
    $stmt->execute([$description, $id, $_SESSION['user_id']]);
}

header('Location: ../public/dashboard.php');
