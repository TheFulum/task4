<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/session_init.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

// защита сессии
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
    $_SESSION['ip'] = get_user_ip();
    $_SESSION['ua'] = get_user_agent();
} else {
    // юзер с другого ip или другой user_agent
    if ($_SESSION['ip'] !== get_user_ip() || $_SESSION['ua'] !== get_user_agent()) {
        session_unset();
        session_destroy();
        die('Обнаружена подозрительная активность!');
    }
}

$isLoggedIn = isset($_SESSION['user_id']) ?? null;

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Чат' ?></title>
    <link rel="icon" type="image/x-icon" href="wwwroot/favicon.ico">
    <link rel="stylesheet" href="/wwwroot/styles/bootstrap5.3.7.css">
    <link rel="stylesheet" href="/wwwroot/styles/main.css">
</head>

<body class="container">
    <header class="border-bottom border-gray">
        <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
            <div class="container">
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link active fs-4" href="/">Главная</a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center">
                        <?php if (!($isLoggedIn)): ?>
                            <a href="/views/Account/Login.php" class="btn btn-outline-primary rounded-pill me-2">
                                <i class="bi bi-person-plus me-1"></i>Войти
                            </a>
                            <a href="/views/Account/Register.php" class="btn btn-outline-primary rounded-pill">
                                <i class="bi bi-person-plus"></i>Зарегистрироваться
                            </a>
                        <?php else: ?>
                            <form method="post" action="/logical/Account/Auth.php">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="btn btn-primary rounded-pill me-1 px-4"
                                    name="logout_form">Выйти</button>
                            </form>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>