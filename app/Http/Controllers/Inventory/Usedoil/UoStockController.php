<?php

namespace App\Http\Controllers\Inventory\Usedoil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

class UoStockController extends Controller
{
    public function dtbleCurrent($plantId)
    {
        $query = DB::table('uo_stocks')
                    ->join('uo_materials', 'uo_materials.code', 'uo_stocks.material_code')
                    ->join('plants', 'plants.id', 'uo_stocks.plant_id')
                    ->where('uo_stocks.plant_id', $plantId)
                    ->select('uo_materials.code', 'uo_materials.name', 'uo_materials.uom', 'uo_stocks.stock',
                        DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS plant"));
        return Datatables::of($query)->addIndexColumn()->make();
    }
}
