<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->query("
  SELECT t.*, u.username 
  FROM tasks t 
  JOIN users u ON t.user_id = u.id 
  ORDER BY t.created_at DESC
");
$tasks = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2>All Users' Tasks</h2>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>User</th>
            <th>Task</th>
            <th>Created At</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['username']) ?></td>
                <td><?= htmlspecialchars($task['description']) ?></td>
                <td><?= $task['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
