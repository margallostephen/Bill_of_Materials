<?php
date_default_timezone_set(timezoneId: 'Asia/Manila');
require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_UTILS_PATH . 'getIPAddress.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

$data = $_POST['items'] ?? [];

function allDataEmpty(array $arr): bool
{
    foreach ($arr as $v) {
        if (!is_array($v) && trim((string)$v) !== '') {
            return false;
        }
    }
    return true;
}

function fieldsEmpty(array $arr, array $keys): array
{
    $missing = [];
    foreach ($keys as $key) {
        if (!isset($arr[$key]) || trim((string)$arr[$key]) === '') {
            $missing[] = $key;
        }
    }
    return $missing;
}

function insertRowAllData($itemData, $rowData, $partSurrogate, $partIndex, $mainStmt, $detailsStmt, $weightCtStmt, $mcStmt, $userRfid, $userIp, $createdAt, $type = 0, $materialSurrogate = null, $materialKey = null)
{
    $division = $type == 0 ? 1 : trim($rowData['division']);
    $customer = trim($itemData[0]);
    $model = trim($itemData[1]);
    $partCode = trim($itemData[2]);
    $erpCode = trim($rowData['erp_code']);
    $code = $type == 0 ? $partCode : trim($rowData['part_code']);
    $description = trim($rowData['part_name']);
    $process = trim($rowData['process']);
    $class = trim($rowData['class']);
    $supplier = trim($rowData['supplier']);
    $qty = trim($rowData['qty']);
    $unit = trim($rowData['unit']);
    $status = trim($rowData['status']);
    $cavNum = trim($rowData['cavity']);
    $toolNum = trim($rowData['tool']);
    $barcode = trim($rowData['barcode']);
    $labelCustomer = trim($rowData['label_customer']);

    if ($type == 0) {
        $mainStmt->bind_param(
            "ssssssisss",
            $partCode,
            $customer,
            $model,
            $erpCode,
            $description,
            $partSurrogate,
            $partIndex,
            $userRfid,
            $userIp,
            $createdAt
        );
    } else {
        $mainStmt->bind_param(
            "ssisssssss",
            $partSurrogate,
            $materialSurrogate,
            $materialKey,
            $model,
            $erpCode,
            $code,
            $description,
            $userRfid,
            $userIp,
            $createdAt
        );
    }

    $mainStmt->execute();

    $codeVal = $type == 0 ? $partCode : $code;

    $detailsStmt->bind_param(
        "issssssisssissssss",
        $division,
        $process,
        $class,
        $supplier,
        $qty,
        $unit,
        $status,
        $cavNum,
        $toolNum,
        $barcode,
        $labelCustomer,
        $type,
        $codeVal,
        $materialSurrogate,
        $partSurrogate,
        $userRfid,
        $userIp,
        $createdAt
    );

    $detailsStmt->execute();

    foreach ($rowData['weight_ct'] as $index => $weight_ct) {
        $prod = (float) trim($weight_ct['prod_g']);
        $sr = (float) trim($weight_ct['sr_g']);
        $total = (float) trim($weight_ct['total_g']);
        $gpcs = (float) trim($weight_ct['gpcs']);
        $ctime = (float) trim($weight_ct['ctime']);

        if (!empty($prod) || !empty($sr) || !empty($total) || !empty($gpcs) || !empty($ctime)) {
            $weightCtStmt->bind_param(
                "dddddsssisss",
                $prod,
                $sr,
                $total,
                $gpcs,
                $ctime,
                $partSurrogate,
                $materialSurrogate,
                $type,
                $index,
                $userRfid,
                $userIp,
                $createdAt
            );

            $weightCtStmt->execute();
        }
    }

    foreach ($rowData['mc'] as $index => $mcDetails) {
        $approved = $index > 4 ? 1 : 0;
        $mc = trim($mcDetails['num']);
        $ton = trim($mcDetails['ton']);

        if (!empty($mc) || !empty($ton)) {
            $mcStmt->bind_param(
                "ssisisssss",
                $mc,
                $ton,
                $index,
                $type,
                $approved,
                $partSurrogate,
                $materialSurrogate,
                $userRfid,
                $userIp,
                $createdAt
            );

            $mcStmt->execute();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userRfid = $_SESSION['RFID'];
    $userIp = getUserIP();
    $createdAt = $updatedAt = date("Y-m-d H:i:s");
    $requiredItemFields = ['customer', 'master_code', 'part_name'];
    $requiredMaterialFields = ['part_code', 'part_name'];

    $checkDuplicatePart = $bomMysqli->prepare("SELECT 1 FROM part_tb WHERE PART_SURROGATE = ? LIMIT 1");

    $insertPart = $bomMysqli->prepare("
                INSERT INTO part_tb 
                (PART_CODE, CUSTOMER, MODEL, ERP_CODE, PART_NAME, PART_SURROGATE, PART_KEY, CREATED_BY, CREATED_IP, CREATED_AT)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

    $insertMaterial = $bomMysqli->prepare("
                INSERT INTO material_tb 
                (PART_SURROGATE, MATERIAL_SURROGATE, MATERIAL_KEY, MODEL, ERP_CODE, MATERIAL_CODE, MATERIAL_NAME, CREATED_BY, CREATED_IP, CREATED_AT)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

    $insertDetails = $bomMysqli->prepare("
                INSERT INTO details_tb (
                    DIVISION, PROCESS, CLASS, SUPPLIER, QTY, UNIT, STATUS, CAV_NUM, TOOL_NUM, BARCODE, LABEL_CUSTOMER, ROW_TYPE, CODE, MATERIAL_SURROGATE, PART_SURROGATE,CREATED_BY, CREATED_IP, CREATED_AT
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

    $insertWeightCt = $bomMysqli->prepare("
                INSERT INTO weight_ct_tb (
                    PROD_G, S_R_G, TOTAL, G_PCS, C_TIME, PART_SURROGATE, MATERIAL_SURROGATE, ROW_TYPE, COL_TYPE, CREATED_BY, CREATED_IP, CREATED_AT
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

    $insertMc = $bomMysqli->prepare("
                INSERT INTO mc_tb (
                    MC, TON, COL_TYPE, ROW_TYPE, APPROVED, PART_SURROGATE, MATERIAL_SURROGATE, CREATED_BY, CREATED_IP, CREATED_AT
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

    $itemIndexWarningLabel = 1;
    foreach ($data as $index => $item) {

        if (allDataEmpty($item)) {
            echo json_encode([
                "status" => false,
                "message" => "All fields are empty for item#" . $itemIndexWarningLabel
            ]);
            exit;
        }

        $missingParent = fieldsEmpty($item, $requiredItemFields);
        if (count($missingParent) > 0) {
            echo json_encode([
                "status" => false,
                "message" => "Missing required parent fields for item#" . $itemIndexWarningLabel . ": " . implode(', ', $missingParent)
            ]);
            exit;
        }

        $materialIndex = 1;
        $materials = $item['material'] ?? [];
        $materials = array_filter($materials, fn($m) => !allDataEmpty($m));
        foreach ($materials as $matIndex => $mat) {
            $missingMat = fieldsEmpty($mat, $requiredMaterialFields);
            if (count($missingMat) > 0) {
                echo json_encode([
                    "status" => false,
                    "message" => "Missing required material fields for item#" . $itemIndexWarningLabel . ", material#" . $materialIndex . ": " . implode(', ', $missingMat)
                ]);
                exit;
            }
            $materialIndex++;
        }

        if (count($materials) === 0) {
            echo json_encode([
                "status" => false,
                "message" => "No material added for item#" . $itemIndexWarningLabel
            ]);
            exit;
        }

        $partSurrogate = "";

        $partIndex = 1;
        do {
            $partSurrogate = implode('_', array_filter([
                $item['master_code'],
                $item['part_name'],
                $item['tool'],
                $partIndex
            ]));

            $checkDuplicatePart->bind_param("s", $partSurrogate);
            $checkDuplicatePart->execute();
            $checkDuplicatePart->store_result();

            $exists = $checkDuplicatePart->num_rows > 0;
            if ($exists) {
                $partIndex++;
            }
        } while ($exists);

        $itemGlobalData = [$item['customer'], $item['model'], $item['master_code']];

        insertRowAllData($itemGlobalData, $item, $partSurrogate, $partIndex, $insertPart, $insertDetails, $insertWeightCt, $insertMc, $userRfid, $userIp, $createdAt);

        $materialKey = 1;
        foreach ($item['material'] as $material) {
            $materialSurrogate = implode('_', array_filter([
                $partSurrogate,
                $material['part_name'],
                $materialKey
            ]));

            insertRowAllData($itemGlobalData, $material, $partSurrogate, $partIndex, $insertMaterial, $insertDetails, $insertWeightCt, $insertMc, $userRfid, $userIp, $createdAt, 1, $materialSurrogate, $materialKey);

            $materialKey++;
        }

        $itemIndexWarningLabel++;
    }

    $checkDuplicatePart->close();
    $insertPart->close();
    $insertMaterial->close();
    $insertDetails->close();
    $insertWeightCt->close();
    $insertMc->close();

    echo json_encode([
        "status" => true,
        "message" => "Items are valid",
        "data" => $data
    ]);
}
