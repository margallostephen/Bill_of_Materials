<?php
ini_set('max_execution_time', 0);
require_once '../../vendor/autoload.php';
require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once PHP_UTILS_PATH . 'renderHeader.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Alignment, Border};

$input = json_decode(file_get_contents("php://input"), true);
$headerTree = $input['header'] ?? [];
$tableData  = $input['data'] ?? [];
$errorResponse = [
    "status"  => "error",
    "message" => "Invalid request method."
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode($errorResponse);
    exit;
}

if (empty($headerTree) || empty($tableData)) {
    header('Content-Type: application/json');
    http_response_code(422);
    $errorResponse["status"] = "warning";
    $errorResponse["message"] = empty($headerTree) ? "Header definition is missing." : "No data to export.";
    echo json_encode($errorResponse);
    exit;
} else {
    $headerTree = array_slice($headerTree, 0, -3, true);
}

try {
    $colMap = [
        'DIVISION' => "A",
        'CUSTOMER' => "B",
        'MODEL' => "C",
        'PART_CODE' => "D",
        'ERP_CODE' => "E",
        'CODE' => "F",
        'DESCRIPTION' => "G",
        'PROCESS' => "H",
        'CLASS' => "I",
        'SUPPLIER' => "J",
        'QTY' => "K",
        'UNIT' => "L",
        'STATUS' => "M",
        'CAV_NUM' => "N",
        'TOOL_NUM' => "O",
        'BARCODE' => "P",
        'LABEL_CUSTOMER' => "Q",
        'PROD_QT' => "R",
        'S_R_QT' => "S",
        'TOTAL_QT' => "T",
        'G_PCS_QT' => "U",
        'C_TIME_QT' => "V",
        'PROD_AT' => "W",
        'S_R_AT' => "X",
        'TOTAL_AT' => "Y",
        'G_PCS_AT' => "Z",
        'C_TIME_AT' => "AA",
        'PROD_AP' => "AB",
        'S_R_AP' => "AC",
        'TOTAL_AP' => "AD",
        'G_PCS_AP' => "AE",
        'C_TIME_AP' => "AF",
        'MC_1' => "AG",
        'TON_1' => "AH",
        'MC_2' => "AI",
        'TON_2' => "AJ",
        'MC_3' => "AK",
        'TON_3' => "AL",
        'MC_4' => "AM",
        'TON_4' => "AN",
        'MC_5' => "AO",
        'TON_5' => "AP",
        'MC_1_AP_4M' => "AQ",
        'TON_1_AP_4M' => "AR",
        'MC_2_AP_4M' => "AS",
        'TON_2_AP_4M' => "AT",
    ];

    $spreadsheet = new Spreadsheet();
    $spreadsheet->getDefaultStyle()->getFont()
        ->setName('Tahoma')
        ->setSize(10);

    $currentSheet = $spreadsheet->getActiveSheet();
    $currentSheet->setTitle('Bill of Materials');

    $styleDivision1 = [
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => '92D050'],
        ],
        'borders' => [
            'top'    => ['borderStyle' => Border::BORDER_THIN],
            'bottom' => ['borderStyle' => Border::BORDER_THIN],
        ],
    ];

    $styleDefault = [
        'borders' => [
            'top'    => ['borderStyle' => Border::BORDER_HAIR],
            'bottom' => ['borderStyle' => Border::BORDER_HAIR],
        ],
    ];

    $writeRow = function ($sheet, $row, &$rowNum, $colMap, $level = 0)
    use ($styleDivision1, $styleDefault) {
        foreach ($row as $field => $value) {
            if (!isset($colMap[$field])) continue;

            $letter = $colMap[$field];

            if (is_numeric($value) && strpos((string)$value, '.') !== false) {
                $sheet->setCellValue($letter . $rowNum, round((float)$value, 2));
                $sheet->getStyle($letter . $rowNum)
                    ->getNumberFormat()
                    ->setFormatCode('0.00');
            } else {
                $sheet->setCellValue($letter . $rowNum, (($value == '' || $value == 0) ? '' : $value));
            }
        }

        $sheet->getRowDimension($rowNum)->setOutlineLevel($level);

        if (isset($row['DIVISION']) && $row['DIVISION'] == 1) {
            $sheet->getStyle("A{$rowNum}:Q{$rowNum}")->applyFromArray($styleDivision1);
        } else {
            $sheet->getStyle("A{$rowNum}:Q{$rowNum}")->applyFromArray($styleDefault);
        }

        $rowNum++;
    };

    $headerRow = 1;
    $firstCol = 1;
    $maxDepth = 3;
    renderHeaders($currentSheet, $headerTree, $headerRow, $firstCol, $maxDepth);

    $highestCol = $currentSheet->getHighestColumn();
    $currentSheet->getStyle("A1:{$highestCol}{$maxDepth}")
        ->getFont()->setBold(true);

    $currentSheet->getStyle("A1:{$highestCol}{$maxDepth}")
        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

    $currentSheet->getStyle('A2:AP3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E9EFF6');

    $currentSheet->getStyle('AQ1:AT3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2CC');

    $currentSheet->getStyle('A1:AT3')->getFont()->setBold(true);

    $currentSheet->getStyle('A1:AT3')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    foreach (array_merge(range('A', 'J'), ['M']) as $firstCol) {
        $currentSheet->getStyle("{$firstCol}2")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_NONE);
        $currentSheet->getStyle("{$firstCol}3")->getBorders()->getTop()->setBorderStyle(Border::BORDER_NONE);
    }

    foreach (range(1, 3) as $r) $currentSheet->getRowDimension($r)->setRowHeight(25);

    $currentSheet->freezePane('I4');
    $currentSheet->setAutoFilter("A3:Q3");
    $currentSheet->setShowSummaryBelow(false);

    $firstRow = 4;

    foreach ($tableData as $row) {
        $writeRow($currentSheet, $row, $firstRow, $colMap, 0);

        if (!empty($row['_children'])) {
            foreach ($row['_children'] as $child) {
                $writeRow($currentSheet, $child, $firstRow, $colMap, 1);
            }
        }
    }

    $groupCols = [
        "A"      => 10,
        "B"      => 15,
        "C"      => 25,
        "D"      => 18,
        "E"      => 13,
        "F"      => 16,
        "G"      => 68,
        "H"      => 20,
        "I"      => 16,
        "J"      => 22,
        "K:L"    => 10,
        "M"      => 16,
        "N:O"    => 10,
        "P:Q"    => 28,
        "R:V"    => 10,
        "W:AA"   => 10,
        "AB:AF"  => 10,
        "AG:AH"  => 10,
        "AI:AJ"  => 10,
        "AK:AL"  => 10,
        "AM:AN"  => 10,
        "AO:AP"  => 10,
        "AQ:AR"  => 10,
        "AS:AT"  => 10,
    ];

    $lastRow = $currentSheet->getHighestRow();
    $lastColumn = $currentSheet->getHighestColumn();
    $lastColIndex = Coordinate::columnIndexFromString($lastColumn);
    $innerSingleColStyle = [
        'borders' => [
            'top'    => ['borderStyle' => Border::BORDER_THIN],
            'bottom' => ['borderStyle' => Border::BORDER_THIN],
            'left'   => ['borderStyle' => Border::BORDER_HAIR],
            'right'  => ['borderStyle' => Border::BORDER_HAIR],
        ],
    ];
    $innerGroupedColStyle = [
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_HAIR],
        ],
    ];
    $outerGroupedColStyle = [
        'borders' => [
            'left'  => ['borderStyle' => Border::BORDER_THIN],
            'right' => ['borderStyle' => Border::BORDER_THIN],
        ],
    ];

    foreach ($groupCols as $col => $width) {
        if (strpos($col, ":") !== false) {
            [$start, $end] = array_map('trim', explode(":", $col));
            $range = "{$start}3:{$end}{$lastRow}";

            if (in_array($start, ['K', 'N', 'P'])) {
                $startIdx = Coordinate::columnIndexFromString($start);
                $endIdx   = Coordinate::columnIndexFromString($end);

                for ($c = $startIdx; $c <= $endIdx; $c++) {
                    $colLetter = Coordinate::stringFromColumnIndex($c);
                    $colRange  = "{$colLetter}3:{$colLetter}{$lastRow}";

                    $currentSheet->getStyle($colRange)->applyFromArray($innerSingleColStyle);
                    $currentSheet->getColumnDimension($colLetter)->setWidth($width);
                }
            } else {
                $currentSheet->getStyle($range)->applyFromArray($innerGroupedColStyle);
            }
        } else {
            $range = "{$col}4:{$col}{$lastRow}";
            $currentSheet->getColumnDimension($col)->setWidth($width);
        }

        $currentSheet->getStyle($range)->applyFromArray($outerGroupedColStyle);
    }

    $currentSheet->getStyle("A{$lastRow}:AT{$lastRow}")
        ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

    $currentSheet->getStyle("A4:AT{$lastRow}")->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER)
        ->setWrapText(true);

    $currentSheet->getStyle("AQ4:AT{$lastRow}")
        ->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFF2CC');

    $currentSheet->getStyle("G4:G{$lastRow}")
        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $currentSheet->getStyle("K4:K{$lastRow}")
        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    $currentSheet->getSheetView()->setZoomScale(75);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="bom_export.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->setPreCalculateFormulas(false);
    $writer->save('php://output');
} catch (Throwable $err) {
    header('Content-Type: application/json');
    http_response_code(500);
    $errorResponse["message"] = $err->getMessage();
    $errorResponse["trace"]   = $err->getTraceAsString();
    echo json_encode($errorResponse);
}
