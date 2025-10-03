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
        m.MATERIAL_SURROGATE,
        u.USERNAME AS REGISTERED_BY,
        CASE WHEN d.ROW_TYPE = 0 THEN p.CREATED_AT ELSE m.CREATED_AT END AS REGISTERED_DATE,
        p.RID AS PART_ID,
        m.RID AS MATERIAL_ID,

        -- Weight + CT (pivoted)
        w.RID_QT, w.PROD_QT, w.S_R_QT, w.TOTAL_QT, w.G_PCS_QT, w.C_TIME_QT,
        w.RID_AT, w.PROD_AT, w.S_R_AT, w.TOTAL_AT, w.G_PCS_AT, w.C_TIME_AT,
        w.RID_AP, w.PROD_AP, w.S_R_AP, w.TOTAL_AP, w.G_PCS_AP, w.C_TIME_AP,

        -- Machines (pivoted)
        mc.RID_MC_1, mc.MC_1, mc.TON_1,
        mc.RID_MC_2, mc.MC_2, mc.TON_2,
        mc.RID_MC_3, mc.MC_3, mc.TON_3,
        mc.RID_MC_4, mc.MC_4, mc.TON_4,
        mc.RID_MC_5, mc.MC_5, mc.TON_5,
        mc.RID_MC_1_AP_4M, mc.MC_1_AP_4M, mc.TON_1_AP_4M,
        mc.RID_MC_2_AP_4M, mc.MC_2_AP_4M, mc.TON_2_AP_4M

    FROM details_tb d
    LEFT JOIN part_tb p
        ON d.PART_SURROGATE = p.PART_SURROGATE
    LEFT JOIN material_tb m 
        ON d.MATERIAL_SURROGATE = m.MATERIAL_SURROGATE
    LEFT JOIN user_tb u 
        ON (d.ROW_TYPE = 0 AND p.CREATED_BY = u.RFID)
        OR (d.ROW_TYPE = 1 AND m.CREATED_BY = u.RFID)

    -- Pivot Weight + CT
    LEFT JOIN (
        SELECT 
            CASE WHEN ROW_TYPE = 0 THEN PART_SURROGATE ELSE MATERIAL_SURROGATE END AS SURROGATE,

            -- RID per COL_TYPE
            MAX(CASE WHEN COL_TYPE = 0 THEN RID END) AS RID_QT,
            MAX(CASE WHEN COL_TYPE = 1 THEN RID END) AS RID_AT,
            MAX(CASE WHEN COL_TYPE = 2 THEN RID END) AS RID_AP,

            -- Values for QT
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(PROD_G, 0), '') END) AS PROD_QT,
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(S_R_G, 0), '') END) AS S_R_QT,
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(TOTAL, 0), '') END) AS TOTAL_QT,
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(G_PCS, 0), '') END) AS G_PCS_QT,
            MAX(CASE WHEN COL_TYPE = 0 THEN COALESCE(NULLIF(C_TIME, 0), '') END) AS C_TIME_QT,

            -- Values for AT
            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(PROD_G, 0), '') END) AS PROD_AT,
            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(S_R_G, 0), '') END) AS S_R_AT,
            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(TOTAL, 0), '') END) AS TOTAL_AT,
            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(G_PCS, 0), '') END) AS G_PCS_AT,
            MAX(CASE WHEN COL_TYPE = 1 THEN COALESCE(NULLIF(C_TIME, 0), '') END) AS C_TIME_AT,

            -- Values for AP
            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(PROD_G, 0), '') END) AS PROD_AP,
            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(S_R_G, 0), '') END) AS S_R_AP,
            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(TOTAL, 0), '') END) AS TOTAL_AP,
            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(G_PCS, 0), '') END) AS G_PCS_AP,
            MAX(CASE WHEN COL_TYPE = 2 THEN COALESCE(NULLIF(C_TIME, 0), '') END) AS C_TIME_AP
        FROM weight_ct_tb
        GROUP BY CASE WHEN ROW_TYPE = 0 THEN PART_SURROGATE ELSE MATERIAL_SURROGATE END
    ) w 
        ON (d.ROW_TYPE = 0 AND d.PART_SURROGATE = w.SURROGATE)
        OR (d.ROW_TYPE = 1 AND d.MATERIAL_SURROGATE = w.SURROGATE)

    -- Pivot Machines
    LEFT JOIN (
        SELECT 
            CASE WHEN ROW_TYPE = 0 THEN PART_SURROGATE ELSE MATERIAL_SURROGATE END AS SURROGATE,

            -- RIDs per COL_TYPE
            MAX(CASE WHEN COL_TYPE=0 THEN RID END) AS RID_MC_1,
            MAX(CASE WHEN COL_TYPE=1 THEN RID END) AS RID_MC_2,
            MAX(CASE WHEN COL_TYPE=2 THEN RID END) AS RID_MC_3,
            MAX(CASE WHEN COL_TYPE=3 THEN RID END) AS RID_MC_4,
            MAX(CASE WHEN COL_TYPE=4 THEN RID END) AS RID_MC_5,
            MAX(CASE WHEN COL_TYPE=5 THEN RID END) AS RID_MC_1_AP_4M,
            MAX(CASE WHEN COL_TYPE=6 THEN RID END) AS RID_MC_2_AP_4M,

            -- Actual values
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
        GROUP BY CASE WHEN ROW_TYPE = 0 THEN PART_SURROGATE ELSE MATERIAL_SURROGATE END
    ) mc 
        ON (d.ROW_TYPE = 0 AND d.PART_SURROGATE = mc.SURROGATE)
        OR (d.ROW_TYPE = 1 AND d.MATERIAL_SURROGATE = mc.SURROGATE)

    WHERE 
        (d.ROW_TYPE = 0 AND p.DELETE_STATUS = 0)
    OR (d.ROW_TYPE = 1 AND p.DELETE_STATUS = 0 AND m.DELETE_STATUS = 0)

    ORDER BY PART_ID ASC, MATERIAL_ID
";

$result = $bomMysqli->query($query);

$bomList = [];

while ($row = $result->fetch_assoc()) {
    $customer = $row['CUSTOMER'];
    $partSurrogate = $row['PART_SURROGATE'];

    if (!isset($bomList[$customer])) {
        $bomList[$customer] = [];
    }

    if ($row['DIVISION'] == 1) {
        if (!isset($bomList[$customer][$partSurrogate])) {
            $bomList[$customer][$partSurrogate] = array_merge($row, ['_children' => []]);
        }
    } else {
        if (isset($bomList[$customer][$partSurrogate])) {
            $bomList[$customer][$partSurrogate]['_children'][] = $row;
        } else {
            $bomList[$customer][$partSurrogate] = [
                '_placeholder' => true,
                '_children' => [$row],
            ];
        }
    }
}

echo json_encode([
    'success' => true,
    'bomList' => $bomList
], JSON_THROW_ON_ERROR);

$bomMysqli->close();
