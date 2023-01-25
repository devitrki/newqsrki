<?php

namespace App\Http\Controllers\Inventory\Usedoil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;

use App\Library\Helper;

use App\Models\Plant;
use App\Models\User;
use App\Models\Inventory\Usedoil\UoMovement;
use App\Models\Inventory\Usedoil\UoStock;

class UoGoodReceiptController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);
        $dataview = [
            'menu_id' => $request->query('menuid'),
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('inventory.usedoil.uo-good-receipt', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_movements')
                    ->join('plants as plant_sender', 'plant_sender.id', 'uo_movements.plant_id_sender')
                    ->where('uo_movements.company_id', $userAuth->company_id_selected)
                    ->whereIn('uo_movements.type', [101, 102])
                    ->select(
                        'uo_movements.id', 'uo_movements.document_number', 'uo_movements.date', 'uo_movements.plant_id_sender',
                        'uo_movements.pic_sender', 'uo_movements.note', DB::raw("CONCAT(plant_sender.initital ,' ', plant_sender.short_name) AS plant_sender"),
                        'uo_movements.is_reverse', 'uo_movements.reverse_id'
                    );

        if($request->has('plant-id')){
            if($request->query('plant-id') != ''){
                $query = $query->where('uo_movements.plant_id_sender', $request->query('plant-id'));
            }
        }

        if($request->has('from') && $request->has('until')){
            if($request->query('from') != '' && $request->query('until') != ''){
                $query = $query->whereBetween('uo_movements.date', [$request->query('from') . ' 00:00:00', $request->query('until') . ' 23:59:59' ]);
            }
        }

        $query = $query->orderByDesc('uo_movements.created_at');

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('status_reverse', function ($data) {
                    if($data->is_reverse != 0){
                        $reverseId = UoMovement::getDocumentNumberReverse($data->reverse_id);
                        if( $reverseId != ''){
                            $status = "<div class = 'badge badge-light'>" . Lang::get('Reverse') . " " . $reverseId . "</div>";
                        } else {
                            $status = "<div class = 'badge badge-danger'>" . Lang::get('Reverse') . "</div>";
                        }
                    } else {
                        $status = "";
                    }
                    return $status;
                })
                ->rawColumns(['status_reverse'])
                ->make();
    }

    public function dtbleView($id)
    {
        $query = DB::table('uo_movement_items')
                    ->where('uo_movement_id', $id)
                    ->select(['material_code', 'material_name', 'material_uom', 'qty']);
        return Datatables::of($query)
                    ->addIndexColumn()
                    ->editColumn('qty', function ($data) {
                        return Helper::convertNumberToInd($data->qty, '');
                    })
                    ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'plant' => 'required',
                        'pic' => 'required',
                    ]);

        $userAuth = $request->get('userAuth');

        DB::beginTransaction();

        $movementType = '101'; // GR +

        $uoMovement = new UoMovement;
        $uoMovement->company_id = $userAuth->company_id_selected;
        $uoMovement->document_number = Helper::generateDocNumber($movementType, 'uo_movements', 'document_number', 11);
        $uoMovement->plant_id_sender = $request->plant;
        $uoMovement->plant_id_receiver = $request->plant;
        $uoMovement->date = date('Y-m-d');
        $uoMovement->type = $movementType;
        $uoMovement->note = $request->note;
        $uoMovement->pic_sender = $request->pic;
        $uoMovement->pic_receiver = $request->pic;
        $uoMovement->created_by = User::getNameById(Auth::id());
        $uoMovement->created_id = Auth::id();
        if ($uoMovement->save()) {

            $insertMovementItems = [];
            for ($i=0; $i < sizeof($request->material_id); $i++) {
                $material = DB::table('uo_materials')->where('id', $request->material_id[$i])->first();
                $insertMovementItems[] = [
                    'uo_movement_id' => $uoMovement->id,
                    'material_code' => $material->code,
                    'material_name' => $material->name,
                    'material_uom' => $material->uom,
                    'qty' => round($request->qty[$i], 2),
                ];
                // update / create stock
                UoStock::updateStock($userAuth->company_id_selected, $request->plant, $material->code, $request->qty[$i]);
            }

            DB::table('uo_movement_items')->insert($insertMovementItems);

            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("good receipt")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("good receipt")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'note' => 'required',
            'pic' => 'required',
        ]);

        $userAuth = $request->get('userAuth');

        DB::beginTransaction();

        $uoMovementReverse = UoMovement::find($id);
        $uoMovementReverse->is_reverse = 1;

        // validate stock
        $materials = DB::table('uo_movement_items')
                        ->where('uo_movement_id', $uoMovementReverse->id)
                        ->get();

        $insertMovementItems = [];
        $valid = true;
        foreach ($materials as $material) {
            $stockCurrent = UoStock::getStockCurrent($userAuth->company_id_selected, $uoMovementReverse->plant_id_sender, $material->material_code);

            if( ($stockCurrent - $material->qty) < 0 ){
                $valid = false;
                break;
            }
        }
        if( !$valid ){
            DB::rollBack();
            $stat = 'failed';
            $msg = "Material " . $material->material_code . ' - ' . $material->material_name . " deficit, cannot canceled.";
            return response()->json( Helper::resJSON( $stat, $msg ) );
        }

        if ($uoMovementReverse->save()) {

            $movementType = '102'; // GR -

            $uoMovement = new UoMovement;
            $uoMovement->company_id = $uoMovementReverse->company_id;
            $uoMovement->document_number = Helper::generateDocNumber($movementType, 'uo_movements', 'document_number', 11);
            $uoMovement->plant_id_sender = $uoMovementReverse->plant_id_sender;
            $uoMovement->plant_id_receiver = $uoMovementReverse->plant_id_receiver;
            $uoMovement->date = date('Y-m-d');
            $uoMovement->type = $movementType;
            $uoMovement->note = $request->note;
            $uoMovement->pic_sender = $request->pic;
            $uoMovement->pic_receiver = $request->pic;
            $uoMovement->is_reverse = 1;
            $uoMovement->reverse_id = $uoMovementReverse->id;
            $uoMovement->created_by = User::getNameById(Auth::id());
            $uoMovement->created_id = Auth::id();
            if ($uoMovement->save()) {

                $materials = DB::table('uo_movement_items')
                                ->where('uo_movement_id', $uoMovementReverse->id)
                                ->get();

                $insertMovementItems = [];
                foreach ($materials as $material) {
                    $insertMovementItems[] = [
                        'uo_movement_id' => $uoMovement->id,
                        'material_code' => $material->material_code,
                        'material_name' => $material->material_name,
                        'material_uom' => $material->material_uom,
                        'qty' => $material->qty * -1,
                    ];
                    // update / create stock
                    UoStock::updateStock($userAuth->company_id_selected, $uoMovementReverse->plant_id_sender, $material->material_code, $material->qty * -1);
                }

                DB::table('uo_movement_items')->insert($insertMovementItems);

                DB::commit();
                $stat = 'success';
                $msg = Lang::get("message.cancel.success", ["data" => Lang::get("good receipt")]);
            } else {
                DB::rollBack();
                $stat = 'failed';
                $msg = Lang::get("message.cancel.failed", ["data" => Lang::get("good receipt")]);
            }
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.cancel.failed", ["data" => Lang::get("good receipt")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
