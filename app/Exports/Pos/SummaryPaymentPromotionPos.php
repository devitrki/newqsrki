<?php

namespace App\Exports\Pos;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

use App\Models\Pos\AllPos;

class SummaryPaymentPromotionPos implements FromView, ShouldAutoSize
{
    protected $store;
    protected $pos;
    protected $date;

    function __construct($store, $pos, $date)
    {
        $this->store = $store;
        $this->pos = $pos;
        $this->date = $date;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Summary Payment and Promotion Pos Report'),
            'data' => AllPos::getDataSummaryPaymentPromotionReport($this->store, $this->pos, $this->date)
        ];

        return view('pos.excel.summary-payment-promotion-pos-excel', $report_data);
    }
}
