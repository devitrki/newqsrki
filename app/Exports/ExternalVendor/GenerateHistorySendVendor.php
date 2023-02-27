<?php

namespace App\Exports\ExternalVendor;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;

use App\Models\ExternalVendor\HistorySendVendor;

class GenerateHistorySendVendor implements FromView, ShouldAutoSize
{
    protected $plant;
    protected $dateFrom;
    protected $dateUntil;
    protected $status;

    function __construct($plant, $dateFrom, $dateUntil, $status)
    {
        $this->plant = $plant;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
        $this->status = $status;
    }

    public function view(): View
    {
        $report_data = [
            'title' => Lang::get('History Send Vendor Report'),
            'data' => HistorySendVendor::getDataReport($this->plant, $this->dateFrom, $this->dateUntil, $this->status)
        ];

        return view('externalVendors.excel.history-send-vendor-excel', $report_data);
    }
}
