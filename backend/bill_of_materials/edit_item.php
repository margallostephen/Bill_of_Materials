<?php
date_default_timezone_set('Asia/Manila');
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

    if ($previousValue == $newValue) {
        echo json_encode([
            "status" => false,
            "message" => "New value is same as the previous value."
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

    if ($rowType === 0 && in_array($columnField, ['PART_CODE', 'PART_NAME', 'TOOL_NUM'], true)) {
        $getSql = "SELECT 
                p.RID,
                p.PART_CODE,
                p.PART_NAME, 
                d.TOOL_NUM,
                p.PART_KEY
            FROM part_tb AS p
            INNER JOIN details_tb AS d 
                ON p.PART_SURROGATE = d.PART_SURROGATE
            WHERE 
                d.ROW_TYPE = 0
                AND p.PART_SURROGATE = ?";

        $getPartDetails = $bomMysqli->prepare($getSql);
        $getPartDetails->bind_param("s", $partSurrogate);
        $getPartDetails->execute();

        $partInfo = $getPartDetails->get_result()->fetch_assoc();
        $getPartDetails->close();

        $partId = $partInfo['RID'];
        $partCode = $partInfo['PART_CODE'];
        $partName = $partInfo['PART_NAME'];
        $toolNum = $partInfo['TOOL_NUM'];
        $partKey = $partInfo['PART_KEY'];

        switch ($columnField) {
            case 'PART_CODE':
                $partCode = $newValue;
                break;
            case 'PART_NAME':
                $partName = $newValue;
                break;
            case 'TOOL_NUM':
                $toolNum = $newValue;
                break;
        }

        $newPartSurrogate = "{$partCode}_{$partName}_{$toolNum}_{$partKey}";
    }

    if ($rowType === 1 && in_array($columnField, ["MATERIAL_NAME"])) {
        $getSql = "SELECT RID, MATERIAL_NAME, MATERIAL_KEY, PART_SURROGATE 
            FROM material_tb WHERE MATERIAL_SURROGATE = ?";

        $getMaterialDetails = $bomMysqli->prepare($getSql);
        $getMaterialDetails->bind_param("s", $materialSurrogate);
        $getMaterialDetails->execute();

        $materialInfo = $getMaterialDetails->get_result()->fetch_assoc();
        $getMaterialDetails->close();

        $materialId = $materialInfo['RID'];
        $materialName = $newValue ?? $materialInfo['MATERIAL_NAME'];
        $materialKey = $materialInfo['MATERIAL_KEY'];
        $materialPartSurrogate = $materialInfo['PART_SURROGATE'];

        $newMaterialSurrogate = "{$materialPartSurrogate}|{$materialName}_{$materialKey}";
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

    if ($rowType === 0 && in_array($columnField, ['PART_CODE', 'PART_NAME', 'TOOL_NUM'], true)) {
        $insertSql = "INSERT INTO prev_part_history_tb (
            `PART_CODE`, `PART_NAME`, `TOOL_NUM`, `PART_KEY`, `PART_SURROGATE`, 
            `CREATED_BY`, `CREATED_IP`, `CREATED_AT`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $insertDetails = $bomMysqli->prepare($insertSql);
        $insertDetails->bind_param(
            "sssissss",
            $partInfo["PART_CODE"],
            $partInfo["PART_NAME"],
            $partInfo["TOOL_NUM"],
            $partInfo["PART_KEY"],
            $partSurrogate,
            $userRfid,
            $userIp,
            $createdAt
        );
        $insertDetails->execute();
        $insertDetails->close();


        $getMaterialSql = "SELECT RID, MATERIAL_SURROGATE 
            FROM MATERIAL_TB 
            WHERE PART_SURROGATE = ?";
        $getMaterials = $bomMysqli->prepare($getMaterialSql);
        $getMaterials->bind_param("s", $partSurrogate);
        $getMaterials->execute();
        $materialSurrogates = $getMaterials->get_result()->fetch_all(MYSQLI_ASSOC);
        $getMaterials->close();

        if (!empty($materialSurrogates)) {
            $caseSql = "UPDATE MATERIAL_TB SET MATERIAL_SURROGATE = CASE RID ";
            $ids = [];

            foreach ($materialSurrogates as $row) {
                $suffix = explode('|', $row['MATERIAL_SURROGATE'])[1] ?? '';
                $newMatSurrogate = "{$newPartSurrogate}|{$suffix}";
                $escapedMat = $bomMysqli->real_escape_string($newMatSurrogate);
                $caseSql .= "WHEN {$row['RID']} THEN '{$escapedMat}' ";
                $ids[] = $row['RID'];
            }

            $caseSql .= "END WHERE RID IN (" . implode(',', $ids) . ")";
            $bomMysqli->query($caseSql);
        }

        $updatePartSurrogateSql = "UPDATE part_tb SET PART_SURROGATE = ? WHERE RID = ?";
        $updatePartSurrogate = $bomMysqli->prepare($updatePartSurrogateSql);
        $updatePartSurrogate->bind_param("si", $newPartSurrogate, $partId);
        $updatePartSurrogate->execute();
        $updatePartSurrogate->close();
    }

    if ($rowType === 1 && in_array($columnField, ["MATERIAL_NAME"])) {
        $updateMaterialSurrogateSql = "UPDATE material_tb SET MATERIAL_SURROGATE = ? WHERE RID = ?";
        $updateMaterialSurrogate = $bomMysqli->prepare($updateMaterialSurrogateSql);
        $updateMaterialSurrogate->bind_param("si", $newMaterialSurrogate, $materialId);
        $updateMaterialSurrogate->execute();
        $updateMaterialSurrogate->close();
    }

    echo json_encode([
        "status" => true,
        "message" => "Successfully edited the info"
    ]);
    exit;
}
