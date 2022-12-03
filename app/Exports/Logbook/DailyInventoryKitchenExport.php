<?php

namespace App\Exports\Logbook;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Logbook\DailyInventoryKitchen;

class DailyInventoryKitchenExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $plant;
    protected $date;

    function __construct($plant, $date)
    {
        $this->plant = $plant;
        $this->date = Carbon::createFromFormat('Y-m-d', $date);
    }

    public function view(): View
    {
        $report_data = [
            'title' => 'Form Daily Inventory Kitchen',
        ];

        $daily_inventory_kitchens = DB::table('daily_inventory_kitchens')
                    ->where('plant_id', $this->plant)
                    ->where('date', $this->date->format('Y-m-d'))
                    ->first();

        if (isset($daily_inventory_kitchens->id)) {
            $dataDetail = DailyInventoryKitchen::getDataDetailById($daily_inventory_kitchens->id);
            $inventoryKitchen = $dataDetail['inventory_kitchen'];
            $detailInventoryKitchens = $dataDetail['detail_inventory_kitchens'];

            $report_data['data'] = [
                'desc' => [
                    'outlet' => $inventoryKitchen->outlet,
                    'date' => $this->date->format('d/m/Y'),
                    'mod' => $inventoryKitchen->mod,
                    'crew_opening' => $inventoryKitchen->crew_opening,
                    'crew_midnight' => $inventoryKitchen->crew_midnight,
                    'crew_closing' => $inventoryKitchen->crew_closing,
                ],
                'table' => $detailInventoryKitchens
            ];
        } else {
            $plant = DB::table('plants')->where('id', $this->plant)->first();
            $report_data['data'] = [
                'desc' => [
                    'outlet' => $plant->short_name,
                    'date' => $this->date->format('d/m/Y'),
                    'mod' => '-',
                    'crew_opening' => '-',
                    'crew_midnight' => '-',
                    'crew_closing' => '-',
                ],
                'table' => []
            ];
        }

        return view('logbook.excel.daily-inventory-kitchen', $report_data);
    }

    public function title(): string
    {
        return $this->date->format('d-m-Y');
    }
}
