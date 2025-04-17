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

$whereSQL = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Compte total
$countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM tasks t
    JOIN users u ON t.user_id = u.id
    $whereSQL
");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

// Requête paginée
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

// Paramètres pour liens de pagination
$linkQuery = '&user=' . urlencode($userFilter) . '&task=' . urlencode($taskFilter);
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

    <!-- Tableau des tâches -->
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

    <!-- Pagination intelligente -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php
                $visiblePages = 2;

                // Affiche première page
                if ($page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1' . $linkQuery . '">1</a></li>';
                    if ($page > $visiblePages + 2) {
                        echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    }
                }

                // Affiche les pages autour
                for ($i = max(2, $page - $visiblePages); $i <= min($totalPages - 1, $page + $visiblePages); $i++) {
                    echo '<li class="page-item ' . ($i === $page ? 'active' : '') . '">
                            <a class="page-link" href="?page=' . $i . $linkQuery . '">' . $i . '</a>
                          </li>';
                }

                // Affiche dernière page
                if ($page < $totalPages - $visiblePages - 1) {
                    if ($page + $visiblePages < $totalPages - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . $linkQuery . '">' . $totalPages . '</a></li>';
                }

                // Active page 1/last si nécessaire
                if ($page === 1) {
                    echo '<li class="page-item active"><span class="page-link">1</span></li>';
                }
                if ($page === $totalPages && $totalPages > 1) {
                    echo '<li class="page-item active"><span class="page-link">' . $totalPages . '</span></li>';
                }
                ?>
            </ul>
        </nav>
    <?php endif; ?>

    <hr class="my-4">

    <!-- Lien gestion étudiants -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>👥 Manage Students</h4>
        <a href="manage_users.php" class="btn btn-outline-success">➕ Add New Student</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
