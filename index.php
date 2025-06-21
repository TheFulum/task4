<?php
$pageTitle = 'Чат';
include_once $_SERVER['DOCUMENT_ROOT'] . '/views/Layout/Header.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/logical/Chat.php';

$success = $_COOKIE['success'] ?? '';
$error = $_COOKIE['error'] ?? '';
$userName = $_SESSION['user_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';

// отображение сообщений
$filter_name = $_GET['filter_name'] ?? '';
$filter_email = $_GET['filter_email'] ?? '';
$filter_date = $_GET['filter_date'] ?? '';
$filter_order = $_GET['filter_order'] ?? 'desc';

$sql = "SELECT name, email, text, created_at, file_name FROM messages WHERE 1=1";
$params = [];


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter_form'])) {

    if (!empty($filter_name)) {
        $sql .= " AND name LIKE :name";
        $params[':name'] = '%' . $filter_name . '%';
    }

    if (!empty($filter_email)) {
        $sql .= " AND email LIKE :email";
        $params[':email'] = '%' . $filter_email . '%';
    }

    if (!empty($filter_date)) {
        $sql .= " AND DATE(created_at) = :date";
        $params[':date'] = $filter_date;
    }
}


$sql .= " ORDER BY created_at $filter_order";
$countOfMessages = get_count_of_messages($params, $sql);

$totalPages = ceil($countOfMessages / 25);
$thisPage = $_GET['page'] ?? 1;
$limit = 25;
$offset = ($thisPage - 1) * $limit;

// !через бинд параметров не работает!
$sql .= " LIMIT " . $limit . " OFFSET " . (int) $offset;


$messages = get_messages($sql, $params);


?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success my-2"><?= $success ?></div>
<?php elseif (!empty($error)): ?>
    <div class="alert alert-danger my-2"><?= $error ?></div>
<?php endif; ?>


<div class="row justify-content-center w-100">
    <div class="card-body p-4">
        <h1 class="text-center mb-4">Новое сообщение</h1>

        <form method="post" action="/logical/Chat.php" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? null ?>">
            <div class="mb-3">
                <label for="user_name" class="form-label">Имя пользователя*</label>
                <input type="text" class="form-control" id="user_name" maxlength="75" name="name"
                    value="<?= $userName ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email*</label>
                <input type="email" class="form-control" id="email" maxlength="50" name="email"
                    pattern="[^\s@]+@[^\s@]+\.[^\s@]+" value="<?= $userEmail ?>" required>
            </div>

            <div class="mb-3">
                <label for="text" class="form-label">Сообщение*</label>
                <textarea class="form-control" id="text" maxlength="16777215" name="text" rows="2" required></textarea>
            </div>

            <div class="mb-4">
                <label for="file" class="form-label">Прикрепить файл</label>
                <input type="file" class="form-control" id="file" name="file" accept=".jpg,.jpeg,.png,.gif,.txt">
                <div class="form-text">
                    Допустимы форматы: JPG(JPEG)/GIF/PNG (до 320×240px) или TXT (до 100KB)
                </div>
            </div>

            <button type="submit" name="message_form" class="btn btn-primary w-100">Отправить</button>
        </form>
    </div>
</div>

<div class="card mb-4 w-10">
    <div class="card-body">
        <h5 class="card-title">Фильтры</h5>
        <form method="get" action="">
            <div class="row g-2">
                <div class="col-md-12">
                    <label for="filter_name" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="filter_name" name="filter_name"
                        value="<?= htmlspecialchars($_GET['filter_name'] ?? '') ?>">
                </div>

                <div class="col-md-12">
                    <label for="filter_email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="filter_email" name="filter_email"
                        value="<?= htmlspecialchars($_GET['filter_email'] ?? '') ?>">
                </div>

                <div class="col-md-12">
                    <label for="filter_date_from" class="form-label">Дата</label>
                    <input type="date" class="form-control mb-2" id="filter_date" name="filter_date"
                        value="<?= htmlspecialchars($_GET['filter_date'] ?? '') ?>">

                    <div class="radio-group my-3">
                        <label class="radio-option">
                            <input type="radio" id="filter_date_desc" name="filter_order" value="desc"
                                <?= (isset($_GET['filter_order']) && $_GET['filter_order'] === 'desc' || !isset($_GET['filter_order'])) ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <span class="radio-label">Новые</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" id="filter_date_asc" name="filter_order" value="asc"
                                <?= (isset($_GET['filter_order']) && $_GET['filter_order'] === 'asc') ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <span class="radio-label">Старые</span>
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" name="filter_form" class="btn btn-primary">Применить</button>
                    <a href="/" class="btn btn-secondary">Сбросить</a>
                </div>
            </div>

            <input type="hidden" name="page" value="1">
        </form>
    </div>
</div>

<div class="table-responsive">
    <div class="text-end p-2 fs-4">
        <u><?= !empty($messages) ? 'Найдено сообщений: ' . $countOfMessages : '' ?></u>
    </div>
    <table class="table table-striped table-hover align-middle text-center">
        <thead class="table-light">
            <tr>
                <th width="150">Имя</th>
                <th width="150">Почта</th>
                <th width="450">Сообщение</th>
                <th width="150">Файл</th>
                <th width="150">Дата</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4">Сообщений не найдено</td>
                </tr>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= htmlspecialchars($message['name']) ?></td>
                        <td><?= htmlspecialchars($message['email']) ?><?= isset($_SESSION['user_email']) == $message['email'] ? '(Вы)' : '' ?>
                        </td>
                        <td>
                            <div class="message-text">
                                <?= nl2br(htmlspecialchars($message['text'])) ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($message['file_name'])): ?>
                                <?php if (in_array(strtolower(pathinfo($message['file_name'], PATHINFO_EXTENSION)), ALLOWED_IMAGE_TYPES)): ?>
                                    <a href="/wwwroot/uploads/<?= htmlspecialchars($message['file_name']) ?>"
                                        target="_blank">Просмотреть</a>
                                <?php else: ?>
                                    <a href="/wwwroot/uploads/<?= htmlspecialchars($message['file_name']) ?>" download>Скачать</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($message['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>



<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($thisPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" aria-label="First">
                        <span aria-hidden="true">&laquo;&laquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $thisPage - 1])) ?>"
                        aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $startPage = max(1, $thisPage - 2);
            $endPage = min($totalPages, $thisPage + 2);

            for ($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?= $i == $thisPage ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($thisPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $thisPage + 1])) ?>"
                        aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>"
                        aria-label="Last">
                        <span aria-hidden="true">&raquo;&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/views/Layout/Footer.php'; ?>