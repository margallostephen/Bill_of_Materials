<?php
require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

$detailsQuery = "
SELECT 
    d.RID,
    d.DIVISION,
    p.CUSTOMER,
    CASE WHEN d.ROW_TYPE = 0 THEN p.MODEL ELSE m.MODEL END AS MODEL,
    p.PART_CODE,
    CASE WHEN d.ROW_TYPE = 0 THEN p.ERP_CODE ELSE m.ERP_CODE END AS ERP_CODE,
    CASE WHEN d.ROW_TYPE = 0 THEN p.PART_CODE ELSE m.MATERIAL_CODE END AS CODE,
    CASE WHEN d.ROW_TYPE = 0 THEN p.PART_NAME ELSE m.MATERIAL_NAME END AS DESCRIPTION,
    d.PROCESS,
    d.CLASS,
    d.SUPPLIER,
    d.QTY,
    d.UNIT,
    d.STATUS,
    d.CAV_NUM,
    d.TOOL_NUM,
    d.BARCODE,
    d.LABEL_CUSTOMER,
    p.PART_SURROGATE,
    m.MATERIAL_SURROGATE,
    u.USERNAME AS REGISTERED_BY,
    CASE WHEN d.ROW_TYPE = 0 THEN p.CREATED_AT ELSE m.CREATED_AT END AS REGISTERED_DATE,
    p.RID AS PART_ID,
    m.RID AS MATERIAL_ID,
    d.ROW_TYPE
FROM details_tb d
LEFT JOIN part_tb p ON d.PART_SURROGATE = p.PART_SURROGATE
LEFT JOIN material_tb m ON d.MATERIAL_SURROGATE = m.MATERIAL_SURROGATE
LEFT JOIN user_tb u ON (d.ROW_TYPE = 0 AND p.CREATED_BY = u.RFID) OR (d.ROW_TYPE = 1 AND m.CREATED_BY = u.RFID)
WHERE (d.ROW_TYPE = 0 AND p.DELETE_STATUS = 0) OR (d.ROW_TYPE = 1 AND m.DELETE_STATUS = 0)
ORDER BY PART_ID ASC, MATERIAL_ID ASC
";
$detailsResult = $bomMysqli->query($detailsQuery);
if (!$detailsResult) {
    die("SQL Error: " . $bomMysqli->error);
}
$detailsList = $detailsResult->fetch_all(MYSQLI_ASSOC);

$weightQuery = "
SELECT 
    SURROGATE,
    MAX(CASE WHEN COL_TYPE = 0 THEN RID END) AS RID_QT,
    MAX(CASE WHEN COL_TYPE = 1 THEN RID END) AS RID_AT,
    MAX(CASE WHEN COL_TYPE = 2 THEN RID END) AS RID_AP,
    MAX(CASE WHEN COL_TYPE = 0 THEN PROD_G END) AS PROD_QT,
    MAX(CASE WHEN COL_TYPE = 0 THEN S_R_G END) AS S_R_QT,
    MAX(CASE WHEN COL_TYPE = 0 THEN TOTAL END) AS TOTAL_QT,
    MAX(CASE WHEN COL_TYPE = 0 THEN G_PCS END) AS G_PCS_QT,
    MAX(CASE WHEN COL_TYPE = 0 THEN C_TIME END) AS C_TIME_QT,
    MAX(CASE WHEN COL_TYPE = 1 THEN PROD_G END) AS PROD_AT,
    MAX(CASE WHEN COL_TYPE = 1 THEN S_R_G END) AS S_R_AT,
    MAX(CASE WHEN COL_TYPE = 1 THEN TOTAL END) AS TOTAL_AT,
    MAX(CASE WHEN COL_TYPE = 1 THEN G_PCS END) AS G_PCS_AT,
    MAX(CASE WHEN COL_TYPE = 1 THEN C_TIME END) AS C_TIME_AT,
    MAX(CASE WHEN COL_TYPE = 2 THEN PROD_G END) AS PROD_AP,
    MAX(CASE WHEN COL_TYPE = 2 THEN S_R_G END) AS S_R_AP,
    MAX(CASE WHEN COL_TYPE = 2 THEN TOTAL END) AS TOTAL_AP,
    MAX(CASE WHEN COL_TYPE = 2 THEN G_PCS END) AS G_PCS_AP,
    MAX(CASE WHEN COL_TYPE = 2 THEN C_TIME END) AS C_TIME_AP
