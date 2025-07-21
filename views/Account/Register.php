<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/session_init.php';
$pageTitle = 'Регистрация';
include_once $_SERVER['DOCUMENT_ROOT'] . '/views/Layout/Header.php';

$oldName = $_COOKIE['old_name'] ?? '';
$oldEmail = $_COOKIE['old_email'] ?? '';
$error = $_COOKIE['register_error'] ?? null;

?>

<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow-lg rounded-4">
        <div class="card-body">
            <h3 class="text-center mb-4">Регистрация</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>


            <form method="post" action="/logical/Account/Auth.php" id="registerForm">
                <div class="mb-3">
                    <label for="name" class="form-label">Имя*</label>
                    <input type="text" class="form-control rounded-3" value="<?= htmlspecialchars($oldName) ?>"
                        id="name" name="name" maxlength="75" required>
                    <div class="invalid-feedback">Пожалуйста, введите ваше имя</div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email*</label>
                    <input type="email" class="form-control rounded-3" value="<?= htmlspecialchars($oldEmail) ?>"
                        id="email" name="email" maxlength="50" required>
                    <div class="invalid-feedback">Пожалуйста, введите корректный email</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль*</label>
                    <input type="password" class="form-control rounded-3" id="password" name="password" required>
                    <div class="invalid-feedback">Пароль должен содержать минимум 6 символов</div>
                </div>
                <div class="mb-3">
                    <label for="repeat_password" class="form-label">Повторить пароль*</label>
                    <input type="password" class="form-control rounded-3" id="repeat_password" required>
                    <div class="invalid-feedback">Пароли не совпадают</div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success rounded-3"
                        name="register_form">Зарегистрироваться</button>
                </div>
                <div class="mt-2 text-end">
                    <a href="/views/Account/Login.php">Уже есть аккаунт?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/wwwroot/scripts/validationForm.js"></script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/views/Layout/Footer.php'; ?>