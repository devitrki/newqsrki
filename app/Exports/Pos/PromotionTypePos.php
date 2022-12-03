<?php

namespace App\Exports\Pos;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

use App\Models\Pos\AllPos;


class PromotionTypePos implements FromView, ShouldAutoSize, WithTitle
{
    protected $dateFrom;
    protected $dateUntil;

    function __construct($dateFrom, $dateUntil)
    {
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
    }

    public function view(): View
    {
        $report_data = [
            'title' => \Lang::get('Promotion Type POS Report'),
            'data' => AllPos::getDataPromotionTypeReport($this->dateFrom, $this->dateUntil)
        ];

        return view('pos.excel.promotion-type-pos-excel', $report_data);
    }

    public function title(): string
    {
        return "Promotion Type POS Report";
    }
}
