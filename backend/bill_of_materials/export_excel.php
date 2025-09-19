<?php
require_once '../../vendor/autoload.php';
require_once __DIR__ . '/../../config/constants.php';
require_once PHP_UTILS_PATH . 'isValidPostRequest.php';
require_once PHP_UTILS_PATH . 'renderHeader.php';
require_once CONFIG_PATH . 'db.php';
require_once PHP_HELPERS_PATH . 'sessionChecker.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Alignment, Border};

$input = json_decode(file_get_contents("php://input"), true);
$headerTree = $input['header'] ?? [];
$tableData  = $input['data'] ?? [];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    if (empty($headerTree)) {
        throw new Exception("Header definition is missing");
    }

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

    $writeRow = function ($sheet, $row, &$rowNum, $colMap, $level = 0) {
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
            $sheet->getStyle("A{$rowNum}:Q{$rowNum}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '92D050'],
                ],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
        } else {
            $sheet->getStyle("A{$rowNum}:Q{$rowNum}")->applyFromArray([
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_HAIR],
                    'bottom' => ['borderStyle' => Border::BORDER_HAIR],
                ],
            ]);
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

        if (!empty($row['children'])) {
            foreach ($row['children'] as $child) {
                $writeRow($currentSheet, $child, $firstRow, $colMap, 1);
            }
        }
    }

    $groups = [
        "A",
        "B",
        "C",
        "D",
        "E",
        "F",
        "G",
        "H",
        "I",
        "J",
        "K:L",
        "M",
        "N:O",
        "P:Q",
        "R:V",
        "W:AA",
        "AB:AF",
        "AG:AH",
        "AI:AJ",
        "AK:AL",
        "AM:AN",
        "AO:AP",
        "AQ:AR",
        "AS:AT"
    ];

    $lastRow = $currentSheet->getHighestRow();
    $lastColumn = $currentSheet->getHighestColumn();
    $lastColIndex = Coordinate::columnIndexFromString($lastColumn);

    foreach ($groups as $col) {
        if (strpos($col, ":") !== false) {
            [$start, $end] = array_map('trim', explode(":", $col));
            $range = "{$start}3:{$end}{$lastRow}";

            if (in_array($start, ['K', 'N', 'P'])) {
                $startIdx = Coordinate::columnIndexFromString($start);
                $endIdx   = Coordinate::columnIndexFromString($end);

                for ($c = $startIdx; $c <= $endIdx; $c++) {
                    $colLetter = Coordinate::stringFromColumnIndex($c);
                    $colRange  = "{$colLetter}3:{$colLetter}{$lastRow}";

                    $currentSheet->getStyle($colRange)->applyFromArray([
                        'borders' => [
                            'top'    => ['borderStyle' => Border::BORDER_THIN],
                            'bottom' => ['borderStyle' => Border::BORDER_THIN],
                            'left'   => ['borderStyle' => Border::BORDER_HAIR],
                            'right'  => ['borderStyle' => Border::BORDER_HAIR],
                        ],
                    ]);
                }
            } else {
                $currentSheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_HAIR],
                    ],
                ]);
            }
        } else {
            $range = "{$col}4:{$col}{$lastRow}";
        }

        $currentSheet->getStyle($range)->applyFromArray([
            'borders' => [
                'left'  => ['borderStyle' => Border::BORDER_THIN],
                'right' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);
    }

    $currentSheet->getStyle("A{$lastRow}:AT{$lastRow}")
        ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

    $currentSheet->getStyle("A4:AT{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)
        ->setVertical(Alignment::VERTICAL_CENTER);

    $currentSheet->getStyle("AQ4:AT{$lastRow}")
        ->getFill()->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFF2CC');

    $currentSheet->getStyle("A4:AT{$lastRow}")
        ->getAlignment()->setWrapText(true);

    for ($col = 1; $col <= $lastColIndex; $col++) {
        $colLetter = Coordinate::stringFromColumnIndex($col);
        $currentSheet->getColumnDimension($colLetter)->setAutoSize(true);
    }

    $currentSheet->calculateColumnWidths();

    $currentSheet->getStyle("G4:G{$lastRow}")
        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $currentSheet->getStyle("K4:K{$lastRow}")
        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    $currentSheet->getSheetView()->setZoomScale(75);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="bom_export.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    echo json_encode([
        "status" => "warning",
        "message" => $e->getMessage()
    ]);
    exit;
}
