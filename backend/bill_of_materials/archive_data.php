<?php
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_UTILS_PATH . 'getIPAddress.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userRfid   = $_SESSION['RFID'];
    $userIp     = getUserIP();
    $createdAt  = $deletedAt = date("Y-m-d H:i:s");

    $rowType = strtolower($_POST["type"]);
    $rowId   = (int) $_POST["data_id"];

    $allowedTypes = ['part', 'material'];
    if (!in_array($rowType, $allowedTypes)) {
        echo json_encode(["status" => false, "message" => "Invalid type"]);
        exit;
    }

    $table  = $rowType . "_tb";
    $column = strtoupper($rowType) . "_SURROGATE";

    $getSurrogateSql = "SELECT $column FROM $table WHERE RID = ?";
    $getSurrogateStmt = $bomMysqli->prepare($getSurrogateSql);
    $getSurrogateStmt->bind_param("i", $rowId);
    $getSurrogateStmt->execute();
    $result = $getSurrogateStmt->get_result();
    $row = $result->fetch_assoc();
    $surrogate = $row[$column] ?? null;
    $getSurrogateStmt->close();

    if ($surrogate) {
        $sql = "UPDATE $table 
                SET delete_status = 1, 
                    DELETED_BY = ?, 
                    DELETED_IP = ?, 
                    DELETED_AT = ? 
                WHERE $column = ?";

        $stmt = $bomMysqli->prepare($sql);
        $stmt->bind_param("ssss", $userRfid, $userIp, $deletedAt, $surrogate);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => true,
                "message" => ucfirst($rowType) . " archived successfully."
            ]);

            if ($rowType == "part") {
                $sql = "UPDATE material_tb 
                SET DELETE_STATUS = 1, 
                    DELETED_BY = ?, 
                    DELETED_IP = ?, 
                    DELETED_AT = ? 
                WHERE $column = ?";

                $stmt = $bomMysqli->prepare($sql);
                $stmt->bind_param("ssss", $userRfid, $userIp, $deletedAt, $surrogate);
            }
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Failed to archive $rowType."
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Record not found."
        ]);
    }

    $bomMysqli->close();
}
