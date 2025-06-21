<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/session_init.php';
$pageTitle = 'Вход';
include_once $_SERVER['DOCUMENT_ROOT'] . '/views/Layout/Header.php';

$oldEmail = $_SESSION['old_email'] ?? '';
$error = $_SESSION['login_error'] ?? null;
$success = $_SESSION['register_success'] ?? '';

unset($_SESSION['old_email'], $_SESSION['login_error']);
?>

<div class="container mt-5" style="max-width: 450px;">
    <div class="card shadow-lg rounded-4">
        <div class="card-body">
            <h3 class="text-center mb-4">Вход</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="post" action="/logical/Account/Auth.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Email*</label>
                    <input type="email" class="form-control rounded-3" id="email" name="email"
                        value="<?= htmlspecialchars($oldEmail) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль*</label>
                    <input type="password" class="form-control rounded-3" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success rounded-3" name="login_form">Войти</button>
                </div>
                <div class="mt-2 text-end">
                    <a href="/views/Account/Register.php">Еще нет аккаунта?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/views/Layout/Footer.php'; ?>