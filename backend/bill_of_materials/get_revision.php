<?php
require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

$sql = "
    SELECT r.RID, r.DATA_TYPE, r.PREV_VAL, r.NEW_VAL, r.REMARKS, u.USERNAME AS REVISED_BY, r.REVISED_AT
    FROM `revision_tb` AS r
    INNER JOIN `user_tb` AS u
        ON r.REVISED_BY = U.RFID
";

$getRevisionStmt = $bomMysqli->prepare($sql);
$getRevisionStmt->execute();
$result = $getRevisionStmt->get_result();

$response = ($result && $result->num_rows > 0)
    ? [
        'status' => true,
        'revisionList' => $result->fetch_all(MYSQLI_ASSOC)
    ] : [
        'status' => false,
        'message' => "No revisions found."
    ];

$getRevisionStmt->close();
$bomMysqli->close();
echo json_encode($response);
