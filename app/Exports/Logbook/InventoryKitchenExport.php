<?php

namespace App\Exports\Logbook;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Logbook\DailyInventoryKitchenExport;
use Carbon\Carbon;

class InventoryKitchenExport implements WithMultipleSheets
{
    use Exportable;

    protected $plant;
    protected $from;
    protected $until;

    function __construct($plant, $from, $until)
    {
        $this->plant = $plant;
        $this->from = $from;
        $this->until = $until;
    }

    public function sheets(): array
    {
        $sheets = [];

        $until = Carbon::createFromFormat('Y/m/d', $this->until);
        $date = Carbon::createFromFormat('Y/m/d', $this->from);
        $loop = true;

        while ($loop) {
            $sheets[] = new DailyInventoryKitchenExport($this->plant, $date->format('Y-m-d'));
            $date->addDay();
            if ($date->greaterThan($until)) {
                $loop = false;
            }
        }

        return $sheets;
    }
}
