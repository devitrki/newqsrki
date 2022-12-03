<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class MassClearingGeneratedExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $massClearingId;

    function __construct($massClearingId)
    {
        $this->massClearingId = $massClearingId;
    }

    public function view(): View
    {
        $items = DB::table('mass_clearing_generates')
                    ->where('mass_clearing_id', $this->massClearingId)
                    ->get();

        $report_data = [
            'items' => $items
        ];

        return view('financeacc.excel.mass-clearing-generated-excel', $report_data);
    }

    public function title(): string
    {
        return 'Mass Clearing Generated';
    }
}
