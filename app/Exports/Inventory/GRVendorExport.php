<?php

namespace App\Exports\Inventory;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use App\Models\Inventory\GrVendor;

class GRVendorExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $plant;
    protected $dateFrom;
    protected $dateUntil;

    function __construct($plant, $dateFrom, $dateUntil)
    {
        $this->plant = $plant;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('GR PO Vendor Report'),
            'data' => GrVendor::getDataReport($this->plant, $this->dateFrom, $this->dateUntil)
        ];

        return view('inventory.excel.gr-vendor-excel', $report_data);
    }

    public function title(): string
    {
        return "GR PO Vendor Report";
    }
}
