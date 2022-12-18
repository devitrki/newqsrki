<?php

namespace App\Exports\Pos;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;

use App\Models\Pos\AllPos;

class PaymentPosEx implements FromView, ShouldAutoSize, WithTitle
{
    protected $companyId;
    protected $store;
    protected $dateFrom;
    protected $dateUntil;

    function __construct($companyId, $store, $dateFrom, $dateUntil)
    {
        $this->companyId = $companyId;
        $this->store = $store;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function view(): View
    {
        $report_data = [
            'title' => Lang::get('Payment POS Report'),
            'data' => AllPos::getDataPaymentReport($this->companyId, $this->store, $this->dateFrom, $this->dateUntil)
        ];

        return view('pos.excel.payment-pos-excel', $report_data);
    }

    public function title(): string
    {
        return "Payment POS Report";
    }
}
