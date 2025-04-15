<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Récupération des étudiants
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
            <th style="width: 200px">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['username']) ?></td>
                <td><?= $student['created_at'] ?></td>
                <td>
                    <form method="POST" action="../actions/edit_user.php" class="d-inline">
                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                        <input type="text" name="username" value="<?= htmlspecialchars($student['username']) ?>" class="form-control form-control-sm d-inline-block w-auto" required>
                        <button type="submit" class="btn btn-warning btn-sm">✏️ Update</button>
                    </form>

                    <form method="POST" action="../actions/delete_user.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
