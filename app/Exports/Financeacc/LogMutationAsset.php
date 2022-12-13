<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;

use app\Models\Financeacc\AssetMutation;

class LogMutationAsset implements fromView, ShouldAutoSize
{
    protected $companyId;
    protected $plant;
    protected $user_id;
    protected $from_date;
    protected $until_date;

    function __construct($companyId, $plant, $user_id, $from_date, $until_date)
    {
        $this->companyId = $companyId;
        $this->plant = $plant;
        $this->user_id = $user_id;
        $this->from_date = $from_date;
        $this->until_date = $until_date;
    }

    public function view(): View
    {
        $report_data = [
            'title' => Lang::get('Log Asset Transfer Report'),
            'data' => AssetMutation::getDataLogReport($this->companyId, $this->plant, $this->user_id, $this->from_date, $this->until_date)
        ];

        return view('financeacc.excel.log-mutation-asset-excel', $report_data);
    }
}
