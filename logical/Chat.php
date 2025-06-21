<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/core/session_init.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/db_functions.php';



// сохранение сообщения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_form'])) {
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $text = $_POST['text'];

    if (!empty($_FILES['file']['name'])) {
        $file_info = save_file($_FILES['file']);
    }

    if (empty($file_info['error']) && !empty($_FILES['file']['name'])) {
        save_message($user_id, $name, $email, $text, $file_info['new_file_name']);
    } elseif (empty($_FILES['file']['name'])) {
        save_message($user_id, $name, $email, $text);
    } elseif (!empty($file_info['error'])) {
        setcookie('error', 'Ошибка сохранения сообщения. ' . $file_info['new_file_name'] . '', time() + 1, '/', '', true, true);
        header("Location: /");
    }
}

