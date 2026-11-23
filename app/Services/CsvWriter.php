<?php

namespace App\Services;

final class CsvWriter
{
    /**
     * Write one row, neutralising text that spreadsheet software would evaluate as a formula.
     *
     * @param  resource  $handle
     * @param  list<int|string|null>  $row
     */
    public function writeRow($handle, array $row): void
    {
        fputcsv($handle, array_map($this->escapeFormula(...), $row), escape: '');
    }

    private function escapeFormula(int|string|null $value): int|string|null
    {
        if (is_string($value) && preg_match('/^[\s\p{Z}]*[=+\-@]/u', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }
}
