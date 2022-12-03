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
use App\Models\Inventory\Usedoil\UoSaldoVendor;
use App\Models\Inventory\Usedoil\UoSaldoVendorHistory;
use App\Models\Inventory\Usedoil\UoVendor;

class UoSalesController extends Controller
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
        return view('inventory.usedoil.uo-sales', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_movements')
                    ->join('plants as plant_sender', 'plant_sender.id', 'uo_movements.plant_id_sender')
                    ->join('plants as plant_receiver', 'plant_receiver.id', 'uo_movements.plant_id_receiver')
                    ->where('uo_movements.company_id', $userAuth->company_id_selected)
                    ->whereIn('uo_movements.type', [201, 202])
                    ->select(
                        'uo_movements.id', 'uo_movements.document_number', 'uo_movements.date', 'uo_movements.plant_id_sender',
                        'uo_movements.pic_sender', 'uo_movements.note', DB::raw("CONCAT(plant_sender.initital ,' ', plant_sender.short_name) AS plant_sender"),
                        DB::raw("CONCAT(plant_receiver.initital ,' ', plant_receiver.short_name) AS plant_receiver"),
                        'uo_movements.is_reverse', 'uo_movements.reverse_id', 'uo_movements.subtotal'
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
                ->addColumn('date_desc', function ($data) {
                    return date("d-m-Y", strtotime($data->date));
                })
                ->addColumn('subtotal_sales', function ($data) {
                    return Helper::convertNumberToInd($data->subtotal, 'Rp ', 0);
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

        $enough = true;
        $stockCurrent = 0;
        $materialName = '';
        for ($i=0; $i < sizeof($request->material_id); $i++) {
            $material = DB::table('uo_materials')->where('id', $request->material_id[$i])->first();
            $materialName = $material->name;
            // check stock material plant
            $stockCurrent = UoStock::getStockCurrent($userAuth->company_id_selected, $request->plant, $material->code);

            if( $stockCurrent < $request->qty[$i] ){
                $enough = false;
                break;
            }
        }

        if( !$enough ){
            $msg = 'Material ' . $materialName . ' ' . Lang::get('stock is deficit') . ', ' . Lang::get('stock current') . ' ' . $stockCurrent;
            return response()->json( Helper::resJSON( 'failed', $msg ) );
        }

        DB::beginTransaction();

        $movementType = '201'; // Sales -

        $uoMovement = new UoMovement;
        $uoMovement->company_id = $userAuth->company_id_selected;
        $uoMovement->document_number = Helper::generateDocNumber($movementType, 'uo_movements', 'document_number', 11);
        $uoMovement->plant_id_sender = $request->plant;
        $uoMovement->plant_id_receiver = $request->plant;
        $uoMovement->date = date('Y-m-d');
        $uoMovement->type = $movementType;
        $uoMovement->note = $request->note;
        $uoMovement->pic_sender = $request->pic;
        $uoMovement->pic_receiver = '';
        $uoMovement->created_by = User::getNameById(Auth::id());
        $uoMovement->created_id = Auth::id();
        if ($uoMovement->save()) {

            $insertMovementItems = [];
            $subtotal = 0;
            for ($i=0; $i < sizeof($request->material_id); $i++) {
                if($request->qty[$i] > 0){
                    $material = DB::table('uo_materials')->where('id', $request->material_id[$i])->first();
                    $insertMovementItems[] = [
                        'uo_movement_id' => $uoMovement->id,
                        'material_code' => $material->code,
                        'material_name' => $material->name,
                        'material_uom' => $material->uom,
                        'qty' => round($request->qty[$i] * -1, 2),
                        'price' => $request->price[$i]
                    ];
                    // update / create stock
                    UoStock::updateStock($userAuth->company_id_selected, $request->plant, $material->code, $request->qty[$i] * -1);

                    $subtotal = $subtotal + round($request->qty[$i], 2) * $request->price[$i];
                }
            }

            // get vendor plant
            $vendor = DB::table('uo_vendor_plants')
                        ->select('uo_vendor_plants.uo_vendor_id')
                        ->where('uo_vendor_plants.plant_id', $request->plant)
                        ->first();

            $saldoVendor = UoSaldoVendor::getSaldoVendor($vendor->uo_vendor_id);

            if( $saldoVendor < $subtotal ){
                DB::rollBack();
                $msg = Lang::get('Vendor balance is not enough, the maximum total purchase is') . ' ' . Helper::convertNumberToInd($saldoVendor, 'Rp ', 0);
                return response()->json( Helper::resJSON( 'failed', $msg ) );
            }

            $subtotal = round($subtotal, 0);

            // sub saldo
            $saldoVendorNow = UoVendor::updateSaldoVendor($vendor->uo_vendor_id, $subtotal * -1);

            // insert to saldo histories
            $uoSaldoVendorHistory = new UoSaldoVendorHistory;
            $uoSaldoVendorHistory->uo_vendor_id = $vendor->uo_vendor_id;
            $uoSaldoVendorHistory->date = date('Y-m-d');
            $uoSaldoVendorHistory->transaction_type = 0;
            $uoSaldoVendorHistory->transaction_id = $uoMovement->id;
            $uoSaldoVendorHistory->nominal = $subtotal;
            $uoSaldoVendorHistory->saldo = $saldoVendorNow;
            $uoSaldoVendorHistory->description = 'Sales ' . Plant::getShortNameById($request->plant);
            $uoSaldoVendorHistory->save();

            DB::table('uo_movement_items')->insert($insertMovementItems);

            $uoMovement->uo_vendor_id = $vendor->uo_vendor_id;
            $uoMovement->subtotal = $subtotal;
            $uoMovement->tax = $subtotal * 0.1;
            $uoMovement->total = $subtotal + ($subtotal * 0.1);
            $uoMovement->save();

            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("sales")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("sales")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function printDeliveryOrder($id)
    {
        $qMovement = DB::table('uo_movements')
                        ->join('plants as plant_sender', 'plant_sender.id', 'uo_movements.plant_id_sender')
                        ->join('uo_vendors', 'uo_vendors.id', 'uo_movements.uo_vendor_id')
                        ->where('uo_movements.id', $id)
                        ->select(
                            'uo_movements.id', 'uo_movements.document_number', 'uo_movements.date', 'uo_movements.plant_id_sender',
                            'uo_movements.pic_sender', 'uo_movements.note', DB::raw("CONCAT(plant_sender.initital ,' ', plant_sender.short_name) AS plant_sender"),
                            'plant_sender.code as plant_sender_code', 'plant_sender.address as plant_sender_address',
                            'uo_movements.is_reverse', 'uo_movements.subtotal', 'uo_vendors.name as vendor_name', 'uo_vendors.address as vendor_address'
                        );

        if( $qMovement->count() > 0 ){
            $movement = $qMovement->first();

            $movementItems = DB::table('uo_movement_items')
                                ->where('uo_movement_id', $movement->id)
                                ->where('qty', '<>', 0)
                                ->select(['material_code', 'material_name', 'material_uom', 'qty', 'price'])
                                ->get();

            $dataview = [
                'movement' => $movement,
                'movementItems' => $movementItems
            ];

            return view('inventory.usedoil.uo-sales-delivery-order', $dataview);
        } else {
            echo Lang::get("This Transaction Cannot Print !");
        }
    }

    public function printInvoice($id)
    {
        $qMovement = DB::table('uo_movements')
                        ->join('plants as plant_sender', 'plant_sender.id', 'uo_movements.plant_id_sender')
                        ->join('uo_vendors', 'uo_vendors.id', 'uo_movements.uo_vendor_id')
                        ->where('uo_movements.id', $id)
                        ->select(
                            'uo_movements.id', 'uo_movements.document_number', 'uo_movements.date', 'uo_movements.plant_id_sender',
                            'uo_movements.pic_sender', 'uo_movements.note', DB::raw("CONCAT(plant_sender.initital ,' ', plant_sender.short_name) AS plant_sender"),
                            'plant_sender.code as plant_sender_code', 'plant_sender.address as plant_sender_address',
                            'uo_movements.is_reverse', 'uo_movements.subtotal', 'uo_vendors.name as vendor_name', 'uo_vendors.address as vendor_address'
                        );

        if( $qMovement->count() > 0 ){
            $movement = $qMovement->first();

            $movementItems = DB::table('uo_movement_items')
                                ->where('uo_movement_id', $movement->id)
                                ->where('qty', '<>', 0)
                                ->select(['material_code', 'material_name', 'material_uom', 'qty', 'price'])
                                ->get();

            $dataview = [
                'movement' => $movement,
                'movementItems' => $movementItems
            ];

            return view('inventory.usedoil.uo-sales-invoice', $dataview);
        } else {
            echo Lang::get("This Transaction Cannot Print !");
        }
    }

    public function printInvoiceCopy($id)
    {
        $qMovement = DB::table('uo_movements')
                        ->join('plants as plant_sender', 'plant_sender.id', 'uo_movements.plant_id_sender')
                        ->join('uo_vendors', 'uo_vendors.id', 'uo_movements.uo_vendor_id')
                        ->where('uo_movements.id', $id)
                        ->select(
                            'uo_movements.id', 'uo_movements.document_number', 'uo_movements.date', 'uo_movements.plant_id_sender',
                            'uo_movements.pic_sender', 'uo_movements.note', DB::raw("CONCAT(plant_sender.initital ,' ', plant_sender.short_name) AS plant_sender"),
                            'plant_sender.code as plant_sender_code', 'plant_sender.address as plant_sender_address',
                            'uo_movements.is_reverse', 'uo_movements.subtotal', 'uo_vendors.name as vendor_name', 'uo_vendors.address as vendor_address',
                            'uo_movements.subtotal'
                        );

        if( $qMovement->count() > 0 ){
            $movement = $qMovement->first();

            $movementItems = DB::table('uo_movement_items')
                                ->where('uo_movement_id', $movement->id)
                                ->where('qty', '<>', 0)
                                ->select(['material_code', 'material_name', 'material_uom', 'qty', 'price'])
                                ->get();

            $dataview = [
                'movement' => $movement,
                'movementItems' => $movementItems
            ];

            return view('inventory.usedoil.uo-sales-invoice-copy', $dataview);
        } else {
            echo Lang::get("This Transaction Cannot Print !");
        }
    }
}
