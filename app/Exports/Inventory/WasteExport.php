<?php

namespace App\Exports\Inventory;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Models\Plant;

class WasteExport implements WithMultipleSheets
{
    protected $plant;
    protected $hide;
    protected $dateFrom;
    protected $dateUntil;
    protected $userId;

    function __construct($plant, $hide, $dateFrom, $dateUntil, $userId)
    {
        $this->plant = $plant;
        $this->hide = $hide;
        $this->dateFrom = $dateFrom;
        $this->dateUntil = $dateUntil;
        $this->userId = $userId;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        if($this->plant != '0'){
            $plants = DB::table('plants')->where('id', $this->plant)->select('id')->get();
        } else {
            $plants = Plant::getPlantAuthUser($this->userId);
        }

        foreach ($plants as $plant) {

            $count = DB::table('wastes')
                        ->where('plant_id', $plant->id)
                        ->whereBetween('date', [$this->dateFrom . '  00:00:00', $this->dateUntil . '  23:59:59'])
                        ->count();

            if( $count < 1 && $this->hide == 'true'){
                continue;
            }

            $plantCode = Plant::getCodeById($plant->id);
            $sheets[] = new WasteSheet($plant->id, $this->hide, $plantCode, $this->dateFrom, $this->dateUntil, $this->userId);
        }

        return $sheets;
    }
}
