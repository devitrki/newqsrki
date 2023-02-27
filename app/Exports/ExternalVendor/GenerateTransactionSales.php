<?php

namespace App\Exports\ExternalVendor;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;

class GenerateTransactionSales implements FromView, ShouldAutoSize
{
    protected $datas;
    protected $fields;

    function __construct($datas, $fields)
    {
        $this->datas = $datas;
        $this->fields = $fields;
    }

    public function view(): View
    {
        $report_data = [
            'title' => Lang::get('Transaction Sales Richeese Factory'),
            'datas' => $this->datas,
            'fields' => $this->fields
        ];

        return view('externalVendors.excel.transaction-sales', $report_data);
    }
}
