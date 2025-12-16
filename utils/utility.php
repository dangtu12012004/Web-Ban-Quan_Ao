<?php
function removeSpecialCharacter($str) {
    if (!is_string($str)) return $str;

    $str = str_replace('\\', '\\\\', $str);
    $str = str_replace('\'', '\\\'', $str);
    return $str;
}

function getPost($key, $default = '') {
    if (!isset($_POST[$key])) {
        return $default;
    }
    return removeSpecialCharacter($_POST[$key]);
}

function getGet($key, $default = '') {
    if (!isset($_GET[$key])) {
        return $default;
    }
    return removeSpecialCharacter($_GET[$key]);
}
