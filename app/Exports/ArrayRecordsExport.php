<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ArrayRecordsExport implements FromArray, WithStrictNullComparison, ShouldAutoSize
{
	protected $records;
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    public function array(): array
    {
        return $this->records;
    }
}
