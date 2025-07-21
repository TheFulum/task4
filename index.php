<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/core/autoload.php';

$messageModel = new MessageModel();
$userModel = new UserModel();

$pageTitle = 'Чат';

include_once $_SERVER['DOCUMENT_ROOT'] . '/views/layout/header.php';

$success = $_COOKIE['success'] ?? '';
$error = $_COOKIE['error'] ?? '';
$userName = $_SESSION['user_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';

$filters = [
    'name' => $_GET['filter_name'] ?? '',
    'email' => $_GET['filter_email'] ?? '',
    'date' => $_GET['filter_date'] ?? '',
    'order' => $_GET['filter_order'] ?? 'desc'
];

$page = $_GET['page'] ?? 1;
$limit = 25;
$offset = ($page - 1) * $limit;

$messages = $messageModel->getMessages($filters, $limit, $offset);
$totalMessages = $messageModel->getMessagesCount($filters);
$totalPages = ceil($totalMessages / $limit);
?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success my-2"><?= $success ?></div>
<?php elseif (!empty($error)): ?>
    <div class="alert alert-danger my-2"><?= $error ?></div>
<?php endif; ?>


<!-- Новое сообщение -->
<div class="justify-content-center w-100">
    <div class="card-body p-4">
        <h1 class="text-center mb-4">Новое сообщение</h1>

        <form method="post" action="/logical/chat.php" enctype="multipart/form-data">
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
                <label for="text" class="form-label m-0">Сообщение*</label>
                <textarea class="form-control" id="text" maxlength="16777215" name="text" rows="2" required></textarea>

                <div class="btn-group btn-group-sm mt-1" role="group">
                    <button type="button" class="btn btn-outline-secondary bb-code-btn" data-tag="b" title="Жирный [b]">
                        <b>B</b>
                    </button>
                    <button type="button" class="btn btn-outline-secondary bb-code-btn" data-tag="i" title="Курсив [i]">
                        <i>I</i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary bb-code-btn" data-tag="u"
                        title="Подчёркивание [u]">
                        <u>U</u>
                    </button>
                </div>
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


<div class="col-md-12 mb-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title mb-4">Фильтры сообщений</h2>

            <form method="get" action="">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="filter_name" class="form-label">Имя автора</label>
                        <input type="text" class="form-control" id="filter_name" name="filter_name"
                            value="<?= htmlspecialchars($filters['name']) ?>">
                    </div>

                    <div class="col-12">
                        <label for="filter_email" class="form-label">Email автора</label>
                        <input type="text" class="form-control" id="filter_email" name="filter_email"
                            value="<?= htmlspecialchars($filters['email']) ?>">
                    </div>

                    <div class="col-12">
                        <label for="filter_date" class="form-label">Дата сообщения</label>
                        <input type="date" class="form-control" id="filter_date" name="filter_date"
                            value="<?= htmlspecialchars($filters['date']) ?>">
                    </div>

                    <div class="col-12">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="filter_order" id="filter_desc" value="desc"
                                <?= $filters['order'] === 'desc' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="filter_desc">Сначала новые</label>

                            <input type="radio" class="btn-check" name="filter_order" id="filter_asc" value="asc"
                                <?= $filters['order'] === 'asc' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="filter_asc">Сначала старые</label>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-between">
                        <button type="submit" name="filter_form" class="btn btn-primary px-4">
                            Применить
                        </button>
                        <a href="/" class="btn btn-outline-secondary">Сбросить</a>
                    </div>
                </div>

                <input type="hidden" name="page" value="1">
            </form>
        </div>
    </div>
</div>
</div>


<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Сообщения</h3>
            <span class="badge bg-primary rounded-pill fs-6">
                Всего: <?= $totalMessages ?>
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5">
                <i class="bi bi-chat-square-text fs-1 text-muted"></i>
                <p class="fs-4 text-muted mt-3">Сообщений не найдено</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($messages as $message): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <strong><?= htmlspecialchars($message['name']) ?></strong>
                                <span class="text-muted">
                                    <?= '(' . htmlspecialchars($message['email']) . ')' ?>
                                    <?= ($userEmail ?? null) === $message['email'] ? '(Вы)' : '' ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <?= date('d.m.Y H:i', strtotime($message['created_at'])) ?>
                            </small>
                        </div>

                        <div class="mb-2">
                            <?= parse_bbcodes($message['text']) ?>
                        </div>

                        <?php if (!empty($message['file_name'])): ?>
                            <div>
                                <?php if (in_array(strtolower(pathinfo($message['file_name'], PATHINFO_EXTENSION)), ALLOWED_IMAGE_TYPES)): ?>
                                    <a href="/wwwroot/uploads/<?= htmlspecialchars($message['file_name']) ?>" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-image"></i> Просмотреть
                                    </a>
                                <?php else: ?>
                                    <a href="/wwwroot/uploads/<?= htmlspecialchars($message['file_name']) ?>" download
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Скачать
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>"
                            aria-label="First">
                            <span aria-hidden="true">&laquo;&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>"
                            aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>"
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

    <script src="/wwwroot/scripts/bb-codes.js"></script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/views/layout/footer.php'; ?>