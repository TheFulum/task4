<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/session_init.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/db_functions.php';

// регистрация
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_form'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $userIp = get_user_ip();
    $userAgent = get_user_agent();

    register($name, $email, $password, $userIp, $userAgent);
}

// вход
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_form'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    login($email, $password);
}

// выход
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_form'])) {
    logout();
}