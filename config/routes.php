<?php
require_once CONFIG_PATH . 'db.php';

$result = $bomMysqli->query("SELECT ROLE_ID FROM role_tb");
$allRoles = array_column($result->fetch_all(MYSQLI_ASSOC), 'ROLE_ID');

$routes = [
    'auth' => [0],
    'dashboard' => $allRoles,
    'bill_of_materials/list' => $allRoles,
    'error/403' => $allRoles,
    'error/404' => $allRoles,
    'allowed_roles' => $allRoles
];

return $routes;
