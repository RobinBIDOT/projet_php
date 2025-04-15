<?php
require_once '../config/db.php';
include '../includes/header.php';

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? AND deleted = TRUE ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$tasks = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2>🗑 Trash</h2>
    <a href="dashboard.php" class="btn btn-link">← Back to dashboard</a>
    <a href="../actions/empty_trash.php" class="btn btn-danger btn-sm float-end">🧹 Empty Trash</a>

    <ul class="list-group mt-3">
        <?php foreach ($tasks as $task): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($task['description']) ?>
                <a href="../actions/restore_task.php?id=<?= $task['id'] ?>" class="btn btn-outline-primary btn-sm">Restore</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include '../includes/footer.php'; ?>
