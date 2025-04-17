<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Recherche & pagination
$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$searchQuery = '';
$params = [$userId];

if ($search !== '') {
    $searchQuery = "AND description LIKE ?";
    $params[] = "%$search%";
}

// Nombre total de tâches filtrées
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND deleted = FALSE $searchQuery");
$countStmt->execute($params);
$totalTasks = $countStmt->fetchColumn();
$totalPages = ceil($totalTasks / $limit);

// Récupération des tâches paginées
$taskStmt = $pdo->prepare("
    SELECT * FROM tasks 
    WHERE user_id = ? AND deleted = FALSE $searchQuery
    ORDER BY created_at DESC 
    LIMIT $limit OFFSET $offset
");
$taskStmt->execute($params);
$tasks = $taskStmt->fetchAll();

// Tâches terminées (hors pagination)
$doneStmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? AND is_done = TRUE AND deleted = FALSE ORDER BY created_at DESC");
$doneStmt->execute([$userId]);
$doneTasks = $doneStmt->fetchAll();
?>

<div class="container mt-5">
    <h2>📝 My Tasks</h2>

    <!-- Recherche -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-outline-primary">Search</button>
        </div>
    </form>

    <!-- Formulaire ajout de tâche -->
    <form method="POST" action="../actions/add_task.php" class="mb-4">
        <div class="input-group">
            <input type="text" name="description" class="form-control" placeholder="New task..." required>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </div>
    </form>

    <div class="mb-3">
        <a href="trash.php" class="btn btn-outline-secondary btn-sm">🗑 View Trash</a>
    </div>

    <!-- Liste des tâches -->
    <?php if (count($tasks) > 0): ?>
        <ul class="list-group mb-4">
            <?php foreach ($tasks as $task): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <?= htmlspecialchars($task['description']) ?>
                        <?php if ($task['is_done']): ?>
                            <span class="badge bg-success">Done</span>
                        <?php endif; ?>
                    </span>
                    <div>
                        <?php if ($task['is_done']): ?>
                            <a href="../actions/mark_undone.php?id=<?= $task['id'] ?>" class="btn btn-outline-secondary btn-sm">↩</a>
                        <?php else: ?>
                            <a href="../actions/mark_done.php?id=<?= $task['id'] ?>" class="btn btn-success btn-sm">✔</a>
                        <?php endif; ?>
                        <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                        <a href="../actions/trash_task.php?id=<?= $task['id'] ?>" class="btn btn-outline-danger btn-sm">🗑</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-muted">No tasks found.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <?php
        $visiblePages = 2;
        $linkQuery = '&search=' . urlencode($search ?? '') . '&sort=' . urlencode($sort ?? '');
        ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php
                // Affiche la première page
                if ($page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1' . $linkQuery . '">1</a></li>';
                    if ($page > $visiblePages + 2) {
                        echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    }
                }

                // Affiche les pages autour de la page actuelle
                for ($i = max(2, $page - $visiblePages); $i <= min($totalPages - 1, $page + $visiblePages); $i++) {
                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">
                        <a class="page-link" href="?page=' . $i . $linkQuery . '">' . $i . '</a>
                      </li>';
                }

                // Affiche la dernière page
                if ($page < $totalPages - $visiblePages - 1) {
                    if ($page + $visiblePages < $totalPages - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . $linkQuery . '">' . $totalPages . '</a></li>';
                }

                // Met en valeur la page actuelle si c'est 1 ou totalPages
                if ($page == 1) {
                    echo '<li class="page-item active"><span class="page-link">1</span></li>';
                }
                if ($page == $totalPages && $totalPages > 1) {
                    echo '<li class="page-item active"><span class="page-link">' . $totalPages . '</span></li>';
                }
                ?>
            </ul>
        </nav>
    <?php endif; ?>



    <!-- Tâches terminées -->
    <?php if (count($doneTasks) > 0): ?>
        <h4 class="mt-5">✅ Completed Tasks</h4>
        <ul class="list-group">
            <?php foreach ($doneTasks as $task): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars($task['description']) ?></span>
                    <div>
                        <a href="../actions/mark_undone.php?id=<?= $task['id'] ?>" class="btn btn-outline-secondary btn-sm">↩</a>
                        <a href="../actions/trash_task.php?id=<?= $task['id'] ?>" class="btn btn-outline-danger btn-sm">🗑</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
