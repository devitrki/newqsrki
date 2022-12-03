<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

use app\Models\Financeacc\AssetRequestMutation;

class OutstandingRequestMutation implements FromView, ShouldAutoSize
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
            'title' => \Lang::get('Outstanding Request Asset Transfer Report'),
            'data' => AssetRequestMutation::getDataOutstandingReport($this->plant, $this->user_id)
        ];

        return view('financeacc.excel.outstanding-request-mutation-excel', $report_data);
    }
}
