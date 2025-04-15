<?php
require_once '../config/db.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$count = $stmt->fetchColumn();

echo "Utilisateurs dans la base : $count";
