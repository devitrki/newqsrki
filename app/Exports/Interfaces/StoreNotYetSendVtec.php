<?php

namespace App\Exports\Interfaces;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

use App\Models\Interfaces\VtecOrderStatistic;

class StoreNotYetSendVtec implements FromView, ShouldAutoSize, WithTitle
{
    protected $date;

    function __construct($date)
    {
        $this->date = $date;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Store Not Yet Send Vtec Report'),
            'data' => VtecOrderStatistic::getDataReport($this->date)
        ];

        return view('interfaces.excel.store-not-yet-send-vtec-excel', $report_data);
    }

    public function title(): string
    {
        return "Store Not Yet Send Vtec Report";
    }
}
