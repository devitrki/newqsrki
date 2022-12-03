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


class UoGrTransferController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'dc', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);
        $dataview = [
            'menu_id' => $request->query('menuid'),
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
        ];
        return view('inventory.usedoil.uo-gr-transfer', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_movements')
                    ->join('plants as plant_sender', 'plant_sender.id', 'uo_movements.plant_id_sender')
                    ->join('plants as plant_receiver', 'plant_receiver.id', 'uo_movements.plant_id_receiver')
                    ->where('uo_movements.company_id', $userAuth->company_id_selected)
                    ->whereIn('uo_movements.type', [401, 402])
                    ->select(
                        'uo_movements.id', 'uo_movements.document_number', 'uo_movements.date', 'uo_movements.plant_id_sender',
                        'uo_movements.pic_sender', 'uo_movements.note', DB::raw("CONCAT(plant_sender.initital ,' ', plant_sender.short_name) AS plant_sender"),
                        DB::raw("CONCAT(plant_receiver.initital ,' ', plant_receiver.short_name) AS plant_receiver"),
                        'uo_movements.is_reverse', 'uo_movements.reverse_id', 'uo_movements.gr_status', 'uo_movements.pic_receiver',
                    );

        if($request->has('plant-id')){
            if($request->query('plant-id') != ''){
                $query = $query->where('uo_movements.plant_id_receiver', $request->query('plant-id'));
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
                ->addColumn('date_desc', function ($data) {
                    return date("d-m-Y", strtotime($data->date));
                })
                ->rawColumns(['status_reverse', 'status_gr'])
                ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'pic_receiver' => 'required',
                        'gr_date' => 'required',
                    ]);

        $userAuth = $request->get('userAuth');

        $enough = true;
        $materialName = '';
        for ($i=0; $i < sizeof($request->material_code); $i++) {
            $material = DB::table('uo_materials')->where('code', $request->material_code[$i])->first();
            $materialName = $material->name;

            $movementItem = DB::table('uo_movement_items')
                        ->where('uo_movement_id', $request->id)
                        ->where('material_code', $material->code)
                        ->select(['qty'])
                        ->first();

            if( $request->qty[$i] > abs($movementItem->qty) ){
                $enough = false;
                break;
            }
        }

        if( !$enough ){
            $msg = 'Material ' . $materialName . ' ' . Lang::get('should not be more than qty gi');
            return response()->json( Helper::resJSON( 'failed', $msg ) );
        }

        DB::beginTransaction();

        $movementType = '401'; // GR Transfer +

        $uoMovementGI = UoMovement::find($request->id);

        $uoMovement = new UoMovement;
        $uoMovement->company_id = $userAuth->company_id_selected;
        $uoMovement->document_number = Helper::generateDocNumber($movementType, 'uo_movements', 'document_number', 11);
        $uoMovement->plant_id_sender = $uoMovementGI->plant_id_sender;
        $uoMovement->plant_id_receiver = $uoMovementGI->plant_id_receiver;
        $uoMovement->date = date('Y-m-d');
        $uoMovement->type = $movementType;
        $uoMovement->note = $request->note;
        $uoMovement->pic_sender = $uoMovementGI->pic_sender;
        $uoMovement->pic_receiver = $request->pic_receiver;
        $uoMovement->created_by = User::getNameById(Auth::id());
        $uoMovement->created_id = Auth::id();
        if ($uoMovement->save()) {

            $insertMovementItems = [];
            for ($i=0; $i < sizeof($request->material_code); $i++) {
                $material = DB::table('uo_materials')->where('code', $request->material_code[$i])->first();
                $insertMovementItems[] = [
                    'uo_movement_id' => $uoMovement->id,
                    'material_code' => $material->code,
                    'material_name' => $material->name,
                    'material_uom' => $material->uom,
                    'qty' => round($request->qty[$i], 2),
                ];

                // update / create stock
                UoStock::updateStock($userAuth->company_id_selected, $uoMovementGI->plant_id_receiver, $material->code, $request->qty[$i]);

                // update qty gr movement item gi
                DB::table('uo_movement_items')
                    ->where('uo_movement_id', $request->id)
                    ->where('material_code', $material->code)
                    ->update(['qty_gr' => $request->qty[$i]]);
            }

            DB::table('uo_movement_items')->insert($insertMovementItems);

            $uoMovementGI->gr_status = 2;
            $uoMovementGI->pic_receiver = $uoMovement->pic_receiver;
            $uoMovementGI->save();

            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("gr transfer")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("gr transfer")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function getOutstandingByPlantId(Request $request, $plant_id)
    {
        $userAuth = $request->get('userAuth');

        $outstandings =  DB::table('uo_movements')
                            ->join('plants as plant_sender', 'plant_sender.id', 'uo_movements.plant_id_sender')
                            ->whereIn('uo_movements.type', [301])
                            ->where('uo_movements.company_id', $userAuth->company_id_selected)
                            ->where('plant_id_receiver', $plant_id)
                            ->where('is_reverse', 0)
                            ->where('gr_status', '<>', 2)
                            ->select(
                                'uo_movements.id', 'uo_movements.document_number', 'uo_movements.date',
                                'uo_movements.pic_sender', DB::raw("CONCAT(plant_sender.initital ,' ', plant_sender.short_name) AS plant_sender"),
                                'uo_movements.plant_id_receiver'
                            )
                            ->get();

        foreach ($outstandings as $o) {
            $o->date_desc = date("d/m/Y", strtotime($o->date));
        }

        return response()->json($outstandings);
    }

    public function dtbleOutstandingItem($id)
    {
        $query = DB::table('uo_movement_items')
                    ->where('uo_movement_id', $id)
                    ->select(['material_code', 'material_name', 'material_uom', 'qty', 'uo_movement_id']);

        return Datatables::of($query)
                    ->addIndexColumn()
                    ->editColumn('qty', function ($data) {
                        return Helper::convertNumberToInd( abs($data->qty), '');
                    })
                    ->addColumn('qty_input', function ($data) {
                        return '<input type="number" class="form-control form-control-sm mul" name="uogrtransfer[]" value="0" style="min-width: 6rem;">';
                    })
                    ->rawColumns(['qty_input'])
                    ->make();
    }

    public function print($id)
    {
        $qMovement = DB::table('uo_movements')
                        ->join('plants as plant_sender', 'plant_sender.id', 'uo_movements.plant_id_sender')
                        ->join('plants as plant_receiver', 'plant_receiver.id', 'uo_movements.plant_id_receiver')
                        ->where('uo_movements.id', $id)
                        ->select(
                            'uo_movements.id', 'uo_movements.document_number', 'uo_movements.date', 'uo_movements.plant_id_sender',
                            'uo_movements.pic_sender', 'uo_movements.note', DB::raw("CONCAT(plant_sender.initital ,' ', plant_sender.short_name) AS plant_sender"),
                            DB::raw("CONCAT(plant_receiver.initital ,' ', plant_receiver.short_name) AS plant_receiver"),
                            'plant_sender.code as plant_sender_code', 'plant_sender.address as plant_sender_address',
                            'plant_receiver.code as plant_receiver_code', 'plant_receiver.address as plant_receiver_address',
                            'uo_movements.is_reverse', 'uo_movements.reverse_id', 'uo_movements.gr_status'
                        );

        if( $qMovement->count() > 0 ){
            $movement = $qMovement->first();

            $movementItems = DB::table('uo_movement_items')
                                ->where('uo_movement_id', $movement->id)
                                ->where('qty', '<>', 0)
                                ->select(['material_code', 'material_name', 'material_uom', 'qty', 'qty_gr'])
                                ->get();

            $dataview = [
                'movement' => $movement,
                'movementItems' => $movementItems
            ];

            return view('inventory.usedoil.uo-gr-transfer-preview', $dataview);
        } else {
            echo Lang::get("This Transaction Cannot Preview !");
        }
    }
}
