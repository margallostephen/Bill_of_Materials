<?php
require_once '../../vendor/autoload.php';
require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_UTILS_PATH . 'getIPAddress.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


$response = [
    'status' => false,
    'message' => '',
    'data' => null
];

function getVal($sheet, int $col, int $row): string
{
    return trim((string)$sheet->getCell(Coordinate::stringFromColumnIndex($col) . $row)->getFormattedValue());
}

function getValNote($sheet, int $col, int $row): array
{
    $coord  = Coordinate::stringFromColumnIndex($col) . $row;
    $cell   = $sheet->getCell($coord);
    $value  = trim((string)$cell->getFormattedValue());
    $note   = ($c = $sheet->getComment($coord)) ? $c->getText()->getPlainText() : null;
    return [$value, $note];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $userRfid = $_SESSION['RFID'];
    $userIp = getUserIP();
    $createdAt = $updatedAt = date("Y-m-d H:i:s");
    $rowsImported = 0;
    $sheetRowsImported = [];

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
    $reader->setReadDataOnly(false);
    $reader->setIncludeCharts(false);

    $sheetNames = $reader->listWorksheetNames($savedFilePath);

    $checkEmptyTable = $bomMysqli->query("SELECT COUNT(*) as total FROM part_tb");
    $checkTableResult = $checkEmptyTable->fetch_assoc();
    $emptyTable = $checkTableResult['total'] == 0;
    $weightType = [
        0 => "QUOTATION",
        1 => "ACTUAL",
        2 => "APPROVAL"
    ];

    foreach ($sheetNames as $sheetName) {
        if ($sheetName === "REV") {
            continue;
        }

        $reader->setLoadSheetsOnly($sheetName);
        $currentSpreadsheet = $reader->load($savedFilePath);
        $currentSheet = $currentSpreadsheet->getActiveSheet();

        if ($currentSheet->getSheetState() !== Worksheet::SHEETSTATE_VISIBLE) {
            continue;
        }

        $highestRow = $currentSheet->getHighestDataRow();
        $highestCol = $currentSheet->getHighestDataColumn();
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);

        $dataRange = "A4:" . $highestCol . $highestRow;

        $isPart = true;
        $isNewPart = true;
        $partSurrogate = '';
        $materialSurrogate = '';
        $currentRowsImported = $partsImported = $materialsImported = 0;
        $prevCode = '';
        $materialKey = 1;
        $partIndexMap = [];

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

        foreach ($currentSheet->getRowIterator(4, $highestRow) as $row) {
            $rowIndex = $row->getRowIndex();

            $division = getVal($currentSheet, 9,  $rowIndex);
            $customer = getVal($currentSheet, 10, $rowIndex);
            $model = getVal($currentSheet, 11, $rowIndex);

            list($partCode, $partCodeNote) = getValNote($currentSheet, 12, $rowIndex);
            $erpCode = getVal($currentSheet, 13, $rowIndex);
            list($code, $codeNote) = getValNote($currentSheet, 14, $rowIndex);
            list($description, $descriptionNote) = getValNote($currentSheet, 15, $rowIndex);
            $process = getVal($currentSheet, 16, $rowIndex);
            $class = getVal($currentSheet, 17, $rowIndex);
            $supplier = getVal($currentSheet, 18, $rowIndex);
            $qty = getVal($currentSheet, 19, $rowIndex);
            $unit = getVal($currentSheet, 20, $rowIndex);
            $status = getVal($currentSheet, 21, $rowIndex);
            $cavNum = getVal($currentSheet, 22, $rowIndex);
            list($toolNum, $toolNumNote) = getValNote($currentSheet, 23, $rowIndex);
            $barcode = getVal($currentSheet, 24, $rowIndex);
            $labelCustomer = getVal($currentSheet, 25, $rowIndex);

            if (!array_filter([
                $customer,
                $model,
                $partCode,
                $erpCode,
                $code,
                $description,
                $process,
                $class,
                $supplier,
                $qty,
                $unit,
                $status,
                $cavNum,
                $toolNum,
                $barcode,
                $labelCustomer
            ], 'strlen')) {
                continue;
            }

            if ($description == 'RUNNER') {
                $code = $prevCode . 'RNR';
            }

            if ($division == "1") {
                $isPart = true;
                if ($partCode == "") {
                    $partCode = $code;
                }

                if ($emptyTable) {
                    $partIndex = 1;
                    do {
                        $partSurrogate = implode('_', array_filter([
                            $partCode,
                            $description,
                            $toolNum,
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

                    $insertPart->bind_param(
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
                    $insertPart->execute();
                }

                $materialKey = 1;
                $materialSurrogate = implode('_', array_filter([
                    $partSurrogate,
                    $description,
                    $materialKey
                ]));

                $partsImported++;
            } else {
                $isPart = false;
                $prevCode = $code;

                if ($emptyTable) {
                    $materialSurrogate = implode('_', array_filter([
                        $partSurrogate,
                        $description,
                        $materialKey
                    ]));

                    $insertMaterial->bind_param(
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
                    $insertMaterial->execute();

                    $materialKey++;
                }
                $materialsImported++;
            }

            $rowType = $isPart ? 0 : 1;
            $codeVal = !$isPart ? $code : "";
            $materialSurrogate = $isPart ? NULL : $materialSurrogate;

            if ($emptyTable) {
                $insertDetails->bind_param(
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
                    $rowType,
                    $codeVal,
                    $materialSurrogate,
                    $partSurrogate,
                    $userRfid,
                    $userIp,
                    $createdAt
                );

                $insertDetails->execute();

                $startingWeightCtColIndex = 26;

                for ($i = 0; $i < 3; $i++) {
                    $prod = round((float) getVal($currentSheet, $startingWeightCtColIndex, $rowIndex), 2);
                    $sr = round((float) getVal($currentSheet, $startingWeightCtColIndex + 1, $rowIndex), 2);
                    $total = round((float) getVal($currentSheet, $startingWeightCtColIndex + 2, $rowIndex), 2);
                    $gpc = round((float) getVal($currentSheet, $startingWeightCtColIndex + 3, $rowIndex), 2);
                    $ctime = round((float) getVal($currentSheet, $startingWeightCtColIndex + 4, $rowIndex), 2);

                    if (!empty($prod) || !empty($sr) || !empty($total) || !empty($gpc) || !empty($ctime)) {
                        $insertWeightCt->bind_param(
                            "dddddsssisss",
                            $prod,
                            $sr,
                            $total,
                            $gpc,
                            $ctime,
                            $partSurrogate,
                            $materialSurrogate,
                            $rowType,
                            $i,
                            $userRfid,
                            $userIp,
                            $createdAt
                        );

                        $insertWeightCt->execute();
                    }

                    $startingWeightCtColIndex += 5;
                }

                $startingMcColIndex = 41;

                for ($i = 0; $i < 7; $i++) {
                    $approved = $i > 4 ? 1 : 0;
                    $mc = getVal($currentSheet, $startingMcColIndex, $rowIndex);
                    $ton = getVal($currentSheet, $startingMcColIndex + 1, $rowIndex);

                    if (!empty($mc) || !empty($ton)) {
                        $insertMc->bind_param(
                            "ssisisssss",
                            $mc,
                            $ton,
                            $i,
                            $rowType,
                            $approved,
                            $partSurrogate,
                            $materialSurrogate,
                            $userRfid,
                            $userIp,
                            $createdAt
                        );

                        $insertMc->execute();
                    }

                    $startingMcColIndex += 2;
                }
            }

            $currentRowsImported++;
        }
        $rowsImported += $currentRowsImported;

        $sheetRowsImported[$sheetName] = [
            'parts_imported' => $partsImported,
            'materials_imported' => $materialsImported,
            'current_total_rows' => $currentRowsImported
        ];
        $checkDuplicatePart->close();
        $insertPart->close();
        $insertMaterial->close();
        $insertDetails->close();
        $insertWeightCt->close();
        $insertMc->close();
        $currentSpreadsheet->disconnectWorksheets();
        unset($currentSpreadsheet);
        gc_collect_cycles();
    }

    $response = [
        'status' => true,
        'message' => 'Excel imported successfully.',
        'rows_imported' => [
            'total_rows' => $rowsImported,
            'per_sheet' => $sheetRowsImported
        ]
    ];
} else {
    throw new Exception('Invalid request. File not found.');
}

echo json_encode($response, JSON_PRETTY_PRINT);
