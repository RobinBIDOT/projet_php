<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$description = trim($_POST['description'] ?? '');
$category = trim($_POST['category'] ?? '');

// Vérification du fichier
if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../public/documents.php?error=upload");
    exit;
}

$allowedTypes = [
    'image/jpeg', 'image/png', 'image/gif',
    'application/pdf', 'text/plain'
];

$file = $_FILES['document'];
$originalName = basename($file['name']);
$mimeType = mime_content_type($file['tmp_name']);

if (!in_array($mimeType, $allowedTypes)) {
    header("Location: ../public/documents.php?error=type");
    exit;
}

// Créer le dossier personnel
$uploadDir = "../uploads/$userId/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Nom unique
$extension = pathinfo($originalName, PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $extension;
$destination = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    $stmt = $pdo->prepare("INSERT INTO documents (user_id, filename, original_name, mime_type, description, category) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $filename, $originalName, $mimeType, $description, $category]);
}

header("Location: ../public/documents.php");
exit;
