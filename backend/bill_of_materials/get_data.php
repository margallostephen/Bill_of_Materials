<?php
require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

$query = "
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
        d.PART_SURROGATE,
        m.MATERIAL_SURROGATE,
        d.MATERIAL_SURROGATE,

        -- Weight + CT (pivoted)
        w.PROD_QT, w.S_R_QT, w.TOTAL_QT, w.G_PCS_QT, w.C_TIME_QT,
        w.PROD_AT, w.S_R_AT, w.TOTAL_AT, w.G_PCS_AT, w.C_TIME_AT,
        w.PROD_AP, w.S_R_AP, w.TOTAL_AP, w.G_PCS_AP, w.C_TIME_AP,

        -- Machines (pivoted)
        mc.MC_1, mc.TON_1,
        mc.MC_2, mc.TON_2,
        mc.MC_3, mc.TON_3,
        mc.MC_4, mc.TON_4,
        mc.MC_5, mc.TON_5,
        mc.MC_1_AP_4M, mc.TON_1_AP_4M,
        mc.MC_2_AP_4M, mc.TON_2_AP_4M

    FROM details_tb d
    LEFT JOIN part_tb p
        ON d.PART_SURROGATE = p.PART_SURROGATE
    LEFT JOIN material_tb m 
        ON d.MATERIAL_SURROGATE = m.MATERIAL_SURROGATE

    -- Pivot Weight + CT
    LEFT JOIN (
        SELECT 
            CASE WHEN ROW_TYPE = 0 THEN PART_SURROGATE ELSE MATERIAL_SURROGATE END AS SURROGATE,
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(PROD_G, 0), '') END) AS PROD_QT,
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(S_R_G, 0), '') END) AS S_R_QT,
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(TOTAL, 0), '') END) AS TOTAL_QT,
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(G_PCS, 0), '') END) AS G_PCS_QT,
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(C_TIME, 0), '') END) AS C_TIME_QT,

            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(PROD_G, 0), '') END) AS PROD_AT,
            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(S_R_G, 0), '') END) AS S_R_AT,
            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(TOTAL, 0), '') END) AS TOTAL_AT,
            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(G_PCS, 0), '') END) AS G_PCS_AT,
            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(C_TIME, 0), '') END) AS C_TIME_AT,

            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(PROD_G, 0), '') END) AS PROD_AP,
            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(S_R_G, 0), '') END) AS S_R_AP,
            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(TOTAL, 0), '') END) AS TOTAL_AP,
            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(G_PCS, 0), '') END) AS G_PCS_AP,
            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(C_TIME, 0), '') END) AS C_TIME_AP
        FROM weight_ct_tb
        GROUP BY SURROGATE
        ORDER BY RID ASC
    ) w 
        ON (d.ROW_TYPE = 0 AND d.PART_SURROGATE = w.SURROGATE)
        OR (d.ROW_TYPE = 1 AND d.MATERIAL_SURROGATE = w.SURROGATE)

    -- Pivot Machines
    LEFT JOIN (
        SELECT 
            CASE WHEN ROW_TYPE = 0 THEN PART_SURROGATE ELSE MATERIAL_SURROGATE END AS SURROGATE,
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
        FROM mc_tb
        GROUP BY SURROGATE
        ORDER BY RID ASC
    ) mc 
        ON (d.ROW_TYPE = 0 AND d.PART_SURROGATE = mc.SURROGATE)
        OR (d.ROW_TYPE = 1 AND d.MATERIAL_SURROGATE = mc.SURROGATE)

    ORDER BY d.RID ASC, CUSTOMER DESC
";

$result = $bomMysqli->query($query);

$bomList = [];
$currentIndex = -1;
$currentCustomer = null;
$prevCustomer = "";

while ($row = $result->fetch_assoc()) {
    $customer = $row['CUSTOMER'];

    $customer = $customer == "" ? $prevCustomer : $customer;

    if (!isset($bomList[$customer])) {
        $bomList[$customer] = [];
        $currentIndex = -1;
    }

    if ($row['DIVISION'] == 1) {
        $currentIndex++;
        $bomList[$customer][$currentIndex] = array_merge($row, ['children' => []]);
    } else {
        if ($currentIndex >= 0) {
            $bomList[$customer][$currentIndex]['children'][] = $row;
        }
    }

    $prevCustomer = $customer;
}

echo json_encode([
    'success' => true,
    'bomList' => $bomList
], JSON_THROW_ON_ERROR);

$bomMysqli->close();
