<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/core/autoload.php';

$messageModel = new MessageModel();
$fileModel = new FileModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_form'])) {
    $userId = !empty($_POST['user_id']) ? $_POST['user_id'] :null;
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $text = $_POST['text'];
    $fileName = null;

    if (!empty($_FILES['file']['name'])) {
        $fileInfo = $fileModel->saveFile($_FILES['file']);

        if (!empty($fileInfo['error'])) {
            setcookie('error', $fileInfo['error'], time() + 1, '/', '', true, true);
            header("Location: /");
        }

        $fileName = $fileInfo['fileName'];
    }

    $messageModel->saveMessage($name, $email, $text, $userId, $fileName);
}