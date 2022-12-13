<?php

namespace App\Exports\Financeacc;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Lang;

use app\Models\Financeacc\AssetSo;

class AssetSoReportExport implements fromView, ShouldAutoSize
{
    protected $companyId;
    protected $plant;
    protected $costcenter;
    protected $periode;

    function __construct($companyId, $plant, $costcenter, $periode)
    {
        $this->companyId = $companyId;
        $this->plant = $plant;
        $this->costcenter = $costcenter;
        $this->periode = $periode;
    }

    public function view(): View
    {
        $report_data = [
            'title' => Lang::get('Asset SO Report'),
            'data' => AssetSo::getDataAssetSoReport($this->companyId, $this->plant, $this->costcenter, $this->periode)
        ];

        return view('financeacc.excel.asset-so-excel', $report_data);
    }
}
