<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit($id)) {
    header("Location: ../public/dashboard.php?error=invalid");
    exit;
}

// On vérifie que la tâche appartient à l'utilisateur
$stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

header("Location: ../public/dashboard.php?success=deleted");
