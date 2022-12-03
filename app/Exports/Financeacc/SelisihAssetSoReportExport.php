<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;

use app\Models\Financeacc\AssetSo;

class SelisihAssetSoReportExport implements fromView, ShouldAutoSize
{
    protected $plant;
    protected $periode;
    protected $userID;

    function __construct($plant, $periode, $userID)
    {
        $this->plant = $plant;
        $this->periode = $periode;
        $this->userID = $userID;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Selisih Asset SO Report'),
            'data' => AssetSo::getDataSelisihAssetSoReport($this->plant, $this->periode, $this->userID)
        ];

        return view('financeacc.excel.selisih-asset-so-excel', $report_data);
    }
}
