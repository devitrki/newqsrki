<?php

namespace App\Exports\Pos;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;

use App\Models\Pos\AllPos;

class SalesPerHourPos implements FromView, ShouldAutoSize
{
    protected $companyId;
    protected $store;
    protected $pos;
    protected $dateFrom;
    protected $dateUntil;

    function __construct($companyId, $store, $pos, $dateFrom, $dateUntil)
    {
        $this->companyId = $companyId;
        $this->store = $store;
        $this->pos = $pos;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function view(): View
    {
        $report_data = [
            'title' => Lang::get('Sales Per Hour Pos Report'),
            'data' => AllPos::getDataSalesPerHourReport($this->companyId, $this->store, $this->pos, $this->dateFrom, $this->dateUntil, 'download')
        ];

        return view('pos.excel.sales-per-hour-pos-excel', $report_data);
    }
}
