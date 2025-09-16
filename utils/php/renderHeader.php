<?php

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

function renderHeaders($sheet, $columns, $row, &$col, $maxDepth = 1)
{
    foreach ($columns as $colDef) {
        $title = $colDef['title'] ?? $colDef['field'] ?? '';

        if (!empty($colDef['columns'])) {
            $startCol = $col;
            renderHeaders($sheet, $colDef['columns'], $row + 1, $col, $maxDepth);
            $endCol = $col - 1;

            $startCell = Coordinate::stringFromColumnIndex($startCol) . $row;
            $endCell   = Coordinate::stringFromColumnIndex($endCol) . $row;

            $sheet->mergeCells("{$startCell}:{$endCell}");
            $sheet->setCellValue($startCell, $title);
        } else {
            $lastRow = $maxDepth;
            $cell = Coordinate::stringFromColumnIndex($col) . $lastRow;
            $sheet->setCellValue($cell, $title);

            $col++;
        }
    }
}
