<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/serverPath.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';

$rfid = $_POST['rfid'] ?? '';
$password = $_POST['password'] ?? '';
$response = ['status' => false, 'message' => ''];

if (empty($rfid) || empty($password)) {
    $response['message'] = 'ID and password are required.';
    echo json_encode($response);
    exit;
}

$sql = "SELECT * FROM `1_employee_masterlist_tb` WHERE RFID = ?";
$stmt = $hrisMysqli->prepare($sql);
if (!$stmt) {
    exit("HRIS prepare failed: " . $hrisMysqli->error);
}
$stmt->bind_param("s", $rfid);
$stmt->execute();
$hrisResult = $stmt->get_result();

if ($hrisResult->num_rows === 0) {
    $response['message'] = 'User not registered in HRIS.';
    echo json_encode($response);
    exit;
}
$userHrisData = $hrisResult->fetch_assoc();
$stmt->close();

$sql = "
    SELECT u.*, r.ROLE_NAME
    FROM `user_tb` AS u
    INNER JOIN `role_tb` AS r ON u.ROLE_ID = r.ROLE_ID
    WHERE u.RFID = ?
";
$stmt = $bomMysqli->prepare($sql);
if (!$stmt) {
    exit("BOM prepare failed: " . $bomMysqli->error);
}
$stmt->bind_param("s", $rfid);
$stmt->execute();
$bomResult = $stmt->get_result();

if ($bomResult->num_rows === 0) {
    $response['message'] = 'User not found.';
    echo json_encode($response);
    exit;
}
$user = $bomResult->fetch_assoc();
$stmt->close();

if ($user['ACTIVE'] != 1 && !in_array($user['RFID'], ["ADMIN", 101])) {
    $response['message'] = 'Account disabled. Contact administrator.';
    echo json_encode($response);
    exit;
}

if (!password_verify($password, $user['PASSWORD'])) {
    $response['message'] = 'Incorrect password.';
    echo json_encode($response);
    exit;
}

session_start();
$_SESSION = [
    'RFID' => $user['RFID'],
    'FIRST_NAME' => $userHrisData['F_NAME'],
    'LAST_NAME' => $userHrisData['L_NAME'],
    'EMAIL' => $userHrisData['EMAIL'],
    'ROLE' => $user['ROLE_ID'],   
    'ROLE_NAME' => $user['ROLE_NAME'],
    'LAST_ACTIVITY' => time(),
];

unset($user['PASSWORD']); 
$response = ['status' => true, 'message' => 'Logging in.', 'user' => $_SESSION];

$hrisMysqli->close();
$bomMysqli->close();

echo json_encode($response);
