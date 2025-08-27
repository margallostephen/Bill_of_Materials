<?php

session_start();
require_once __DIR__ . '/config/serverPath.php';
require_once __DIR__ . '/config/clientPath.php';
require_once PHP_HELPERS_PATH . 'getAssetPath.php';

$routes = require CONFIG_PATH . 'routes.php';

$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri, PHP_URL_PATH);

$normalizedBaseUrl = rtrim(BASE_URL, '/');

$path = preg_replace('#^' . preg_quote($normalizedBaseUrl, '#') . '/?#i', '', $parsedUrl);

$route = trim($path, '/');

$route = $path ?: 'auth';
$_SESSION['current_route'] = $route;

$userRole = $_SESSION['ROLE'] ?? '0';

if (
    $route !== 'auth' &&
    strpos($route, 'error/') !== 0 &&
    isset($routes[$route])
) {
    $_SESSION['last_route'] = $route;
}

if ($route === 'auth' && $userRole !== '0') {
    $fallbackRoute = $_SESSION['last_route'] ?? $_SESSION['ROLE'] != 3 ? 'dashboard' : 'survey';
    header("Location: " . BASE_URL . $fallbackRoute);
    exit;
}

$allowedRoles = null;
$matchedPrefix = null;

foreach ($routes as $definedPrefix => $roles) {
    if (strpos($route, $definedPrefix) === 0) {
        $allowedRoles = $roles;
        $matchedPrefix = $definedPrefix;
        break;
    }
}

if (is_null($allowedRoles)) {
    http_response_code(404);
    include PAGES_PATH . '/error/404.php';
    exit;
}

if (!in_array($userRole, $allowedRoles)) {
    http_response_code(403);
    include PAGES_PATH . '/error/403.php';
    exit;
}

$pagePath = PAGES_PATH . '/' . $route . '/index.php';

if (!file_exists($pagePath)) {
    http_response_code(404);
    include PAGES_PATH . '/error/404.php';
    exit;
}

include $pagePath;
