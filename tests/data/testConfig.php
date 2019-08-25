<?php

if (!defined('PATH_BASE')) {
    define('PATH_BASE', realpath(__DIR__ . '/..') . '/');
}
if (!defined('PATH_APP')) {
    define('PATH_APP', realpath(__DIR__ . '/..') . '/data/');
}
if (!defined('PATH_LOGS')) {
    define('PATH_LOGS', PATH_BASE . 'logs/');
}
if (!defined('URL_BASE')) {
    define('URL_BASE', 'testserver');
}
if (!defined('URL_MEDIA')) {
    define('URL_MEDIA', 'testserver');
}
if (!defined('URL_HTTPS')) {
    define('URL_HTTPS', false);
}
if (!defined('URL_REWRITE')) {
    define('URL_REWRITE', false);
}

if (!defined('DEBUG_REQUEST')) {
    define('DEBUG_REQUEST', false);
}

return [
    'domain' => [
        'key' => 'value'
    ],
    'application' => [
        'logTo' => 'null'
    ],
    'database' => [
        'dsn' => 'example',
        'user' => 'dbuser',
        'password' => 'dbpassword',
    ]
];