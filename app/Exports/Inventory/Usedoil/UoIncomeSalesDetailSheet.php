<?php

namespace App\Exports\Inventory\Usedoil;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use App\Models\Inventory\Usedoil\UoMovementItem;

class UoIncomeSalesDetailSheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $plantId;
    protected $title;
    protected $dateFrom;
    protected $dateUntil;
    protected $userId;

    function __construct($plantId, $title, $dateFrom, $dateUntil, $userId)
    {
        $this->plantId = $plantId;
        $this->title = $title;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
        $this->userId = $userId;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Income Sales Detail Used Oil Report'),
            'data' => UoMovementItem::getDataReport($this->plantId, $this->dateFrom, $this->dateUntil, $this->userId)
        ];

        return view('inventory.usedoil.excel.uo-income-sales-detail-excel', $report_data);
    }

    public function title(): string
    {
        $title = Str::of($this->title)->substr(0, 30);
        return $title . '';
    }
}
