<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function save_file($file)
{
    $result = ['error' => null, 'name' => '', 'new_file_name' => '', 'type' => '', 'path' => ''];

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $file_name = basename($file['name']);
    $file_tmp = $file['tmp_name'];
    $file_type = $file['type'];
    $file_size = $file['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    

    if (in_array($file_ext, ALLOWED_IMAGE_TYPES)) {
        [$width, $height] = getimagesize($file_tmp);
        if ($width > MAX_IMAGE_WIDTH || $height > MAX_IMAGE_HEIGHT) {
            $result['error'] = "Картинка не должна быть больше 320x240 пикселей";
            return $result;
        }
    } elseif ($file_ext !== ALLOWED_FILE_EXTENSIONS) {
        $result['error'] = "Неверный тип файла. Картинки только в формате JPG, GIF, PNG. Текстовый файл только в формате TXT";
        return $result;
    }

    if ($file_ext === ALLOWED_FILE_EXTENSIONS && $file_size > MAX_FILE_SIZE) {
        $result['error'] = "Текстовый файл не должен быть больше 100KB";
        return $result;
    }

    // Генерация уникального имени файла
    $new_file_name = uniqid() . '.' . $file_ext;
    $destination = UPLOAD_DIR . $new_file_name;

    if (move_uploaded_file($file_tmp, $destination)) {
        $result['name'] = $file_name;
        $result['type'] = $file_type;
        $result['path'] = $destination;
        $result['new_file_name'] = $new_file_name;
    } else {
        $result['error'] = "Ошибка загрузки файла";
    }

    return $result;

}

// ip юзера
function get_user_ip()
{
    static $ipaddress = null;
    if (empty($ipaddress)) {
        if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
}

// агент юзера
function get_user_agent()
{
    static $userAgent = null;
    if (empty($userAgent)) {
        if (isset($_SERVER['HTTP_USER_AGENT']))
            $userAgent = (string) $_SERVER['HTTP_USER_AGENT'];
        else
            $userAgent = 'UNKNOWN';
    }

    return $userAgent;
}
