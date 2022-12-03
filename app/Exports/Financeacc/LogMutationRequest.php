<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

use app\Models\Financeacc\AssetRequestMutation;

class LogMutationRequest implements fromView, ShouldAutoSize
{
    protected $plant;
    protected $user_id;
    protected $from_date;
    protected $until_date;

    function __construct($plant, $user_id, $from_date, $until_date)
    {
        $this->plant = $plant;
        $this->user_id = $user_id;
        $this->from_date = $from_date;
        $this->until_date = $until_date;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Log Request Mutation Report'),
            'data' => AssetRequestMutation::getDataLogReport($this->plant, $this->user_id, $this->from_date, $this->until_date)
        ];

        return view('financeacc.excel.log-request-mutation-excel', $report_data);
    }
}
