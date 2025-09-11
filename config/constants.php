<?php
define('BASE_PATH', realpath(__DIR__ . '/../') . '/');
define('BASE_URL', '/' . basename(BASE_PATH) . '/');
define('SYSTEM_NAME', 'Bill of Materials Management System');
define('COMPANY_NAME', 'Prima Tech Phils., Inc.');

define('CONFIG_PATH', BASE_PATH . 'config/');

define('PHP_UTILS_PATH', BASE_PATH . 'utils/php/');
define('JS_UTILS_PATH', BASE_URL . 'utils/js/');

define('PHP_HELPERS_PATH', BASE_PATH . 'helpers/php/');
define('JS_HELPERS_PATH', BASE_URL . 'helpers/js/');

define('JS_PATH', BASE_URL . 'assets/js/');
define('CSS_PATH', BASE_URL . 'assets/css/');
define('IMAGES_URL', BASE_URL . 'assets/images/');

define('PAGES_PATH', BASE_PATH . 'pages');
define('PARTIALS_PATH', BASE_PATH . 'partials');

define('BACKEND_PATH', BASE_URL . 'backend/');
define('AJAX_PATH', BASE_URL . 'ajax/');
define('UPLOADS_PATH', BASE_URL . 'uploads/');
define('UPLOADS_DIR', BASE_PATH . 'uploads/');
