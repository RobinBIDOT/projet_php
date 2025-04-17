<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Créer dossier utilisateur si non existant
$uploadDir = "../uploads/$userId/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// 🔎 Recherche, filtre, tri, pagination
$search = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category_filter'] ?? '');
$sort = $_GET['sort'] ?? 'date';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 6;
$offset = ($page - 1) * $limit;

$orderBy = $sort === 'name' ? 'original_name ASC' : 'uploaded_at DESC';

$where = "WHERE user_id = ?";
$params = [$userId];

if ($search !== '') {
    $where .= " AND original_name LIKE ?";
    $params[] = "%$search%";
}
if ($categoryFilter !== '') {
    $where .= " AND category = ?";
    $params[] = $categoryFilter;
}

// Total pour pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM documents $where");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

// Documents paginés
$stmt = $pdo->prepare("SELECT * FROM documents $where ORDER BY $orderBy LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$documents = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h2>📁 My Documents</h2>

    <!-- Upload form -->
    <form action="../actions/upload_document.php" method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <input type="file" name="document" class="form-control" required>
        </div>
        <div class="mb-3">
            <input type="text" name="description" class="form-control" placeholder="Optional description...">
        </div>
        <div class="mb-3">
            <select name="category" class="form-select">
                <option value="">Choose category (optional)</option>
                <option value="Cours">Cours</option>
                <option value="Devoir">Devoir</option>
                <option value="Projet">Projet</option>
                <option value="Support">Support</option>
                <option value="Autre">Autre</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">📤 Upload</button>
    </form>

    <!-- Filter/search -->
    <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="🔍 Search by file name..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <select name="category_filter" class="form-select">
                <option value="">All categories</option>
                <?php foreach (['Cours', 'Devoir', 'Projet', 'Support', 'Autre'] as $cat): ?>
                    <option value="<?= $cat ?>" <?= $categoryFilter === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="date" <?= $sort === 'date' ? 'selected' : '' ?>>📅 Sort by date</option>
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>🔤 Sort by name</option>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
        </div>
    </form>

    <!-- Documents -->
    <?php if (count($documents) > 0): ?>
        <div class="row">
            <?php foreach ($documents as $doc): ?>
                <?php
                $filePath = "../uploads/$userId/{$doc['filename']}";
                $isImage = str_starts_with($doc['mime_type'], 'image/');
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if ($isImage): ?>
                            <img src="<?= $filePath ?>" class="card-img-top" style="object-fit: cover; height: 200px;">
                        <?php elseif ($doc['mime_type'] === 'application/pdf'): ?>
                            <iframe src="<?= $filePath ?>" height="200" class="w-100"></iframe>
                        <?php elseif (str_starts_with($doc['mime_type'], 'text/')): ?>
                            <div class="p-3"><pre style="max-height:200px; overflow:auto;"><?= htmlspecialchars(file_get_contents($filePath)) ?></pre></div>
                        <?php else: ?>
                            <div class="p-3 text-muted">📄 <?= htmlspecialchars($doc['original_name']) ?></div>
                        <?php endif; ?>

                        <div class="card-body">
                            <h6 class="card-title"><?= htmlspecialchars($doc['original_name']) ?></h6>
                            <?php if (!empty($doc['category'])): ?>
                                <span class="badge bg-info text-dark"><?= htmlspecialchars($doc['category']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($doc['description'])): ?>
                                <p class="card-text text-muted"><?= htmlspecialchars($doc['description']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-outline-primary">🔍 View</a>
                            <a href="<?= $filePath ?>" download class="btn btn-sm btn-outline-success">⬇️ Download</a>
                            <a href="edit_document.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-outline-warning">✏️ Edit</a>
                            <a href="../actions/delete_document.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this document?')">🗑 Delete</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&category_filter=<?= urlencode($categoryFilter) ?>&sort=<?= urlencode($sort) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <p class="text-muted">No documents uploaded yet.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
