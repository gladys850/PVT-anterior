<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\MultipleSheetsReport;

class FileWithMultipleSheetsReport implements WithMultipleSheets
{
    protected $command_sheet_later;
    protected $command_sheet_before;
    protected $senasir_sheet_later;
    protected $senasir_sheet_before;
    protected $command_ancient;
    protected $senasir_ancient;

    public function __construct(array $command_sheet_later, array $command_sheet_before, array $senasir_sheet_later, array $senasir_sheet_before,array $command_ancient,array $senasir_ancient)
    {
        $this->command_sheet_later = $command_sheet_later;
        $this->command_sheet_before = $command_sheet_before;
        $this->senasir_sheet_later = $senasir_sheet_later;
        $this->senasir_sheet_before = $senasir_sheet_before;
        $this->command_ancient = $command_ancient;
        $this->senasir_ancient = $senasir_ancient;

    }

     /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [
            "command_later" => new MultipleSheetsReport($this->command_sheet_later, 'Nuevos comando > 15 (Ant_Mes)'),
            "command_before" => new MultipleSheetsReport($this->command_sheet_before, 'Nuevos comando <= 15 (Act_Mes)'),
            "senasir_later" => new MultipleSheetsReport($this->senasir_sheet_later, 'Nuevos senasir > 15 (Ant_Mes)'),
            "senasir_before" => new MultipleSheetsReport($this->senasir_sheet_before, 'Nuevos senasir <= 15 (Act_Mes)'),
            "command_ancient" => new MultipleSheetsReport($this->command_ancient, 'comando recurrentes'),
            "senasir_ancient" => new MultipleSheetsReport($this->senasir_ancient, 'senasir recurrentes')
        ];

        return $sheets;
    }
}
