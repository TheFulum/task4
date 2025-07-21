<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/Model.php';

class MessageModel extends Model
{
    public function saveMessage($name, $email, $text, $userId = null, $fileName = null)
    {
        $stmt = $this->db->prepare("INSERT INTO messages (text, file_name, user_id, name, email) VALUES (?, ?, ?, ?, ?)");

        try {
            $stmt->execute([$text, $fileName, $userId, $name, $email]);
        } catch (Exception $e) {
            die('Ошибка сохранения сообщения: ' . $e);
        }

        setcookie('success', 'Сообщение успешно добавленно', time() + 1, '/', '', true, true);
        header("Location: /");
    }

    public function getMessages($filters = [], $limit = 25, $offset = 0)
    {
        $sql = "SELECT name, email, text, created_at, file_name FROM messages WHERE 1=1";
        $params = [];

        if (!empty($filters['name'])) {
            $sql .= " AND name LIKE :name";
            $params[':name'] = '%' . trim($filters['name']) . '%';
        }

        if (!empty($filters['email'])) {
            $sql .= " AND email LIKE :email";
            $params[':email'] = '%' . trim($filters['email']) . '%';
        }

        if (!empty($filters['date'])) {
            $sql .= " AND DATE(created_at) = :date";
            $params[':date'] = $filters['date'];
        }

        $sql .= " ORDER BY created_at " . ($filters['order'] ?? 'desc');
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getMessagesCount($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM messages WHERE 1=1";
        $params = [];

        if (!empty($filters['name'])) {
            $sql .= " AND name LIKE :name";
            $params[':name'] = '%' . trim($filters['name']) . '%';
        }

        if (!empty($filters['email'])) {
            $sql .= " AND email LIKE :email";
            $params[':email'] = '%' . trim($filters['email']) . '%';
        }

        if (!empty($filters['date'])) {
            $sql .= " AND DATE(created_at) = :date";
            $params[':date'] = $filters['date'];
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}