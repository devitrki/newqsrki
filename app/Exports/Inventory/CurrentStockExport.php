<?php

namespace App\Exports\Inventory;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use App\Models\Stock;

class CurrentStockExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $plant;
    protected $materialType;

    function __construct($plant, $materialType)
    {
        $this->plant = $plant;
        $this->materialType = $materialType;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Current Stock Report'),
            'data' => Stock::getDataReport($this->plant, $this->materialType)
        ];

        return view('inventory.excel.current-stock-excel', $report_data);
    }

    public function title(): string
    {
        return "Current Stock Report";
    }
}
