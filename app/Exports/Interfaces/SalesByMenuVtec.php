<?php

namespace App\Exports\Interfaces;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

use App\Models\Interfaces\VtecOrderDetail;

class SalesByMenuVtec implements FromView, ShouldAutoSize, WithTitle
{
    protected $store;
    protected $dateFrom;
    protected $dateUntil;

    function __construct($store, $dateFrom, $dateUntil)
    {
        $this->store = $store;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Sales By Menu VTEC Report'),
            'data' => VtecOrderDetail::getDataReport($this->store, $this->dateFrom, $this->dateUntil, 'download')
        ];

        return view('interfaces.excel.sales-by-menu-vtec-excel', $report_data);
    }

    public function title(): string
    {
        return "Sales By Menu VTEC Report";
    }
}
