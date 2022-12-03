<?php

namespace App\Exports\Interfaces;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

use App\Models\Interfaces\VtecOrderDetail;

class SalesHourMenuVtec implements FromView, ShouldAutoSize
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
            'title' => \Lang::get('Sales By Menu Per Hour VTEC Report'),
            'data' => VtecOrderDetail::getDataMenuPerHourReport($this->store, $this->dateFrom, $this->dateUntil, 'download')
        ];

        return view('interfaces.excel.sales-hour-menu-vtec-excel', $report_data);
    }
}
