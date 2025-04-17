<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY username ASC");
$students = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2>👥 Manage Students</h2>

    <form action="../actions/add_user.php" method="POST" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="username" class="form-control" placeholder="New student username" required>
        </div>
        <div class="col-md-5">
            <input type="password" name="password" class="form-control" placeholder="Temporary password" required>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-success">➕ Add</button>
        </div>
    </form>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
        <tr>
            <th>Username</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['username']) ?></td>
                <td><?= $student['created_at'] ?></td>
                <td>
                    <a href="user_documents.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-outline-info">📁 Docs</a>

                    <form method="POST" action="../actions/edit_user.php" class="d-inline">
                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                        <input type="text" name="username" value="<?= htmlspecialchars($student['username']) ?>" class="form-control form-control-sm d-inline-block w-auto" required>
                        <button type="submit" class="btn btn-warning btn-sm">✏️</button>
                    </form>

                    <form method="POST" action="../actions/delete_user.php" class="d-inline" onsubmit="return confirm('Delete this user?');">
                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">🗑</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
