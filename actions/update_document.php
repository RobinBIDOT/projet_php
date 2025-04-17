<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$id = $_POST['id'] ?? null;
$original_name = trim($_POST['original_name'] ?? '');
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');

if ($id && $original_name !== '' && ctype_digit($id)) {
    $stmt = $pdo->prepare("UPDATE documents SET original_name = ?, description = ?, category = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$original_name, $description, $category, $id, $userId]);
}

header("Location: ../public/documents.php");
exit;
