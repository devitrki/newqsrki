<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Inventory\Opname;
use App\Models\Plant;
use App\Models\Stock;
use App\Models\Configuration;
use App\Models\User;

use App\Services\OpnameServiceAppsImpl;
use App\Services\OpnameServiceSapImpl;

class OpnameController extends Controller
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
        return view('inventory.opname', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('opnames')
                    ->join('plants', 'plants.id', 'opnames.plant_id')
                    ->where('opnames.company_id', $userAuth->company_id_selected)
                    ->select(
                        'opnames.id',
                        'opnames.document_number',
                        'opnames.plant_id',
                        'opnames.date',
                        'opnames.note',
                        'opnames.pic',
                        'opnames.pic_update',
                        'opnames.document_number',
                        'opnames.update',
                        'opnames.submit',
                        'opnames.posting_date',
                        'opnames.update_date',
                        'opnames.created_at',
                        'opnames.updated_at',
                        DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS plant"),
                    );

        if($request->has('plant-id')){
            if($request->query('plant-id') != ''){
                $query = $query->where('opnames.plant_id', $request->query('plant-id'));
            }
        }

        if($request->has('from') && $request->has('until')){
            if($request->query('from') != '' && $request->query('until') != ''){
                $query = $query->whereBetween('opnames.created_at', [$request->query('from') . ' 00:00:00', $request->query('until') . ' 23:59:59' ]);
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
                        $sql = "opnames.document_number like ?";
                        $query->orWhereRaw($sql, ["%{$keyword}%"]);
                    })
                    ->addColumn('submit_desc', function ($data) {
                        if ($data->submit != 0) {
                            return '<i class="bx bxs-check-circle text-success"></i>';
                        } else {
                            return '<i class="bx bx-x text-danger"></i>';
                        }
                    })
                    ->addColumn('update_desc', function ($data) {
                        if ($data->update != 0) {
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
                    ->editColumn('pic_update', function ($data) {
                        if( $data->update != 0 ){
                            return $data->pic_update;
                        } else {
                            return '-';
                        }
                    })
                    ->addColumn('update_date', function ($data) {
                        if( $data->update != 0 ){
                            return date("d-m-Y H:i:s", strtotime($data->update_date));
                        } else {
                            return '-';
                        }
                    })
                    ->addColumn('create_date', function ($data) {
                        return date("d-m-Y H:i:s", strtotime($data->created_at));
                    })
                    ->editColumn('posting_date', function ($data) {
                        if( $data->submit != 0 ){
                            return date("d-m-Y H:i:s", strtotime($data->posting_date));
                        } else {
                            return '-';
                        }

                    })
                    ->rawColumns(['submit_desc', 'update_desc'])
                    ->make();
    }

    public function dtbleQty($inputName, $id, $plant)
    {
        $companyId = Plant::getCompanyIdByPlantId($plant);

        $datas = DB::table('material_outlets')
                    ->where('opname', 1)
                    ->where('company_id', $companyId)
                    ->select(
                        'id',
                        'code',
                        'description',
                        'opname_uom as uom',
                        DB::raw("'" . $inputName . "' as inputName")
                    )
                    ->orderBy('code')
                    ->get();

        $data = [];
        foreach ($datas as $q) {
            $qty = 0;
            if( $id != '0' ){
                $qOpnameItem = DB::table('opname_items')
                            ->join('opnames', 'opnames.id', 'opname_items.opname_id')
                            ->where('opname_id', $id)
                            ->where('material_code', $q->code)
                            ->select('opnames.update', DB::raw('qty_final as qty'))
                            ->first();

                $qty = $qOpnameItem->qty;
            }

            $data[] = [
                'id' => $q->id,
                'code' => $q->code,
                'description' => $q->description,
                'uom' => $q->uom,
                'inputName' => $inputName,
                'qty' => $qty + 0
            ];
        }

        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('qty_input', function ($data) {
                    return '<input type="number" class="form-control form-control-sm mul" name="' . $data['inputName'] . '[]" value="' . $data['qty'] . '" style="min-width: 6rem;">';
                })
                ->rawColumns(['qty_input'])
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

        $opname = new Opname;
        $opname->company_id = $userAuth->company_id_selected;
        $opname->plant_id = $request->plant;
        $opname->date = $request->date;
        $opname->pic = $request->pic;
        $opname->note = $request->note;
        if ($opname->save()) {

            $insertItems = [];
            for ($i=0; $i < sizeof($request->material_id); $i++) {
                $material = DB::table('material_outlets')->where('id', $request->material_id[$i])->first();
                $qty = round( Helper::replaceDelimiterNumber($request->qty[$i]), 3);
                $insertItems[] = [
                    'opname_id' => $opname->id,
                    'material_code' => $material->code,
                    'material_name' => $material->description,
                    'uom_first' => $material->opname_uom,
                    'qty_first' => $qty,
                    'uom_final' => $material->opname_uom,
                    'qty_final' => $qty,
                ];
            }

            DB::table('opname_items')->insert($insertItems);

            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("opname")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("opname")]);
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

        $check = DB::table('opnames')
                    ->where('id', $request->id)
                    ->select('update')
                    ->first();

        if ($check->update != '0') {
            $stat = 'failed';
            $msg = Lang::get('Data opname can only be edited once');
            return response()->json( Helper::resJSON( $stat, $msg ) );
        }

        $opname = Opname::find($request->id);
        $opname->plant_id = $request->plant;
        $opname->date = $request->date;
        $opname->update_date = date('Y-m-d H:i:s');
        $opname->pic_update = $request->pic;
        $opname->note = $request->note;
        $opname->update = 1;
        if ($opname->save()) {

            for ($i=0; $i < sizeof($request->material_id); $i++) {

                $material = DB::table('material_outlets')->where('id', $request->material_id[$i])->first();
                $qty = round( Helper::replaceDelimiterNumber($request->qty[$i]), 3);

                DB::table('opname_items')
                    ->where('opname_id', $request->id)
                    ->where('material_code', $material->code)
                    ->update([
                        'uom_update' => $material->opname_uom,
                        'qty_update' => $qty,
                        'uom_final' => $material->opname_uom,
                        'qty_final' => $qty,
                    ]);
            }

            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("opname")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("opname")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $opname = Opname::find($id);
        if ($opname->delete()) {

            // delete items
            DB::table('opname_items')->where('opname_id', $id)->delete();

            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("opname")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("opname")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function preview($id){
        $opnameService = new OpnameServiceSapImpl();
        $response = $opnameService->getPreviewData($id);
        $dataview = [];
        if ($response['status']) {
            $dataview = $response['data'];
            return view('inventory.opname-preview', $dataview)->render();
        } else {
            echo $response['message'];
        }
    }

    public function download($id){

        $opname = DB::table('opnames')->where('id', $id)->first();
        $opname_items = DB::table('opname_items')->where('opname_id', $id)->get();
        $opnameDate    = Helper::DateConvertFormat($opname->date, 'Y-m-d', 'd.m.Y');
        $plantCode = Plant::getCodeById($opname->plant_id);

        $contents = '';
        foreach ($opname_items as $item) {
            $flag   = ($item->qty_final + 0) <> 0 ? '' : 'X';
            $qty    = str_replace('.', ',', $item->qty_final);
            $a      = [$opnameDate, $opnameDate, $plantCode, 'S001', $item->material_code, '', $qty, $item->uom_final, $flag, "\r\n"];
            $contents .= implode("\t", $a);
        }

        $typefile = '.txt';
        $fileName = $plantCode . '-' . date("d_m_Y", strtotime($opname->date)) . $typefile;
        $filePath = 'opname/download/';
        $fileUpload = storage_path('app/public/' . $filePath . $fileName);
        $upload = Storage::disk('public')->put($filePath . $fileName, $contents);

        return response()->download($fileUpload)->deleteFileAfterSend();
    }

    public function submit($id){
        $stat = 'success';
        $msg = Lang::get("message.submit.success", ["data" => Lang::get("opname")]);

        $opnameService = new OpnameServiceSapImpl();
        $response = $opnameService->uploadOpname($id);
        if (!$response['status']) {
            $stat = $response['status'];
            $msg = $response['message'];
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function getOpnameFormat($id) {
        $opname   = DB::table('opnames')->where('id', $id)->first();
        $opnameItems  = DB::table('opname_items')->where('opname_id', $id)->get();

        $data     = [];
        $plantCode = Plant::getCodeById($opname->plant_id);

        $bbq1     = 0;
        $bbq2     = 0;
        $bulgogi1 = 0;
        $bulgogi2 = 0;

        foreach ($opnameItems as $item) {
            switch ($item->material_code) {
                #BBQ
                case '1000144':
                    $bbq1     += $item->qty_final;
                    break;
                case '1000145':
                    $bbq2     += $item->qty_final;
                    break;
                case '9071000':
                    $bbq1     += $item->qty_final;
                    break;
                case '9071001':
                    $bbq1     += ($item->qty_final * 0.95);
                    $bbq2     += ($item->qty_final * 0.05);
                    break;
                case '9071002':
                    $bbq1     += ($item->qty_final * 0.85);
                    $bbq2     += ($item->qty_final * 0.15);
                    break;
                case '9071003':
                    $bbq1     += ($item->qty_final * 0.75);
                    $bbq2     += ($item->qty_final * 0.25);
                    break;
                case '9071004':
                    $bbq1     += ($item->qty_final * 0.65);
                    $bbq2     += ($item->qty_final * 0.35);
                    break;
                case '9071005':
                    $bbq1     += ($item->qty_final * 0.3);
                    $bbq2     += ($item->qty_final * 0.7);
                    break;

                #Bulgogi
                case '1000321':
                    $bulgogi1 += $item->qty_final;
                    break;
                case '1000322':
                    $bulgogi2 += $item->qty_final;
                    break;
                case '9071012':
                    $bulgogi1 += $item->qty_final;
                    break;
                case '9071013':
                    $bulgogi1 += ($item->qty_final * 0.95);
                    $bulgogi2 += ($item->qty_final * 0.05);
                    break;
                case '9071014':
                    $bulgogi1 += ($item->qty_final * 0.85);
                    $bulgogi2 += ($item->qty_final * 0.15);
                    break;
                case '9071015':
                    $bulgogi1 += ($item->qty_final * 0.75);
                    $bulgogi2 += ($item->qty_final * 0.25);
                    break;
                case '9071016':
                    $bulgogi1 += ($item->qty_final * 0.65);
                    $bulgogi2 += ($item->qty_final * 0.35);
                    break;
                case '9071017':
                    $bulgogi1 += ($item->qty_final * 0.3);
                    $bulgogi2 += ($item->qty_final * 0.7);
                    break;
                default:
                    break;
            }
        }

        $sSkipMaterialSap = Configuration::getValueByKeyFor('inventory', 'mat_code_skip_opname');
        $skipMaterialSap = explode( ',', trim($sSkipMaterialSap) );

        foreach ($opnameItems as $item) {
            if (in_array($item->material_code, $skipMaterialSap)) {
                continue;
            }

            switch ($item->material_code) {
                #BBQ
                case '1000144':
                    $item->qty_final  = $bbq1;
                    break;
                case '1000145':
                    $item->qty_final  = $bbq2;
                    break;
                #Bulgogi
                case '1000321':
                    $item->qty_final = $bulgogi1;
                    break;
                case '1000322':
                    $item->qty_final = $bulgogi2;
                    break;
                default:
                    break;
            }

            $data[] = [
                'col01' => date('d.m.Y', strtotime($opname->date)),
                'col02' => $plantCode,
                'col03' => 'S001',
                'col04' => $item->material_code,
                'col05' => '',
                'col06' => str_replace('.', ',', $item->qty_final),
                'col07' => $item->uom_final,
                'col08' => ( $item->qty_final + 0 ) <> 0 ? '' : 'X'
            ];

        }

        return $data;

    }

    public function updateDocNumber(Request $request)
    {
        $request->validate([
                        'document_number' => 'required',
                        'submit' => 'required'
                    ]);

        $opname = Opname::find($request->id);
        $opname->document_number = $request->document_number;
        $opname->submit = $request->submit;
        $opname->posting_date = date('Y-m-d H:i:s');
        if ($opname->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("opname")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("opname")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function openLock($id){

        $opname = Opname::find($id);
        $opname->update = 0;
        if ($opname->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("opname")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("opname")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
