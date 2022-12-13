<?php

namespace App\Exports\Inventory;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;
use App\Models\Plant;

class WasteExport implements WithMultipleSheets
{
    protected $companyId;
    protected $plant;
    protected $hide;
    protected $dateFrom;
    protected $dateUntil;
    protected $userId;

    function __construct($companyId, $plant, $hide, $dateFrom, $dateUntil, $userId)
    {
        $this->companyId = $companyId;
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
            $plants = Plant::getPlantAuthUser($this->companyId, $this->userId);
        }

        foreach ($plants as $plant) {

            $count = DB::table('wastes')
                        ->where('company_id', $this->companyId)
                        ->where('plant_id', $plant->id)
                        ->whereBetween('date', [$this->dateFrom . '  00:00:00', $this->dateUntil . '  23:59:59'])
                        ->count();

            if( $count < 1 && $this->hide == 'true'){
                continue;
            }

            $plantCode = Plant::getCodeById($plant->id);
            $sheets[] = new WasteSheet($this->companyId, $plant->id, $this->hide, $plantCode, $this->dateFrom, $this->dateUntil, $this->userId);
        }

        return $sheets;
    }
}
