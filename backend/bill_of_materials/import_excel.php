<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/serverPath.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_UTILS_PATH . 'getIPAddress.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

$response = [
    'status' => false,
    'message' => '',
    'data' => null
];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

        $uploadDir = UPLOADS_DIR . 'excel_imports/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = basename($_FILES['file']['name']);
        $timestamp = date('Ymd_His');
        $savedFilePath = $uploadDir . $timestamp . "_" . $originalName;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $savedFilePath)) {
            throw new Exception('Failed to save uploaded file.');
        }

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($savedFilePath);

        $sheetCount = $spreadsheet->getSheetCount();
        $sheetRowsImported = [];

        for ($i = 0; $i < $sheetCount; $i++) {
            $currentSheet = $spreadsheet->getSheet($i);
            $sheetName = $currentSheet->getTitle();

            if ($sheetName == "REV") {
                continue;
            }

            $rows = $currentSheet->toArray();

            $rows = array_slice($rows, 3);

            $rows = array_filter($rows, function ($row) {
                foreach ($row as $cell) {
                    if (trim((string) $cell) !== '')
                        return true;
                }
                return false;
            });

            // echo json_encode([
            //     'status' => true,
            //     'message' => 'File imported successfully.',
            //     'data' => $rows,
            // ], JSON_PRETTY_PRINT);

            // exit;

            $newItem = true;
            $surrogateKey = '';
            $rowsImported = 0;
            $prevMaterialCode = '';

            foreach ($rows as $row) {
                $division = trim($row[8]);
                $customer = trim($row[9]);
                $model = trim($row[10]);
                $partCode = trim($row[11]);
                $erpCode = trim($row[12]);
                $materialCode = trim($row[13]);
                $materialName = trim($row[14]);
                $process = trim($row[15]);
                $class = trim($row[16]);
                $supplier = trim($row[17]);
                $qty = trim($row[18]);
                $unit = trim($row[19]);
                $status = trim($row[20]);
                $cavNum = trim($row[21]);
                $toolNum = trim($row[22]);
                $barcode = trim($row[23]);

                if ($materialName == 'RUNNER') {
                    $materialCode = $prevMaterialCode . 'RNR';
                }

                if ($division == "1") {
                    $newItem = true;
                    $surrogateKey = $partCode . '_' . $materialCode . '_' . $materialName . '_' . $toolNum;

                    do {
                        $duplicateCheckStmt = $bomMysqli->prepare("SELECT COUNT(*) as keyCount FROM part_tb WHERE PART_SURROGATE = ?");
                        $duplicateCheckStmt->bind_param("s", $surrogateKey);
                        $duplicateCheckStmt->execute();
                        $duplicateCheckStmt->bind_result($keyCount);
                        $duplicateCheckStmt->fetch();
                        $duplicateCheckStmt->close();

                        if ($keyCount > 0) {
                            $surrogateKey = $surrogateKey . '_' . substr(uniqid(), -6);
                        }
                    } while ($keyCount > 0);

                    $partStmt = $bomMysqli->prepare("
                    INSERT INTO part_tb (PART_CODE, CUSTOMER, MODEL, ERP_CODE, PART_SURROGATE)
                    VALUES (?, ?, ?, ?, ?);
                ");
                    $partStmt->bind_param("sssss", $partCode, $customer, $model, $erpCode, $surrogateKey);
                    $partStmt->execute();
                    $partStmt->close();
                } else {
                    $newItem = false;
                    $prevMaterialCode = $materialCode;

                    $materialStmt = $bomMysqli->prepare("
                    INSERT INTO material_tb (PART_SURROGATE, MATERIAL_CODE, MATERIAL_NAME)
                    VALUES (?, ?, ?)
                ");
                    $materialStmt->bind_param("sss", $surrogateKey, $materialCode, $materialName);
                    $materialStmt->execute();
                    $materialStmt->close();
                }

                $detailsStmt = $bomMysqli->prepare("
                INSERT INTO details_tb (
                    DIVISION, PROCESS, CLASS, SUPPLIER, QTY, UNIT, 
                    STATUS, CAV_NUM, TOOL_NUM, BARCODE, TYPE, MATERIAL_CODE, PART_SURROGATE
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

                if (!$detailsStmt) {
                    throw new Exception("Prepare failed for details_tb: " . $bomMysqli->error);
                }

                $type = $newItem ? 'PART' : 'MATERIAL';

                $materialCodeVal = !$newItem ? $materialCode : "";

                $detailsStmt->bind_param(
                    "issssssisssss",
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
                    $type,
                    $materialCodeVal,
                    $surrogateKey
                );

                $detailsStmt->execute();
                $detailsStmt->close();
                $rowsImported++;
            }

            $sheetRowsImported[$sheetName] = $rowsImported;
        }

        $response = [
            'status' => true,
            'message' => 'Excel imported successfully.',
            'file' => $originalName,
            'rows_imported' => $sheetRowsImported
        ];
    } else {
        throw new Exception('Invalid request. File not found.');
    }
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
