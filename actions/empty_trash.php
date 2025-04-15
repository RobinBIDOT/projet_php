<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE user_id = ? AND deleted = TRUE");
    $stmt->execute([$_SESSION['user_id']]);
}

header("Location: ../public/trash.php");
