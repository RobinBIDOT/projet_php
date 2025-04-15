<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit($id)) {
    header("Location: dashboard.php");
    exit;
}

// Récupération de la tâche
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$task = $stmt->fetch();

if (!$task) {
    header("Location: dashboard.php");
    exit;
}
?>

<div class="container mt-5">
    <h2>✏️ Edit Task</h2>

    <form method="POST" action="../actions/update_task.php">
        <input type="hidden" name="id" value="<?= $task['id'] ?>">
        <div class="mb-3">
            <label class="form-label">Task Description</label>
            <input type="text" name="description" value="<?= htmlspecialchars($task['description']) ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
