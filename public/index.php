<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Login</h2>

    <?php if (isset($_GET['login']) && $_GET['login'] === 'failed'): ?>
        <div class="alert alert-danger">Invalid credentials</div>
    <?php elseif (isset($_GET['registered'])): ?>
        <div class="alert alert-success">Registration successful! Please log in.</div>
    <?php endif; ?>

    <form method="POST" action="../actions/login.php">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" required class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <a href="register.php" class="btn btn-link">Register</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
