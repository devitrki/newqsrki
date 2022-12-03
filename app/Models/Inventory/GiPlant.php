<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class GiPlant extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getDataDetailById($id)
    {
        $giPlant = DB::table('gi_plants')
                    ->leftJoin('plants as issuing_plant', 'issuing_plant.id', '=', 'gi_plants.issuing_plant_id')
                    ->leftJoin('plants as receiving_plant', 'receiving_plant.id', '=', 'gi_plants.receiving_plant_id')
                    ->select('issuing_plant.code as issuing_plant_code', 'issuing_plant.type as issuing_plant_type',
                            'receiving_plant.code as receiving_plant_code', 'gi_plants.issuer', 'gi_plants.date', 'gi_plants.requester',
                            'issuing_plant.description as issuing_plant_desc', 'receiving_plant.description as receiving_plant_desc',
                            'issuing_plant.address as issuing_plant_address', 'receiving_plant.address as receiving_plant_address',
                            'gi_plants.document_number', 'gi_plants.document_posto', 'gi_plants.company_id')
                    ->where('gi_plants.id', $id)
                    ->first();
        $giPlantItem = DB::table('gi_plant_items')
                    ->leftJoin('materials', 'materials.id', '=', 'gi_plant_items.material_id')
                    ->select('materials.code as material_code', 'materials.description as material_desc', 'gi_plant_items.qty',
                            'gi_plant_items.uom', 'gi_plant_items.note')
                    ->where('gi_plant_items.gi_plant_id', $id)
                    ->get();

        return [
            'header' => $giPlant,
            'items' => $giPlantItem
        ];
    }
}
