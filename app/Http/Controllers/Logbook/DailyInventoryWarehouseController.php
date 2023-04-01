<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Logbook\LbDlyInvWarehouse;
use App\Models\Logbook\LbAppReview;
use App\Models\User;

class DailyInventoryWarehouseController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.daily-inventory-warehouse', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_dly_inv_warehouses')
                    ->leftJoin('lb_app_reviews', 'lb_app_reviews.id', 'lb_dly_inv_warehouses.lb_app_review_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->select(['lb_dly_inv_warehouses.id', 'lb_dly_inv_warehouses.product_name', 'lb_dly_inv_warehouses.uom',
                            'lb_dly_inv_warehouses.frekuensi', 'lb_dly_inv_warehouses.stock_opening', 'lb_dly_inv_warehouses.stock_in_gr_plant',
                            'lb_dly_inv_warehouses.stock_in_dc', 'lb_dly_inv_warehouses.stock_in_vendor', 'lb_dly_inv_warehouses.stock_in_section',
                            'lb_dly_inv_warehouses.stock_out_gi_plant', 'lb_dly_inv_warehouses.stock_out_dc', 'lb_dly_inv_warehouses.stock_out_vendor',
                            'lb_dly_inv_warehouses.stock_out_section', 'lb_dly_inv_warehouses.stock_closing', 'lb_dly_inv_warehouses.last_update',
                            'lb_dly_inv_warehouses.updated_at', 'lb_app_reviews.date', 'lb_dly_inv_warehouses.note']);

        if($request->has('plant-id') && $request->query('plant-id')){
            $query = $query->where('lb_app_reviews.plant_id', $request->query('plant-id'));
        } else {
            $query = $query->where('lb_app_reviews.plant_id', 0);
        }

        if($request->has('date')){
            $query = $query->where('lb_app_reviews.date', $request->query('date'));
        }

        $query = $query->orderBy('lb_dly_inv_warehouses.product_name');

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('date_desc', function ($data) {
                            return date("d-m-Y", strtotime($data->updated_at));
                        })
                        ->addColumn('updated_at_desc', function ($data) {
                            return date("d-m-Y H:i:s", strtotime($data->updated_at));
                        })
                        ->addColumn('stock_opening_input', function ($data) {
                            $stockOpening = ($data->stock_opening) ? $data->stock_opening : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_opening' . $data->id . '" value="' . $stockOpening . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_opening\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_gr_plant_input', function ($data) {
                            $stockInGrPlant = ($data->stock_in_gr_plant) ? $data->stock_in_gr_plant : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_in_gr_plant' . $data->id . '" value="' . $stockInGrPlant . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_in_gr_plant\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_dc_input', function ($data) {
                            $stockInDc = ($data->stock_in_dc) ? $data->stock_in_dc : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_in_dc' . $data->id . '" value="' . $stockInDc . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_in_dc\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_vendor_input', function ($data) {
                            $stockInVendor = ($data->stock_in_vendor) ? $data->stock_in_vendor : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_in_vendor' . $data->id . '" value="' . $stockInVendor . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_in_vendor\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_section_input', function ($data) {
                            $stockInSection = ($data->stock_in_section) ? $data->stock_in_section : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_in_section' . $data->id . '" value="' . $stockInSection . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_in_section\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_out_gi_plant_input', function ($data) {
                            $stockOutGiPlant = ($data->stock_out_gi_plant) ? $data->stock_out_gi_plant : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_out_gi_plant' . $data->id . '" value="' . $stockOutGiPlant . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_out_gi_plant\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_out_dc_input', function ($data) {
                            $stockOutDc = ($data->stock_out_dc) ? $data->stock_out_dc : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_out_dc' . $data->id . '" value="' . $stockOutDc . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_out_dc\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_out_vendor_input', function ($data) {
                            $stockOutVendor = ($data->stock_out_vendor) ? $data->stock_out_vendor : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_out_vendor' . $data->id . '" value="' . $stockOutVendor . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_out_vendor\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_out_section_input', function ($data) {
                            $stockOutSection = ($data->stock_out_section) ? $data->stock_out_section : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_out_section' . $data->id . '" value="' . $stockOutSection . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_out_section\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_closing_input', function ($data) {
                            $stockClosing = ($data->stock_closing) ? $data->stock_closing : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvwhstock_closing' . $data->id . '" value="' . $stockClosing . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'stock_closing\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('note_input', function ($data) {
                            $note = ($data->note) ? $data->note : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbdlyinvwhnote' . $data->id . '" value="' . $note . '" onchange="changeLbDlyInvWarehouse(' . $data->id . ', \'note\')" style="min-width: 6rem;">';
                        })
                        ->rawColumns(['stock_opening_input', 'stock_in_gr_plant_input', 'stock_in_dc_input', 'stock_in_vendor_input',
                            'stock_in_section_input', 'stock_in_section_input', 'stock_out_gi_plant_input', 'stock_out_dc_input',
                            'stock_out_vendor_input', 'stock_out_section_input', 'stock_closing_input', 'note_input'])
                        ->make();
    }

    public function update(Request $request)
    {
        $lbDlyInvWarehouse = LbDlyInvWarehouse::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbDlyInvWarehouse->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbDlyInvWarehouse[$request->field] = (is_numeric($request->value)) ?
                                                    round( Helper::replaceDelimiterNumber($request->value), 2 ) :
                                                    $request->value;

            // calculation stock closing
            // $stockIn = $lbDlyInvWarehouse->stock_in_gr_plant + $lbDlyInvWarehouse->stock_in_dc + $lbDlyInvWarehouse->stock_in_vendor + $lbDlyInvWarehouse->stock_in_section;
            // $stockOut = $lbDlyInvWarehouse->stock_out_gi_plant + $lbDlyInvWarehouse->stock_out_dc + $lbDlyInvWarehouse->stock_out_vendor + $lbDlyInvWarehouse->stock_out_section;
            // $lbDlyInvWarehouse->stock_closing = $lbDlyInvWarehouse->stock_opening + $stockIn - $stockOut;

            if( $lbDlyInvWarehouse->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("daily inventory warehouse")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("daily inventory warehouse")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function preview($lbAppReviewId)
    {
        $appReview = LbAppReview::getFullDataById($lbAppReviewId);

        $header = [
            'outlet' => $appReview->outlet,
            'date' => date("d-m-Y", strtotime($appReview->date)),
            'mod' => ($appReview->mod_pic) ? $appReview->mod_pic : '-'
        ];

        $lbDlyInvWarehouse = DB::table('lb_dly_inv_warehouses')
                            ->where('lb_app_review_id', $lbAppReviewId)
                            ->select('product_name', 'uom', 'frekuensi', 'stock_opening', 'stock_closing',
                            'stock_in_gr_plant', 'stock_in_dc', 'stock_in_vendor', 'stock_in_section', 'stock_out_gi_plant',
                            'stock_out_dc', 'stock_out_vendor', 'stock_out_section', 'note')
                            ->get();

        $dataview = [
            'title' => 'FORM DAILY INVENTORY (WAREHOUSE)',
            'data' => $lbDlyInvWarehouse,
            'header' => $header
        ];


        return view('logbook.preview.daily-inventory-warehouse-preview', $dataview)->render();
    }
}
