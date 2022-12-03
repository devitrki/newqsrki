<?php

namespace App\Exports\Inventory;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use App\Models\Inventory\Posto;

class OutstandingPostoExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $plant;

    function __construct($plant)
    {
        $this->plant = $plant;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Outstanding PO-STO Report'),
            'data' => Posto::getDataReport($this->plant)
        ];

        return view('inventory.excel.outstanding-posto-excel', $report_data);
    }

    public function title(): string
    {
        return "Outstanding PO-STO Report";
    }
}
