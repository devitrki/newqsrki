<?php

namespace App\Exports\Inventory\Usedoil;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Models\Plant;

class UoIncomeSalesDetailExport implements WithMultipleSheets
{
    protected $plant;
    protected $dateFrom;
    protected $dateUntil;
    protected $userId;

    function __construct($plant, $dateFrom, $dateUntil, $userId)
    {
        $this->plant = $plant;
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

            $count = DB::table('uo_movements')
                        ->whereIn('type', [201])
                        ->where('plant_id_sender', $plant->id)
                        ->whereBetween('date', [$this->dateFrom . '  00:00:00', $this->dateUntil . '  23:59:59'])
                        ->count();

            if( $count < 1){
                continue;
            }

            $plantCode = Plant::getCodeById($plant->id);
            $sheets[] = new UoIncomeSalesDetailSheet($plant->id, $plantCode, $this->dateFrom, $this->dateUntil, $this->userId);
        }

        return $sheets;
    }
}
