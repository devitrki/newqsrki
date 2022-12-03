<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class MassClearingResultExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $massClearingId;

    function __construct($massClearingId)
    {
        $this->massClearingId = $massClearingId;
    }

    public function view(): View
    {
        $items = DB::table('mass_clearing_details')
                    ->where('mass_clearing_id', $this->massClearingId)
                    ->select(
                        'bank_in_bank_gl',
                        'bank_in_date',
                        'bank_in_description',
                        'sales_date',
                        'sales_month',
                        'sales_year',
                        'special_gl',
                        'plant_id',
                        'bank_in_nominal',
                        'bank_in_charge',
                        'nominal_sales',
                        'selisih',
                        'selisih_percent',
                        'status_process',
                        'status_generate',
                        'description'
                    )
                    ->get();

        $report_data = [
            'items' => $items
        ];
        return view('financeacc.excel.mass-clearing-result-excel', $report_data);
    }

    public function title(): string
    {
        return 'Result Process Generate';
    }
}
