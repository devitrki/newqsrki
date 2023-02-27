<?php

namespace App\Http\Controllers\ExternalVendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;

use App\Library\Helper;
use App\Models\ExternalVendor\TemplateSales;
use App\Models\ExternalVendor\TemplateSalesDetail;

class TemplateSalesController extends Controller
{
    public function index(Request $request){
        $templateSalesFieldNames = TemplateSales::getTemplateSalesFieldNames();
        $templateSalesFieldNameOptions = [];
        foreach ($templateSalesFieldNames as $k => $v) {
            $templateSalesFieldNameOptions[] = [
                'id' => $k,
                'text' => $v
            ];
        }

        $dataview = [
            'menu_id' => $request->query('menuid'),
            'template_sales_field_name_options' => $templateSalesFieldNameOptions
        ];
        return view('externalVendors.template-sales', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('template_sales')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name']);

        return Datatables::of($query)->addIndexColumn()->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('template_sales')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name as text']);

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
                        'name' => 'required|unique:template_sales,name',
                    ]);

        $userAuth = $request->get('userAuth');

        $templateSales = new TemplateSales;
        $templateSales->company_id = $userAuth->company_id_selected;
        $templateSales->name = $request->name;
        if ($templateSales->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("template sales")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("template sales")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                    ]);

        $templateSales = TemplateSales::find($request->id);
        $templateSales->name = $request->name;
        if ($templateSales->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("template sales")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("template sales")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        // if( Helper::used( $id, 'template_sales_id', [''] ) ){
        //     return response()->json( Helper::resJSON( 'failed', Lang::get('validation.used') ) );
        // }

        $templateSales = TemplateSales::find($id);
        if ($templateSales->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("template sales")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("template sales")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // configuration
    public function dtbleConf($id)
    {
        $query = DB::table('template_sales_details')
                    ->where('template_sale_id', $id)
                    ->select(['id', 'data', 'field_name']);

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('data_desc', function ($data) {
                    $templateSalesFieldNames = TemplateSales::getTemplateSalesFieldNames();
                    return $templateSalesFieldNames[$data->data];
                })
                ->make();
    }

    public function storeConf(Request $request, $templateSaleId)
    {
        $request->validate([
            'id' => 'required',
            'data' => 'required',
            'field_name' => 'required'
        ]);

        if ($request->id && $request->id != 0 && $request->id != '') {
            $templateSalesDetail = TemplateSalesDetail::find($request->id);
        } else {
            $templateSalesDetail = new TemplateSalesDetail;
        }

        $templateSalesDetail->template_sale_id = $templateSaleId;
        $templateSalesDetail->data = $request->data;
        $templateSalesDetail->field_name = $request->field_name;

        if ($templateSalesDetail->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("template sales configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("template sales configuration")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroyConf(Request $request, $templateSaleId)
    {
        $templateSalesDetail = TemplateSalesDetail::find($request->id);
        if ($templateSalesDetail->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("template sales configuration")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("template sales configuration")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
