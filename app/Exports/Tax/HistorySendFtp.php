<?php

namespace App\Exports\Tax;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

use App\Models\tax\HistorySendTax;

class HistorySendFtp implements FromView, ShouldAutoSize
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
            'title' => \Lang::get('History Send FTP Tax Report'),
            'data' => HistorySendTax::getDataReport($this->plant, $this->dateFrom, $this->dateUntil, $this->status)
        ];

        return view('tax.excel.history-send-ftp-excel', $report_data);
    }
}
