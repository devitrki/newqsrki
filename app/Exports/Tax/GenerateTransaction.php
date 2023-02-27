<?php

namespace App\Exports\Tax;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;
use App\Models\Tax\SendTax;

class GenerateTransaction implements FromView, ShouldAutoSize
{
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $report_data = [
            'title' => Lang::get('Transaction Sales Richeese Factory'),
            'data' => $this->data
        ];

        return view('tax.excel.transaction-tax', $report_data);
    }
}
