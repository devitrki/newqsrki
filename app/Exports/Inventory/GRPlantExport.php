<?php

namespace App\Exports\Inventory;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\GrPlant;

class GRPlantExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $plant;
    protected $dateFrom;
    protected $dateUntil;

    function __construct($plant, $dateFrom, $dateUntil)
    {
        $this->plant = $plant;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('GR Plant Report'),
            'data' => GrPlant::getDataReport($this->plant, $this->dateFrom, $this->dateUntil)
        ];

        return view('inventory.excel.gr-plant-excel', $report_data);
    }

    public function title(): string
    {
        return "GR Plant Report";
    }
}