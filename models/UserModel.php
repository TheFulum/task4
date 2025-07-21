<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/Model.php';

class UserModel extends Model
{
    public function register($name, $email, $password, $ip, $userAgent)
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");

        try {
            $stmt->execute([$email]);
        } catch (Exception $e) {
            die('Ошибка регистрации: ' . $e);
        }

        try {
            $stmt->execute([$email]);
        } catch (Exception $e) {
            setcookie('error', 'Ошибка сохранения пользователя: ' . $e, time() + 1, '/', '', true, true);
            header('Location: /views/account/register.php');

            return;
        }

        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Email уже существует'];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (name, password, email, ip, browser) VALUES(?, ?, ?, ?, ?)");
        $stmt->execute([$name, $hashedPassword, $email, $ip, $userAgent]);

        return ['success' => true, 'userId' => $this->db->lastInsertId()];
    }

    public function login($email, $password)
    {
        $stmt = $this->db->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return ['success' => true, 'user' => $user];
        }

        return ['success' => false, 'error' => 'Неверный email или пароль'];
    }
}