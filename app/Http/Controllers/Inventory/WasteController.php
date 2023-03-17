<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Inventory\Waste;
use App\Models\Plant;

use App\Services\WasteServiceSapImpl;

class WasteController extends Controller
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
        return view('inventory.waste', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('wastes')
                    ->join('plants', 'plants.id', 'wastes.plant_id')
                    ->where('wastes.company_id', $userAuth->company_id_selected)
                    ->select(
                        'wastes.id',
                        'wastes.document_number',
                        'wastes.plant_id',
                        'wastes.date',
                        'wastes.pic',
                        'wastes.submit',
                        'wastes.posting_date',
                        'wastes.created_at',
                        DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS plant"),
                    )
                    ->orderByDesc('wastes.date')
                    ->orderByDesc('wastes.id');

        if($request->has('plant-id')){
            if($request->query('plant-id') != ''){
                $query = $query->where('wastes.plant_id', $request->query('plant-id'));
            }
        }

        if($request->has('from') && $request->has('until')){
            if($request->query('from') != '' && $request->query('until') != ''){
                $query = $query->whereBetween('wastes.created_at', [$request->query('from') . ' 00:00:00', $request->query('until') . ' 23:59:59' ]);
            }
        }

        return Datatables::of($query)
                    ->addIndexColumn()
                    ->filterColumn('plant', function($query, $keyword) {
                        $sql = "plants.initital like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                        $sql = "plants.short_name like ?";
                        $query->orWhereRaw($sql, ["%{$keyword}%"]);
                    })
                    ->filterColumn('document_number_desc', function($query, $keyword) {
                        $sql = "wastes.document_number like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                    })
                    ->addColumn('submit_desc', function ($data) {
                        if ($data->submit != 0) {
                            return '<i class="bx bxs-check-circle text-success"></i>';
                        } else {
                            return '<i class="bx bx-x text-danger"></i>';
                        }
                    })
                    ->addColumn('date_desc', function ($data) {
                        return date("d-m-Y", strtotime($data->date));
                    })
                    ->addColumn('document_number_desc', function ($data) {
                        if( $data->document_number != '' ){
                            return $data->document_number;
                        } else {
                            return '-';
                        }
                    })
                    ->rawColumns(['submit_desc'])
                    ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'plant' => 'required',
                        'date' => 'required',
                        'pic' => 'required',
                    ]);

        $userAuth = $request->get('userAuth');

        DB::beginTransaction();

        $waste = new Waste;
        $waste->company_id = $userAuth->company_id_selected;
        $waste->plant_id = $request->plant;
        $waste->date = $request->date;
        $waste->pic = $request->pic;
        if ($waste->save()) {

            $insertItems = [];
            for ($i=0; $i < sizeof($request->material_id); $i++) {
                $material = DB::table('material_outlets')->where('id', $request->material_id[$i])->first();
                $qty = round( Helper::replaceDelimiterNumber($request->qty[$i]), 3);

                $insertItems[] = [
                    'waste_id' => $waste->id,
                    'material_code' => $material->code,
                    'material_name' => $material->description,
                    'uom' => $material->waste_uom,
                    'qty' => $qty,
                    'note' => $request->note[$i]
                ];
            }

            DB::table('waste_items')->insert($insertItems);

            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("waste")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("waste")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'plant' => 'required',
                        'date' => 'required',
                        'pic' => 'required',
                    ]);

        $waste = Waste::find($request->id);
        $waste->plant_id = $request->plant;
        $waste->date = $request->date;
        $waste->pic = $request->pic;
        if ($waste->save()) {

            DB::table('waste_items')->where('waste_id', $request->id)->delete();

            $insertItems = [];
            for ($i=0; $i < sizeof($request->material_id); $i++) {
                $material = DB::table('material_outlets')->where('id', $request->material_id[$i])->first();
                $qty = round( Helper::replaceDelimiterNumber($request->qty[$i]), 3);

                $insertItems[] = [
                    'waste_id' => $waste->id,
                    'material_code' => $material->code,
                    'material_name' => $material->description,
                    'uom' => $material->waste_uom,
                    'qty' => $qty,
                    'note' => $request->note[$i]
                ];
            }

            DB::table('waste_items')->insert($insertItems);

            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("waste")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("waste")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $waste = Waste::find($id);
        if ($waste->delete()) {

            // delete items
            DB::table('waste_items')->where('waste_id', $id)->delete();

            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("waste")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("waste")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function preview($id){

        $waste = DB::table('wastes')
                    ->where('id', $id)
                    ->first();

        $waste_items  = DB::table('waste_items')
                            ->where('waste_id', $id)
                            ->get();

        $dataview = [
            'waste' => $waste,
            'waste_items' => $waste_items,
            'plant' => Plant::getShortNameById($waste->plant_id)
        ];
        return view('inventory.waste-preview', $dataview)->render();
    }

    public function download($id){

        $waste = DB::table('wastes')->where('id', $id)->first();
        $waste_items = DB::table('waste_items')->where('waste_id', $id)->get();
        $wasteDate    = Helper::DateConvertFormat($waste->date, 'Y-m-d', 'd.m.Y');
        $plantCustCode = Plant::getCustomerCodeById($waste->plant_id);
        $companyId = Plant::getCompanyIdByPlantId($waste->plant_id);

        $contents = '';
        foreach ($waste_items as $item) {
            $material = DB::table('material_outlets')
                            ->where('company_id', $companyId)
                            ->where('code', $item->material_code)
                            ->select('waste_flag')
                            ->first();

            $qty    = str_replace(',', '.', ($item->qty + 0));
            if( $material->waste_flag != 0 ){
                $a = [$wasteDate, '551', $item->material_code, $plantCustCode, $qty, $item->uom, 'x', "\r\n"];
            } else {
                $a = [$wasteDate, '551', $item->material_code, $plantCustCode, $qty, $item->uom, "\r\n"];
            }
            $contents .= implode(';', $a);
        }

        $typefile = '.txt';
        $fileName = $plantCustCode . '-' . date("d_m_Y", strtotime($waste->date)) . $typefile;
        $filePath = 'waste/download/';
        $fileUpload = storage_path('app/public/' . $filePath . $fileName);
        $upload = Storage::disk('public')->put($filePath . $fileName, $contents);

        return response()->download($fileUpload)->deleteFileAfterSend();
    }

    public function submit(Request $request, $id){
        $stat = 'success';
        $msg = Lang::get("message.submit.success", ["data" => Lang::get("waste")]);

        $userAuth = $request->get('userAuth');

        $wasteService = new WasteServiceSapImpl();
        $response = $wasteService->uploadWaste($userAuth->company_id_selected, $id);
        if (!$response['status']) {
            $stat = $response['status'];
            $msg = $response['message'];
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function updateDocNumber(Request $request)
    {
        $request->validate([
                        'document_number' => 'required',
                        'submit' => 'required'
                    ]);

        $waste = Waste::find($request->id);
        $waste->document_number = $request->document_number;
        $waste->submit = $request->submit;
        $waste->posting_date = date('Y-m-d H:i:s');
        if ($waste->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("waste")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("waste")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function getItemsWaste($id)
    {
        $waste = DB::table('wastes')->where('id', $id)->first();
        $items = DB::table('waste_items')
                    ->where('waste_id', $id)
                    ->get();

        $companyId = Plant::getCompanyIdByPlantId($waste->plant_id);

        foreach ($items as $item) {
            $material = DB::table('material_outlets')
                            ->where('company_id', $companyId)
                            ->where('code', $item->material_code)
                            ->select('id')
                            ->first();

            $item->material_id = $material->id;
            $item->qty = $item->qty + 0;
            if($item->note == null || $item->note == ''){
                $item->note = '';
            }

        }

        return response()->json($items);
    }
}
