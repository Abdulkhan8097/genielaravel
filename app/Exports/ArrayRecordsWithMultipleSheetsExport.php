<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ArrayRecordsWithMultipleSheetsExport implements FromArray, WithStrictNullComparison, WithMultipleSheets
{
	protected $records;
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach($this->records as $record){
            $sheets[] = new ArrayRecordsExportWithTitle($record['data'], $record['title'], ($record['extra_params']??array()));
        }
        return $sheets;
    }

    public function array(): array
    {
        return $this->records;
    }
}
