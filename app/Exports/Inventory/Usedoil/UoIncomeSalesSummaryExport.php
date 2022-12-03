<?php

namespace App\Exports\Inventory\Usedoil;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use App\Models\Inventory\Usedoil\UoMovement;

class UoIncomeSalesSummaryExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $dateFrom;
    protected $dateUntil;

    function __construct($dateFrom, $dateUntil)
    {
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Income Sales Summary Used Oil Report'),
            'data' => UoMovement::getDataReport($this->dateFrom, $this->dateUntil)
        ];

        return view('inventory.usedoil.excel.uo-income-sales-summary-excel', $report_data);
    }

    public function title(): string
    {
        return "Income Sales Summary";
    }
}
