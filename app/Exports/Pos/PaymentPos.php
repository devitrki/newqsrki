<?php

namespace App\Exports\Pos;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

use App\Models\Pos\AllPos;

class PaymentPos implements FromView, ShouldAutoSize, WithTitle
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
            'title' => \Lang::get('Payment POS Report'),
            'data' => AllPos::getDataPaymentReport($this->store, $this->dateFrom, $this->dateUntil)
        ];

        return view('pos.excel.payment-pos-excel', $report_data);
    }

    public function title(): string
    {
        return "Payment POS Report";
    }
}
