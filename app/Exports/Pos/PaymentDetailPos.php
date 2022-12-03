<?php

namespace App\Exports\Pos;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

use App\Models\Pos\AllPos;

class PaymentDetailPos implements FromView, ShouldAutoSize, WithTitle
{
    protected $date;

    function __construct($date)
    {
        $this->date = $date;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Payment Detail All POS Report'),
            'data' => AllPos::getDataPaymentDetailReport($this->date)
        ];

        return view('pos.excel.payment-detail-pos-excel', $report_data);
    }

    public function title(): string
    {
        return "Payment Detail All POS Report";
    }
}
