<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Logbook\LbDlyInvCashier;
use App\Models\Logbook\LbAppReview;
use App\Models\User;

class DailyInventoryCashierController extends Controller
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
        return view('logbook.daily-inventory-cashier', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_dly_inv_cashiers')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_dly_inv_cashiers.lb_app_review_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->select(['lb_dly_inv_cashiers.id', 'lb_dly_inv_cashiers.product_name', 'lb_dly_inv_cashiers.uom',
                            'lb_dly_inv_cashiers.frekuensi', 'lb_dly_inv_cashiers.stock_opening', 'lb_dly_inv_cashiers.stock_in',
                            'lb_dly_inv_cashiers.stock_out', 'lb_dly_inv_cashiers.stock_closing', 'lb_dly_inv_cashiers.last_update',
                            'lb_dly_inv_cashiers.updated_at', 'lb_app_reviews.date', 'lb_dly_inv_cashiers.note']);

        if($request->has('plant-id') && $request->query('plant-id')){
            $query = $query->where('lb_app_reviews.plant_id', $request->query('plant-id'));
        } else {
            $query = $query->where('lb_app_reviews.plant_id', 0);
        }

        if($request->has('date')){
            $query = $query->where('lb_app_reviews.date', $request->query('date'));
        }

        $query = $query->orderBy('lb_dly_inv_cashiers.product_name');

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
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvcshirstock_opening' . $data->id . '" value="' . $stockOpening . '" onchange="changeLbDlyInvCashier(' . $data->id . ', \'stock_opening\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_input', function ($data) {
                            $stockIn = ($data->stock_in) ? $data->stock_in : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvcshirstock_in' . $data->id . '" value="' . $stockIn . '" onchange="changeLbDlyInvCashier(' . $data->id . ', \'stock_in\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_out_input', function ($data) {
                            $stockOut = ($data->stock_out) ? $data->stock_out : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvcshirstock_out' . $data->id . '" value="' . $stockOut . '" onchange="changeLbDlyInvCashier(' . $data->id . ', \'stock_out\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_closing_input', function ($data) {
                            $stockClosing = ($data->stock_closing) ? $data->stock_closing : 0;
                            return '<input type="number" class="form-control form-control-sm mul" id="lbdlyinvcshirstock_closing' . $data->id . '" value="' . $stockClosing . '" onchange="changeLbDlyInvCashier(' . $data->id . ', \'stock_closing\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('note_input', function ($data) {
                            $note = ($data->note) ? $data->note : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbdlyinvcshirnote' . $data->id . '" value="' . $note . '" onchange="changeLbDlyInvCashier(' . $data->id . ', \'note\')" style="min-width: 6rem;">';
                        })
                        ->rawColumns(['stock_opening_input', 'stock_in_input', 'stock_out_input', 'stock_closing_input', 'note_input'])
                        ->make();
    }

    public function update(Request $request)
    {
        $lbDlyInvCashier = LbDlyInvCashier::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbDlyInvCashier->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbDlyInvCashier[$request->field] = (is_numeric($request->value)) ?
                                                    round( Helper::replaceDelimiterNumber($request->value), 2 ) :
                                                    $request->value;

            // calculation stock closing
            // $lbDlyInvCashier->stock_closing = $lbDlyInvCashier->stock_opening + $lbDlyInvCashier->stock_in - $lbDlyInvCashier->stock_out;

            if( $lbDlyInvCashier->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("daily inventory cashier")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("daily inventory cashier")]);
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
            'mod' => ($appReview->mod_pic) ? $appReview->mod_pic : '-',
        ];

        $lbDlyInvCashier = DB::table('lb_dly_inv_cashiers')
                            ->where('lb_app_review_id', $lbAppReviewId)
                            ->select('product_name', 'uom', 'frekuensi', 'stock_opening', 'stock_out', 'stock_in', 'stock_closing', 'note')
                            ->get();

        $dataview = [
            'title' => 'FORM DAILY INVENTORY (CASHIER)',
            'data' => $lbDlyInvCashier,
            'header' => $header
        ];


        return view('logbook.preview.daily-inventory-cashier-preview', $dataview)->render();
    }
}
