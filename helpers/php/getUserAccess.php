<?php

function getUserAccess($bomMysqli)
{
    $rfid = $_SESSION['RFID'] ?? null;
    if (!$rfid) return [];

    $sql = "
        SELECT 
            m.MODULE_NAME,
            a.ACTION_NAME,
            COALESCE(ua.IS_ALLOWED, 0) AS IS_ALLOWED
        FROM modules_tb m
        CROSS JOIN actions_tb a
        LEFT JOIN user_access_tb ua 
            ON ua.MODULE_RID = m.RID 
            AND ua.ACTION_RID = a.RID 
            AND ua.USER_RFID = ?
        ORDER BY m.MODULE_NAME, a.ACTION_NAME
    ";

    $stmt = $bomMysqli->prepare($sql);
    $stmt->bind_param("i", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    $formatted = [];
    while ($row = $result->fetch_assoc()) {
        $module = $row['MODULE_NAME'];
        $action = $row['ACTION_NAME'];
        $formatted[$module][$action] = (int)$row['IS_ALLOWED']; // Always 0 or 1
    }

    return $formatted;
}
