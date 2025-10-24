<?php

function getUserAccess($bomMysqli)
{
    $rfid = $_SESSION['RFID'] ?? null;
    if (!$rfid) return [];

    $sql = "
        SELECT 
            m.MODULE_NAME,
            a.ACTION_NAME,
            ua.IS_ALLOWED
        FROM user_access_tb ua
        JOIN modules_tb m ON ua.MODULE_RID = m.RID
        JOIN actions_tb a ON ua.ACTION_RID = a.RID
        WHERE ua.USER_RFID = ?
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
        $formatted[$module][$action] = (bool)$row['IS_ALLOWED'];
    }

    return $formatted;
}
