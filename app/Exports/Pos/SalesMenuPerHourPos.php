<?php

namespace App\Exports\Pos;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

use App\Models\Pos\AllPos;

class SalesMenuPerHourPos implements FromView, ShouldAutoSize
{
    protected $store;
    protected $pos;
    protected $dateFrom;
    protected $dateUntil;

    function __construct($store, $pos, $dateFrom, $dateUntil)
    {
        $this->store = $store;
        $this->pos = $pos;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Sales By Menu Per Hour Pos Report'),
            'data' => AllPos::getDataSalesMenuPerHourReport($this->store, $this->pos, $this->dateFrom, $this->dateUntil, 'download')
        ];

        return view('pos.excel.sales-menu-per-hour-pos-excel', $report_data);
    }
}
