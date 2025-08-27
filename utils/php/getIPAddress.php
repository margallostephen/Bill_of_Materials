<?php
function getUserIP()
{
    foreach (
        [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ] as $key
    ) {
        if (!empty($_SERVER[$key])) {
            return $_SERVER[$key];
        }
    }
    return null;
}
