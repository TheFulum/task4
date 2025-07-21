<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Подстановка bb-кодов
function parse_bbcodes($text)
{
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    $replacements = [
        '/\[b\](.*?)\[\/b\]/is' => '<b>$1</b>',
        '/\[i\](.*?)\[\/i\]/is' => '<i>$1</i>',
        '/\[u\](.*?)\[\/u\]/is' => '<u>$1</u>'
    ];

    foreach ($replacements as $pattern => $replacement) {
        $text = preg_replace($pattern, $replacement, $text);
    }

    return nl2br($text);

}

// Ip юзера
function get_user_ip(): string
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

// Агент юзера
function get_user_agent(): string
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
