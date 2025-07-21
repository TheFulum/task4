<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

class FileModel
{
    public function saveFile($file)
    {
        $result = ['error' => null, 'fileName' => ''];

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }

        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($fileExt, ALLOWED_IMAGE_TYPES)) {
            [$width, $height] = getimagesize($file['tmp_name']);
            if ($width > MAX_IMAGE_WIDTH || $height > MAX_IMAGE_HEIGHT) {
                $result['error'] = "Картинка не должна быть больше " . MAX_IMAGE_WIDTH . "x" . MAX_IMAGE_HEIGHT . " пикселей";
                return $result;
            }
        } elseif ($fileExt !== ALLOWED_FILE_EXTENSIONS) {
            $result['error'] = "Неверный тип файла. Допустимы только: " . implode(', ', ALLOWED_IMAGE_TYPES) . " и " . ALLOWED_FILE_EXTENSIONS;
            return $result;
        }

        if ($fileExt === ALLOWED_FILE_EXTENSIONS && $file['size'] > MAX_FILE_SIZE) {
            $result['error'] = "Текстовый файл не должен быть больше " . (MAX_FILE_SIZE / 1024) . "KB";
            return $result;
        }

        $newFileName = uniqid() . '.' . $fileExt;
        $destination = UPLOAD_DIR . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $result['fileName'] = $newFileName;
        } else {
            $result['error'] = "Ошибка загрузки файла";
        }

        return $result;
    }
}