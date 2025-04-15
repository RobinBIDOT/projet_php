<?php
session_start();
require_once '../config/db.php';

$id = $_GET['id'] ?? null;

if (isset($_SESSION['user_id']) && $id) {
    $stmt = $pdo->prepare("UPDATE tasks SET deleted = TRUE WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

header("Location: ../public/dashboard.php");
