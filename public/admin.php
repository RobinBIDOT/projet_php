<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Recherche et pagination
$userFilter = trim($_GET['user'] ?? '');
$taskFilter = trim($_GET['task'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];

if ($userFilter !== '') {
    $whereClauses[] = "u.username LIKE ?";
    $params[] = "%$userFilter%";
}
if ($taskFilter !== '') {
    $whereClauses[] = "t.description LIKE ?";
    $params[] = "%$taskFilter%";
}

$whereSQL = '';
if (count($whereClauses) > 0) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Compte total
$countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM tasks t
    JOIN users u ON t.user_id = u.id
    $whereSQL
");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

// Tâches paginées
$stmt = $pdo->prepare("
    SELECT t.*, u.username 
    FROM tasks t
    JOIN users u ON t.user_id = u.id
    $whereSQL
    ORDER BY u.username ASC, t.created_at DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$tasks = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2>🛠 Admin Panel – All Users' Tasks</h2>

    <!-- Filtres -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="user" value="<?= htmlspecialchars($userFilter) ?>" class="form-control" placeholder="Search by username...">
        </div>
        <div class="col-md-5">
            <input type="text" name="task" value="<?= htmlspecialchars($taskFilter) ?>" class="form-control" placeholder="Search by task content...">
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-primary">🔍 Filter</button>
        </div>
    </form>

    <!-- Tableau -->
    <table class="table table-bordered table-hover">
        <thead class="table-light">
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

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= ($p === $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $p ?>&user=<?= urlencode($userFilter) ?>&task=<?= urlencode($taskFilter) ?>">
                            <?= $p ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <hr class="my-4">

    <!-- Gestion des utilisateurs -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>👥 Manage Students</h4>
        <a href="manage_users.php" class="btn btn-outline-success">➕ Add New Student</a>
    </div>

    <!-- Liste à suivre : gérée dans manage_users.php -->
</div>

<?php include '../includes/footer.php'; ?>
