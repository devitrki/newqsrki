<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

use app\Models\Financeacc\AssetMutation;

class OutstandingMutationAsset implements FromView, ShouldAutoSize
{
    protected $plant;
    protected $user_id;

    function __construct($plant, $user_id)
    {
        $this->plant = $plant;
        $this->user_id = $user_id;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Outstanding Asset Transfer Report'),
            'data' => AssetMutation::getDataOutstandingReport($this->plant, $this->user_id)
        ];

        return view('financeacc.excel.outstanding-mutation-asset-excel', $report_data);
    }
}
