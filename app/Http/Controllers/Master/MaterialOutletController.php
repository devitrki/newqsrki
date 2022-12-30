<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Imports\Master\MaterialOutletImport;

use App\Models\MaterialOutlet;
use App\Models\Material;
use App\Models\Company;

class MaterialOutletController extends Controller
{
    public function index(Request $request){
        $first_company_id = Company::getFirstCompanyIdSelect();
        $first_company_name = Company::getNameById($first_company_id);
        $dataview = [
            'menu_id' => $request->query('menuid'),
            'first_company_id' => $first_company_id,
            'first_company_name' => $first_company_name,
        ];
        return view('master.material-outlet', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('material_outlets')
                    ->join('materials', 'materials.code', 'material_outlets.code')
                    ->where('materials.company_id', $userAuth->company_id_selected)
                    ->where('material_outlets.company_id', $userAuth->company_id_selected)
                    ->select(['material_outlets.id', 'material_outlets.code', 'material_outlets.description',
                        'material_outlets.opname', 'material_outlets.opname_uom', 'material_outlets.waste',
                        'material_outlets.waste_flag', 'material_outlets.waste_uom', 'materials.id as material_id',
                        DB::raw("CONCAT(materials.code , ' - ' , materials.description) as material_name"),
                        ]);

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('opname_desc', function ($data) {
                    if ($data->opname != 0) {
                        return '<i class="bx bxs-check-circle text-success"></i>';
                    } else {
                        return '<i class="bx bxs-x-circle text-danger"></i>';
                    }
                })
                ->addColumn('waste_desc', function ($data) {
                    if ($data->waste != 0) {
                        return '<i class="bx bxs-check-circle text-success"></i>';
                    } else {
                        return '<i class="bx bxs-x-circle text-danger"></i>';
                    }
                })
                ->addColumn('waste_flag_desc', function ($data) {
                    if ($data->waste_flag != 0) {
                        return 'x';
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['opname_desc', 'waste_desc'])
                ->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('material_outlets')
                    ->select(DB::raw("code as id, CONCAT(code,' - ',description) as text"))
                    ->where('company_id', $userAuth->company_id_selected);

        if ($request->has('type')) {
            if ($request->query('type') == 'opname') {
                $query = $query->where('opname', 1);
            }
        } else {
            $query = $query->where('waste', 1);
        }

        $query = $query->where(function($query) use ($request){
            if ($request->has('search')) {
                $query->whereRaw("LOWER(code) like '%" . strtolower($request->search) . "%'");
                $query->orWhereRaw("LOWER(description) like '%" . strtolower($request->search) . "%'");
            }
        });

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        if ($request->query('init') == 'false' && !$request->has('search')) {
            $data = [];
        } else {
            $data = $query->get();
        }

        if ($request->has('ext')) {
            if ($request->query('ext') == 'all') {
                if (!is_array($data)) {
                    $data->prepend(['id' => 0, 'text' => Lang::get('All')]);
                }
            }
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
                        'material' => 'required',
                        'status_opname' => 'required',
                        'uom_opname' => 'required',
                        'status_waste' => 'required',
                        'uom_waste' => 'required',
                        'flag_waste' => 'required',
                    ]);

        $material = Material::find($request->material);

        $userAuth = $request->get('userAuth');

        // check material already exist or not
        $countMat = DB::table('material_outlets')
                        ->where('code', $material->code)
                        ->where('company_id', $userAuth->company_id_selected)
                        ->count();

        if( $countMat > 0 ){
            $stat = 'failed';
            $msg = Lang::get("validation.unique", ["attribute" => "material"]);
            return response()->json( Helper::resJSON( $stat, $msg ) );
        }

        $materialOutlet = new MaterialOutlet;
        $materialOutlet->company_id = $userAuth->company_id_selected;
        $materialOutlet->code = $material->code;
        $materialOutlet->description = $material->description;
        $materialOutlet->opname = ($request->status_opname == 'true') ? 1 : 0;
        $materialOutlet->opname_uom = $request->uom_opname;
        $materialOutlet->waste = ($request->status_waste == 'true') ? 1 : 0;
        $materialOutlet->waste_uom = $request->uom_waste;
        $materialOutlet->waste_flag = ($request->flag_waste == 'true') ? 1 : 0;
        if ($materialOutlet->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("material outlet")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("material outlet")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                    'status_opname' => 'required',
                    'uom_opname' => 'required',
                    'status_waste' => 'required',
                    'uom_waste' => 'required',
                ]);

        $materialOutlet = MaterialOutlet::find($request->id);
        $materialOutlet->opname = ($request->status_opname == 'true') ? 1 : 0;
        $materialOutlet->opname_uom = $request->uom_opname;
        $materialOutlet->waste = ($request->status_waste == 'true') ? 1 : 0;
        $materialOutlet->waste_uom = $request->uom_waste;
        $materialOutlet->waste_flag = ($request->flag_waste == 'true') ? 1 : 0;
        if ($materialOutlet->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("material outlet")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("material outlet")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $materialOutlet = MaterialOutlet::find($id);
        if ($materialOutlet->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("material outlet")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("material outlet")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function getDataMaterial(Request $request, $code)
    {
        $userAuth = $request->get('userAuth');

        $data = DB::table('material_outlets')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->where('code', $code)
                    ->select(
                        'id',
                        'code',
                        'description',
                        'waste_uom as uom',
                    )
                    ->first();

        return response()->json($data);
    }

    public function getWasteMaterial(Request $request, $plant)
    {
        $userAuth = $request->get('userAuth');

        $data = DB::table('material_outlets')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->where('waste', 1)
                    ->select(DB::raw("code as id, CONCAT(code,' - ',description) as text"))
                    ->get();

        return response()->json($data);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required'
        ]);

        $stat = 'success';
        $msg = Lang::get("message.import.success", ["data" => Lang::get("Material Outlet")]);

        if ($request->file('file_excel')) {
            try {
                $import = Excel::import(new MaterialOutletImport, request()->file('file_excel'));
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                $stat = 'failed';
                $msg = 'Error row ' . $failures[0]->row() .  ' column ' . $failures[0]->attribute() . ' : ' . $failures[0]->errors()[0];
            }
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }
}
