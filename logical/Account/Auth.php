<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/session_init.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/autoload.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

$userModel = new UserModel();

// Регистрация
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_form'])) {
    $result = $userModel->register(
        trim($_POST['name']),
        trim($_POST['email']),
        $_POST['password'],
        get_user_ip(),
        get_user_agent()
    );

    if ($result['success']) {
        setcookie('register_success', 'Регистрация прошла успешно. Теперь вы можете войти.', time() + 1, '/', '', true, true);
        header('Location: /views/Account/Login.php');
    } else {
        setcookie('register_error', $result['error'], time() + 1, '/', '', true, true);
        setcookie('old_name', htmlspecialchars($_POST['name']), time() + 1, '/', '', true, true);
        setcookie('old_email', htmlspecialchars($_POST['email']), time() + 1, '/', '', true, true);

        header('Location: /views/account/register.php');
    }
}

// Вход
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_form'])) {
    $result = $userModel->login($_POST['email'], $_POST['password']);

    if ($result['success']) {
        $_SESSION['user_id'] = $result['user']['id'];
        $_SESSION['user_name'] = $result['user']['name'];
        $_SESSION['user_email'] = $_POST['email'];

        setcookie('success', 'Вы успешно вошли в аккаунт', time() + 1, '/', '', true, true);
        header('Location: /');
    } else {
        setcookie('login_error', $result['error'], time() + 1, '/', '', true, true);
        setcookie('old_email', htmlspecialchars($_POST['email']), time() + 1, '/', '', true, true);

        header('Location: /views/account/login.php');
    }
}

// Выход
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_form'])) {
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Недействительный CSRF-токен');
    }

    session_unset();
    session_destroy();
    setcookie('success', 'Вы успешно вышли из аккаунта', time() + 1, '/', '', true, true);
    header('Location: /');
}