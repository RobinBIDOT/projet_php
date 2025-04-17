<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if ($id && ctype_digit($id)) {
    // Récupère le fichier à supprimer
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    $doc = $stmt->fetch();

    if ($doc) {
        $file = "../uploads/$userId/{$doc['filename']}";
        if (file_exists($file)) {
            unlink($file); // Supprimer physiquement
        }
        $delStmt = $pdo->prepare("DELETE FROM documents WHERE id = ? AND user_id = ?");
        $delStmt->execute([$id, $userId]);
    }
}

header("Location: ../public/documents.php");
exit;
