<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';

$response = ['status' => false, 'message' => ''];

// Get inputs
$rfid = $_POST['rfid'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate inputs
if (empty($rfid) || empty($password) || empty($confirmPassword)) {
    $response['message'] = 'All fields are required.';
    echo json_encode($response);
    exit;
}

if ($password !== $confirmPassword) {
    $response['message'] = 'Confirmation password does not match.';
    echo json_encode($response);
    exit;
}

$sql = "
    SELECT e.RFID, e.DEPARTMENT_ID, j.JOB_LEVEL_ID
    FROM `1_employee_masterlist_tb` AS e
    INNER JOIN `job_position_tb` AS j
        ON e.JOB_POSITION_ID = j.JOB_POSITION_ID
    WHERE e.RFID = ?
";
$stmt = $hrisMysqli->prepare($sql);
$stmt->bind_param("s", $rfid);
$stmt->execute();
$hrisResult = $stmt->get_result();

if ($hrisResult->num_rows === 0) {
    $response['message'] = 'RFID not found in HRIS. Contact HR.';
    echo json_encode($response);
    exit;
}

$employee = $hrisResult->fetch_assoc();

$sql = "SELECT RFID FROM `user_tb` WHERE RFID = ?";
$stmt = $bomMysqli->prepare($sql);
$stmt->bind_param("s", $rfid);
$stmt->execute();
$bomResult = $stmt->get_result();

if ($bomResult->num_rows > 0) {
    $response['message'] = 'User already registered.';
    echo json_encode($response);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

$sql = "INSERT INTO `user_tb` 
        (RFID, PASSWORD, ROLE_ID) 
        VALUES (?, ?, ?)";
$stmt = $bomMysqli->prepare($sql);

$roleId = $employee['JOB_LEVEL_ID'];

if ($employee['DEPARTMENT_ID'] == 22) {
    $roleId = 4;
}

$stmt->bind_param(
    "ssi",
    $employee['RFID'],
    $hashedPassword,
    $roleId
);

if ($stmt->execute()) {
    $response = [
        'status' => true,
        'message' => 'Registration successful. You can now log in.'
    ];
} else {
    $response['message'] = 'Error registering user: ' . $stmt->error;
}

$stmt->close();
$hrisMysqli->close();
$bomMysqli->close();

echo json_encode($response);
