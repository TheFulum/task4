<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';

function getDBConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Ошибка подключения к БД: " . $e->getMessage());
        }
    }

    return $pdo;
}

function register($name, $email, $password, $userIp, $userAgent): void
{
    $stmt = getDBConnection()->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if (!$stmt->fetch()) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = getDBConnection()->prepare("INSERT INTO users (name, password, email, ip, browser) VALUES(?, ?, ?, ?, ?)");
            $stmt->execute([$name, $hashedPassword, $email, $userIp, $userAgent]);

            $_SESSION['register_success'] = 'Регистрация прошла успешно. Теперь вы можете войти.';
            header('Location: /views/Account/Login.php');
        } catch (PDOException $e) {
            $_SESSION['register_error'] = 'Ошибка регистрации. Пожалуйста, попробуйте позже.';
            header('Location: /views/Account/Register.php');
        }
    } else {
        $_SESSION['register_error'] = 'Введённый email уже существует';
        $_SESSION['old_name'] = htmlspecialchars($name);
        $_SESSION['old_email'] = htmlspecialchars($email);
        header('Location: /views/Account/Register.php');
    }
}

function login($email, $password): void
{
    $stmt = getDBConnection()->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $email;

        setcookie('success', 'Вы успешно вошли в аккаунт', time() + 1, '/', '', true, true);
        header('Location: /');
    } else {
        $_SESSION['login_error'] = 'Неверный email или пароль';
        $_SESSION['old_email'] = htmlspecialchars($email);
        header('Location: /views/Account/Login.php');
    }
}

function logout(): void
{
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Недействительный CSRF-токен');
    }

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    setcookie('success', 'Вы успешно вышли из аккаунта', time() + 1, '/', '', true, true);
    header('Location: /');
}

function save_message($user_id = null, $name, $email, $text, $file_name = null): void
{
    $stmt = getDBConnection()->prepare("INSERT INTO messages (text, file_name, user_id, name, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$text, $file_name, $user_id, $name, $email]);

    setcookie('success', 'Сообщение успешно добавленно', time() + 1, '/', '', true, true);
    header("Location: /");
}

function get_count_of_messages(array $params, string $sql): int
{
    $stmt = getDBConnection()->prepare('SELECT COUNT(*) FROM (' . $sql . ') AS total');
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $total = $stmt->fetchColumn();

    return $total;
}

function get_messages(string $sql, array $params): array
{
    $stmt = getDBConnection()->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $messages;
}