FROM (
    SELECT *, CASE WHEN ROW_TYPE = 0 THEN PART_SURROGATE ELSE MATERIAL_SURROGATE END AS SURROGATE
    FROM weight_ct_tb
) w
GROUP BY SURROGATE
";
$weightResult = $bomMysqli->query($weightQuery);
$weightList = [];
while ($row = $weightResult->fetch_assoc()) {
    $weightList[$row['SURROGATE']] = $row;
}

$machineQuery = "
SELECT 
    SURROGATE,
    MAX(CASE WHEN COL_TYPE=0 THEN RID END) AS RID_MC_1,
    MAX(CASE WHEN COL_TYPE=1 THEN RID END) AS RID_MC_2,
    MAX(CASE WHEN COL_TYPE=2 THEN RID END) AS RID_MC_3,
    MAX(CASE WHEN COL_TYPE=3 THEN RID END) AS RID_MC_4,
    MAX(CASE WHEN COL_TYPE=4 THEN RID END) AS RID_MC_5,
    MAX(CASE WHEN COL_TYPE=5 THEN RID END) AS RID_MC_1_AP_4M,
    MAX(CASE WHEN COL_TYPE=6 THEN RID END) AS RID_MC_2_AP_4M,
    MAX(CASE WHEN COL_TYPE=0 THEN MC END) AS MC_1,
    MAX(CASE WHEN COL_TYPE=0 THEN TON END) AS TON_1,
    MAX(CASE WHEN COL_TYPE=1 THEN MC END) AS MC_2,
    MAX(CASE WHEN COL_TYPE=1 THEN TON END) AS TON_2,
    MAX(CASE WHEN COL_TYPE=2 THEN MC END) AS MC_3,
    MAX(CASE WHEN COL_TYPE=2 THEN TON END) AS TON_3,
    MAX(CASE WHEN COL_TYPE=3 THEN MC END) AS MC_4,
    MAX(CASE WHEN COL_TYPE=3 THEN TON END) AS TON_4,
    MAX(CASE WHEN COL_TYPE=4 THEN MC END) AS MC_5,
    MAX(CASE WHEN COL_TYPE=4 THEN TON END) AS TON_5,
    MAX(CASE WHEN COL_TYPE=5 THEN MC END) AS MC_1_AP_4M,
    MAX(CASE WHEN COL_TYPE=5 THEN TON END) AS TON_1_AP_4M,
    MAX(CASE WHEN COL_TYPE=6 THEN MC END) AS MC_2_AP_4M,
    MAX(CASE WHEN COL_TYPE=6 THEN TON END) AS TON_2_AP_4M
FROM (
    SELECT *, CASE WHEN ROW_TYPE = 0 THEN PART_SURROGATE ELSE MATERIAL_SURROGATE END AS SURROGATE
    FROM mc_tb
) m
GROUP BY SURROGATE
";
$machineResult = $bomMysqli->query($machineQuery);
$machineList = [];
while ($row = $machineResult->fetch_assoc()) {
    $machineList[$row['SURROGATE']] = $row;
}

$bomList = [];

foreach ($detailsList as $row) {
    $customer = $row['CUSTOMER'];
    $partSurrogate = $row['PART_SURROGATE']; 
    $childSurrogate = $row['ROW_TYPE'] == 0 ? $row['PART_SURROGATE'] : $row['MATERIAL_SURROGATE'];

    if (isset($weightList[$childSurrogate])) {
        $row = array_merge($row, $weightList[$childSurrogate]);
    }
    if (isset($machineList[$childSurrogate])) {
        $row = array_merge($row, $machineList[$childSurrogate]);
    }

    if (!isset($bomList[$customer])) {
        $bomList[$customer] = [];
    }

    if ($row['DIVISION'] == 1 && $row['ROW_TYPE'] == 0) {
        if (!isset($bomList[$customer][$partSurrogate])) {
            $bomList[$customer][$partSurrogate] = array_merge($row, ['_children' => []]);
        }
    } else {
        if (isset($bomList[$customer][$partSurrogate])) {
            $bomList[$customer][$partSurrogate]['_children'][] = $row;
        }
    }
}

echo json_encode([
    'success' => true,
    'bomList' => $bomList
], JSON_THROW_ON_ERROR);

$bomMysqli->close();
