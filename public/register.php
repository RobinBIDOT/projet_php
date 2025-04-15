<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Register</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php
            switch ($_GET['error']) {
                case 'missing':
                    echo "Please fill in all fields.";
                    break;
                case 'weak':
                    echo "Password must be at least 6 characters.";
                    break;
                case 'exists':
                    echo "Username already taken.";
                    break;
            }
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="../actions/register.php">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Register</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
