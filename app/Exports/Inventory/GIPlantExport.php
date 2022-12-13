<?php

namespace App\Exports\Inventory;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;

use App\Models\Inventory\GiPlant;

class GIPlantExport implements FromView, ShouldAutoSize, WithTitle
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
            'title' => Lang::get('GI Plant Report'),
            'data' => GiPlant::getDataReport($this->plant, $this->dateFrom, $this->dateUntil)
        ];

        return view('inventory.excel.gi-plant-excel', $report_data);
    }

    public function title(): string
    {
        return "GI Plant Report";
    }
}
