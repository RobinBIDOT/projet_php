<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY role DESC, username ASC");
$users = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2>📋 Admin – Users Overview</h2>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
        <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Access</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= $user['role'] ?></td>
                <td><?= $user['created_at'] ?></td>
                <td>
                    <?php if ($user['role'] === 'user'): ?>
                        <a href="user_documents.php?id=<?= $user['id'] ?>" class="btn btn-outline-secondary btn-sm">📁 Docs</a>
                        <a href="admin.php?user=<?= urlencode($user['username']) ?>" class="btn btn-outline-primary btn-sm">📝 Tasks</a>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
