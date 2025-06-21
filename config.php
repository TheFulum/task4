<?php

// настройки для дб
define('DB_HOST', 'localhost');
define('DB_NAME', 'task2');
define('DB_USER', 'root');
define('DB_PASS', 'lipouski123');

// настройки для файлов
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/wwwroot/uploads/');
define('MAX_FILE_SIZE', 102400); // 100KB в байтах
define('MAX_IMAGE_WIDTH', 320);
define('MAX_IMAGE_HEIGHT', 240);
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_FILE_EXTENSIONS', 'txt');