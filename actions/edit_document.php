<?php
require_once '../config/db.php';
include '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

// Sécurité : vérifie que le document appartient bien à l'utilisateur
if (!$id || !ctype_digit($id)) {
    header("Location: documents.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $userId]);
$doc = $stmt->fetch();

if (!$doc) {
    header("Location: documents.php");
    exit;
}
?>

<div class="container mt-5">
    <h2>✏️ Edit Document</h2>

    <form action="../actions/update_document.php" method="POST">
        <input type="hidden" name="id" value="<?= $doc['id'] ?>">

        <div class="mb-3">
            <label class="form-label">File name</label>
            <input type="text" name="original_name" value="<?= htmlspecialchars($doc['original_name']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" value="<?= htmlspecialchars($doc['description']) ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option value="">No category</option>
                <?php foreach (['Cours', 'Devoir', 'Projet', 'Support', 'Autre'] as $cat): ?>
                    <option value="<?= $cat ?>" <?= $doc['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">💾 Save changes</button>
        <a href="documents.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
