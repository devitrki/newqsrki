<?php

namespace App\Exports\Interfaces;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

use App\Models\Interfaces\VtecOrderPayDetail;

class PaymentDetailVtec implements FromView, ShouldAutoSize, WithTitle
{
    protected $date;

    function __construct($date)
    {
        $this->date = $date;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Payment Detail VTEC Report'),
            'data' => VtecOrderPayDetail::getDataPaymentDetailReport($this->date)
        ];

        return view('interfaces.excel.payment-detail-vtec-excel', $report_data);
    }

    public function title(): string
    {
        return "Payment Detail VTEC Report";
    }
}
