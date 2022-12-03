<?php

namespace App\Exports\Interfaces;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

use App\Models\Interfaces\GionHistorySales;

class HistorySalesGion implements FromView, ShouldAutoSize, WithTitle
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
            'title' => \Lang::get('History Sales Gion Report'),
            'data' => GionHistorySales::getDataReport($this->dateFrom, $this->dateUntil)
        ];

        return view('interfaces.excel.history-sales-gion-excel', $report_data);
    }

    public function title(): string
    {
        return "History Sales Gion Report";
    }
}
