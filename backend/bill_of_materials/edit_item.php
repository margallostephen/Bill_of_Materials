<?php
date_default_timezone_set(timezoneId: 'Asia/Manila');
require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_UTILS_PATH . 'getIPAddress.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = trim($_POST["table"]);
    $columnTitle = trim($_POST["columnTitle"]);
    $columnField = trim($_POST["columnField"]);
    $columnType = (int) ($_POST["columnType"]);
    $rowType = trim($_POST["rowType"]) == "true" ? 0 : 1;
    $rowId = (int) trim($_POST["rowId"]);
    $previousValue = trim($_POST["previousValue"]);
    $newValue = trim($_POST["newValue"]);
    $remarks = trim($_POST["remarks"]);
    $partSurrogate = trim($_POST["partSurrogate"]);
    $materialSurrogate = trim($_POST["materialSurrogate"]);

    if (empty($newValue)) {
        echo json_encode([
            "status" => false,
            "message" => "Please input a new value"
        ]);
        exit;
    }

    $userRfid = $_SESSION['RFID'];
    $userIp = getUserIP();
    $createdAt = $updatedAt = date("Y-m-d H:i:s");
    $tables = ["part", "material", "details", "weight_ct", "mc"];

    if (in_array($table, $tables, true)) {
        $selectedTable = $table . '_tb';
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Invalid table"
        ]);
        exit;
    }

    if (!empty($rowId)) {
        $sql = "UPDATE $selectedTable SET $columnField = ? WHERE RID = ?";

        $updateItemStmt = $bomMysqli->prepare($sql);
        $updateItemStmt->bind_param("si", $newValue, $rowId);
        $updateItemStmt->execute();
        $updateItemStmt->close();
    } else {
        $isMcTbl  = $selectedTable === "mc_tb";

        $cols = ["`PART_SURROGATE`"];
        $vals = ["?"];
        $types = "s";
        $params = [$partSurrogate];

        if ($rowType) {
            $cols[] = "`MATERIAL_SURROGATE`";
            $vals[] = "?";
            $types .= "s";
            $params[] = $materialSurrogate;
        }

        $cols = array_merge($cols, [
            "`ROW_TYPE`",
            "`COL_TYPE`",
            "`CREATED_BY`",
            "`CREATED_IP`",
            "`CREATED_AT`"
        ]);
        $vals = array_merge($vals, ["?", "?", "?", "?", "?"]);
        $types .= "iisss";
        $params = array_merge($params, [$rowType, $columnType, $userRfid, $userIp, $createdAt]);

        if ($isMcTbl) {
            $cols[] = "`APPROVED`";
            $vals[] = "?";
            $types .= "i";
            $params[] = $columnType > 4 ? 1 : 0;
        }

        $cols[] = "`$columnField`";
        $vals[] = "?";
        $types .= "s";
        $params[] = $newValue;

        $sql = "INSERT INTO $selectedTable (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ")";
        $insertItemInfo = $bomMysqli->prepare($sql);
        $insertItemInfo->bind_param($types, ...$params);
        $insertItemInfo->execute();
        $insertItemInfo->close();
    }

    $sql = "INSERT INTO revision_tb ";

    $surrogateType = $rowType ? "MATERIAL" : "PART";
    $surrogateType .= "_SURROGATE";
    $surrogate = $rowType ? $materialSurrogate : $partSurrogate;

    $sql .= "($surrogateType, DATA_TYPE, PREV_VAL, NEW_VAL, REMARKS, REVISED_BY, REVISED_AT, REVISED_IP) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertRevisionDetails = $bomMysqli->prepare($sql);
    $insertRevisionDetails->bind_param("ssssssss", $surrogate, $columnTitle, $previousValue, $newValue, $remarks, $userRfid, $updatedAt, $userIp);
    $insertRevisionDetails->execute();
    $insertRevisionDetails->close();

    echo json_encode([
        "status" => true,
        "message" => "Successfully edited the info"
    ]);
    exit;
}
