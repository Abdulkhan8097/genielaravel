<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ArrayRecordsExportWithTitle implements FromArray, WithStrictNullComparison, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
	protected $records;
    protected $sheetTitle;
    protected $extra_params;
    public function __construct(array $records, string $sheetTitle = '', array $extra_params)
    {
        $this->records = $records;
        $this->sheetTitle = $sheetTitle;
        $this->extra_params = $extra_params;
    }

    public function array(): array
    {
        return $this->records;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function styles(Worksheet $sheet)
    {
        if(is_array($this->extra_params) && count($this->extra_params) > 0){
            if(isset($this->extra_params['merg_cells']) && is_array($this->extra_params['merg_cells']) && count($this->extra_params['merg_cells']) > 0){
                foreach($this->extra_params['merg_cells'] as $merge_record){
                    $sheet->mergeCells($merge_record);
                }
                unset($merge_record);
            }

            /*if(isset($this->extra_params['freeze_row']) && !empty($this->extra_params['freeze_row'])){
                $sheet->setFreeze($this->extra_params['freeze_row']);
            }*/
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                if(is_array($this->extra_params) && count($this->extra_params) > 0){
                    if(isset($this->extra_params['freeze_row'])){
                        if(!empty($this->extra_params['freeze_row'])){
                            $event->sheet->getDelegate()->freezePane($this->extra_params['freeze_row']);
                        }
                        else{
                            $event->sheet->getDelegate()->freezePane('A2');
                        }
                    }
                }
            },
        ];
    }
}
