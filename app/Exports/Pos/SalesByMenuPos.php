<?php

namespace App\Exports\Pos;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

use App\Models\Pos\AllPos;

class SalesByMenuPos implements FromView, ShouldAutoSize, WithTitle
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
            'title' => \Lang::get('Sales By Menu Pos Report'),
            'data' => AllPos::getDataSalesByMenuReport($this->store, $this->pos, $this->dateFrom, $this->dateUntil, 'download')
        ];

        return view('pos.excel.sales-by-menu-pos-excel', $report_data);
    }

    public function title(): string
    {
        return "Sales By Menu Pos Report";
    }
}
