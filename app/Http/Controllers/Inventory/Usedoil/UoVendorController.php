<?php

namespace App\Http\Controllers\Inventory\Usedoil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;

use App\Library\Helper;

use App\Models\Plant;
use App\Models\Inventory\Usedoil\UoStock;
use App\Models\Inventory\Usedoil\UoVendor;

class UoVendorController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('inventory.usedoil.uo-vendor', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_vendors')
                    ->join('uo_category_prices', 'uo_category_prices.id', 'uo_vendors.uo_category_price_id')
                    ->where('uo_vendors.company_id', $userAuth->company_id_selected)
                    ->select('uo_vendors.id', 'uo_vendors.name', 'uo_vendors.address', 'uo_vendors.city', 'uo_vendors.province',
                            'uo_vendors.contact_person', 'uo_vendors.phone', 'uo_vendors.uo_category_price_id',
                            'uo_category_prices.name as uo_category_price_name');

        return Datatables::of($query)
                    ->addIndexColumn()
                    ->filterColumn('uo_category_price_name', function($query, $keyword) {
                        $sql = "LOWER(uo_category_prices.name) like ?";
                        $query->whereRaw($sql, ["%{strtolower($keyword)}%"]);
                    })
                    ->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_vendors')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name as text'])
                    ->orderBy('name');

        if ($request->has('search')) {
            $query->whereRaw("LOWER(name) like '%" . strtolower($request->search) . "%'");
        }

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
                        'name' => 'required|unique:uo_vendors,name',
                        'category_price' => 'required',
                        'contact_person' => 'required',
                        'phone' => 'required',
                        'address' => 'required',
                        'city' => 'required',
                        'province' => 'required',
                        'id' => 'required',
                    ]);

        $userAuth = $request->get('userAuth');

        DB::beginTransaction();

        $uoVendor = new UoVendor;
        $uoVendor->company_id = $userAuth->company_id_selected;
        $uoVendor->name = $request->name;
        $uoVendor->uo_category_price_id = $request->category_price;
        $uoVendor->name = $request->name;
        $uoVendor->address = $request->address;
        $uoVendor->city = $request->city;
        $uoVendor->province = $request->province;
        $uoVendor->contact_person = $request->contact_person;
        $uoVendor->phone = $request->phone;
        if ($uoVendor->save()) {

            if( $request->plants ){
                $plants = json_decode($request->plants);

                // clear detail
                DB::table('uo_vendor_plants')
                    ->join('plants', 'plants.id', 'uo_vendor_plants.plant_id')
                    ->whereIn('plants.code', $plants)
                    ->delete();

                if( sizeof($plants) > 0 ){
                    $inserts = [];
                    for ($i=0; $i < sizeof($plants); $i++) {
                        $inserts[] = [
                            'uo_vendor_id' => $uoVendor->id,
                            'plant_id' => Plant::getIdByCode($plants[$i])
                        ];
                    }

                    DB::table('uo_vendor_plants')->insert($inserts);
                }
            }

            DB::commit();

            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("vendor used oil")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("vendor used oil")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                        'category_price' => 'required',
                        'contact_person' => 'required',
                        'phone' => 'required',
                        'address' => 'required',
                        'city' => 'required',
                        'province' => 'required',
                        'id' => 'required',
                    ]);

        $uoVendor = UoVendor::find($request->id);
        $uoVendor->name = $request->name;
        $uoVendor->uo_category_price_id = $request->category_price;
        $uoVendor->name = $request->name;
        $uoVendor->address = $request->address;
        $uoVendor->city = $request->city;
        $uoVendor->province = $request->province;
        $uoVendor->contact_person = $request->contact_person;
        $uoVendor->phone = $request->phone;
        if ($uoVendor->save()) {

            // clear detail
            DB::table('uo_vendor_plants')->where('uo_vendor_id', $uoVendor->id)->delete();

            if( $request->plants ){
                $plants = json_decode($request->plants);

                // clear detail
                DB::table('uo_vendor_plants')
                    ->join('plants', 'plants.id', 'uo_vendor_plants.plant_id')
                    ->whereIn('plants.code', $plants)
                    ->delete();

                if( sizeof($plants) > 0 ){
                    $inserts = [];
                    for ($i=0; $i < sizeof($plants); $i++) {
                        $inserts[] = [
                            'uo_vendor_id' => $uoVendor->id,
                            'plant_id' => Plant::getIdByCode($plants[$i])
                        ];
                    }

                    DB::table('uo_vendor_plants')->insert($inserts);
                }
            }

            DB::commit();

            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("vendor used oil")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("vendor used oil")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        if( Helper::used( $id, 'vendor used oil_id', ['profiles'] ) ){
            return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        }

        $uoVendor = UoVendor::find($id);
        if ($uoVendor->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("vendor used oil")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("vendor used oil")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function getPlantVendor($id)
    {
        $data = DB::table('uo_vendor_plants')
                    ->join('plants', 'plants.id', 'uo_vendor_plants.plant_id')
                    ->select('plants.short_name', 'uo_vendor_plants.plant_id', 'plants.initital', 'plants.code')
                    ->where('uo_vendor_id', $id)
                    ->get();

        $stat = 'success';

        return response()->json( Helper::resJSON( $stat, '', $data ) );

    }

    public function getVendorPlant($plantId)
    {
        $data = DB::table('uo_vendor_plants')
                    ->join('uo_vendors', 'uo_vendors.id', 'uo_vendor_plants.uo_vendor_id')
                    ->select('uo_vendors.id', 'uo_vendors.name')
                    ->where('uo_vendor_plants.plant_id', $plantId)
                    ->first();

        $stat = 'success';

        return response()->json( Helper::resJSON( $stat, '', $data ) );

    }

    public function dtblePrice($inputName, $vendorId, $plantId)
    {
        $categoryPriceId = 0;

        $qVendor = DB::table('uo_vendors')
                    ->where('id', $vendorId)
                    ->select('uo_category_price_id', 'company_id');

        if( $qVendor->count() > 0 ){
            $vendor = $qVendor->first();
            $categoryPriceId = $vendor->uo_category_price_id;
        }

        $cats = DB::table('uo_category_price_details')
                    ->join('uo_materials', 'uo_materials.id', 'uo_category_price_details.uo_material_id')
                    ->where('uo_category_price_id', $categoryPriceId)
                    ->select('uo_materials.id', 'uo_materials.code', 'uo_materials.name', 'uo_materials.uom',
                        DB::raw("'"  . $inputName . "' as inputName"), 'uo_category_price_details.price'
                    )
                    ->get();
        $data = [];
        foreach ($cats as $c) {

            $stock = UoStock::getStockCurrent($vendor->company_id, $plantId, $c->code);

            $data[] = [
                'id' => $c->id,
                'code' => $c->code,
                'name' => $c->name,
                'uom' => $c->uom,
                'inputName' => $inputName,
                'price' => $c->price,
                'price_desc' => Helper::convertNumberToInd($c->price, '', 0),
                'stock' => $stock,
                'stock_desc' => Helper::convertNumberToInd($stock, '', 2),
            ];
        }

        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('qty_input', function ($data) {
                    return '<input type="number" class="form-control form-control-sm mul" name="' . $data['inputName'] . '[]" value="0" style="min-width: 6rem;">';
                })
                ->rawColumns(['qty_input'])
                ->make();
    }
}
