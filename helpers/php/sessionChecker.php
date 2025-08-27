<?php
session_start();

$timeout = 1500;

if (
    !isset($_SESSION['RFID']) ||
    (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > $timeout)
) {
    session_unset();
    session_destroy();
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'invalid',
        'message' => 'Session expired. Please log in again.'
    ]);
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();
