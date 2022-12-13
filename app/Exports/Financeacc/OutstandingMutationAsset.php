<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;

use app\Models\Financeacc\AssetMutation;

class OutstandingMutationAsset implements FromView, ShouldAutoSize
{
    protected $companyId;
    protected $plant;
    protected $user_id;

    function __construct($companyId, $plant, $user_id)
    {
        $this->companyId = $companyId;
        $this->plant = $plant;
        $this->user_id = $user_id;
    }

    public function view(): View
    {
        $report_data = [
            'title' => Lang::get('Outstanding Asset Transfer Report'),
            'data' => AssetMutation::getDataOutstandingReport($this->companyId, $this->plant, $this->user_id)
        ];

        return view('financeacc.excel.outstanding-mutation-asset-excel', $report_data);
    }
}
