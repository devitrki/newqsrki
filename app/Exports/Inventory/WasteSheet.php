<?php

namespace App\Exports\Inventory;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use App\Models\Inventory\Waste;


class WasteSheet implements FromView, ShouldAutoSize, WithTitle
{
    protected $plantId;
    protected $hide;
    protected $title;
    protected $dateFrom;
    protected $dateUntil;
    protected $userId;

    function __construct($plantId, $hide, $title, $dateFrom, $dateUntil, $userId)
    {
        $this->plantId = $plantId;
        $this->hide = $hide;
        $this->title = $title;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
        $this->userId = $userId;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Income Sales Detail Used Oil Report'),
            'data' => Waste::getDataReport($this->plantId, $this->hide, $this->dateFrom, $this->dateUntil, $this->userId)
        ];

        return view('inventory.excel.waste-excel', $report_data);
    }

    public function title(): string
    {
        $title = Str::of($this->title)->substr(0, 30);
        return $title . '';
    }
}
