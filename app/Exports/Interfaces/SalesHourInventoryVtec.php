<?php

namespace App\Exports\Interfaces;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

use App\Models\Interfaces\VtecOrderDetail;

class SalesHourInventoryVtec implements FromView, ShouldAutoSize
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
            'title' => \Lang::get('Sales By Inventory Per Hour VTEC Report'),
            'data' => VtecOrderDetail::getDataInventoryPerHourReport($this->store, $this->dateFrom, $this->dateUntil, 'download')
        ];

        return view('interfaces.excel.sales-hour-inventory-vtec-excel', $report_data);
    }
}
