<?php

namespace App\Exports\Inventory\Usedoil;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use App\Models\Inventory\Usedoil\UoSaldoVendorHistory;

class UoHistorySaldoVendorSheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $vendorId;
    protected $vendorName;
    protected $dateFrom;
    protected $dateUntil;

    function __construct($vendorId, $vendorName, $dateFrom, $dateUntil)
    {
        $this->vendorId = $vendorId;
        $this->vendorName = $vendorName;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('History Saldo Vendor Used Oil Report'),
            'data' => UoSaldoVendorHistory::getDataReport($this->vendorId, $this->dateFrom, $this->dateUntil)
        ];

        return view('inventory.usedoil.excel.uo-history-saldo-vendor-excel', $report_data);
    }

    public function title(): string
    {
        $title = Str::of($this->vendorName)->substr(0, 30);
        return $title . '';
    }
}
