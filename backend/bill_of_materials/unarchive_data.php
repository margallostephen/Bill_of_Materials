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
    $createdAt  = $updatedAt = date("Y-m-d H:i:s");

    $rowType = strtolower($_POST["type"]);
    $rowData = $_POST["row_data"];
    $rowId   = $rowType == "part" ? $rowData["p_id"] : $rowData["m_id"];

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
                SET delete_status = 0, 
                    UPDATED_BY = ?, 
                    UPDATED_IP = ?, 
                    UPDATED_AT = ? 
                WHERE $column = ?";

        $stmt = $bomMysqli->prepare($sql);
        $stmt->bind_param("ssss", $userRfid, $userIp, $updatedAt, $surrogate);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => true,
                "message" => ucfirst($rowType) . " unarchived successfully."
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Failed to unarchive $rowType."
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
