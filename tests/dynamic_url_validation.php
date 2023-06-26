<?php

function isUrlValid($url, $mask) {
    $pattern = '#^' . str_replace('/', '\/', preg_quote($mask, '#')) . '$#';

    return preg_match($pattern, $url) === 1;
}


$url = '/articles/1';
$mask = '/articles[/<page>]';

if (isUrlValid($url, $mask)) {
    echo 'URL je validní';
} else {
    echo 'URL je nevalidní';
}
