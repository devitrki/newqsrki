<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbProdPlan;
use App\Models\Logbook\LbProductProdPlan;
use App\Models\Logbook\LbProdTime;
use App\Models\Logbook\LbProdTimeDetail;
use App\Models\Logbook\LbProdTemp;
use App\Models\Logbook\LbProdTempVerify;
use App\Models\Logbook\LbProdQuality;
use App\Models\Logbook\LbProdUsedOil;

class ProductionPlanningController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);
        $first_product = LbProductProdPlan::getFirstProduct($userAuth->company_id_selected);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'first_product' => $first_product,
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.production-planning', $dataview)->render();
    }

    public function dataview(Request $request){
        $userAuth = $request->get('userAuth');
        $plantId = $request->query('plant-id');
        $date = $request->query('date');
        $product = $request->query('product');

        // check to app review
        $qAppReview = LbAppReview::where('date', $date)
                                ->where('company_id', $userAuth->company_id_selected)
                                ->where('plant_id', $plantId);
        $created = false;
        $lbProdPlan = [];
        $lbProdTime = [];

        if($qAppReview->count() > 0){
            $appReview = $qAppReview->first();

            // check already created or not
            $qLbProdPlan = LbProdPlan::where('lb_app_review_id', $appReview->id)->where('product', $product);

            if($qLbProdPlan->count() > 0){
                $lbProdPlan = $qLbProdPlan->first();

                $lbProdTime = DB::table('lb_prod_times')
                                ->where('lb_prod_plan_id', $lbProdPlan->id)
                                ->where('time', '6:00')
                                ->first();

                $created = true;
            }

        }

        if( $created ){

            $times = ['6:00', '7:00', '8:00', '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00', '0:00', '1:00', '2:00', '3:00', '4:00', '5:00'];

            $dataview = [
                'lbProdPlan' => $lbProdPlan,
                'lbProdTime' => $lbProdTime,
                'appReview' => $appReview,
                'date' => $request->query('date'),
                'product' => $request->query('product'),
                'times' => $times
            ];

            return view('logbook.production-planning-dataview', $dataview)->render();
        } else {
            return view('logbook.not-found')->render();
        }
    }

    public function prodPlanUpdate(Request $request)
    {
        $lbProdPlan = LbProdPlan::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbProdPlan->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbProdPlan[$request->field] = ( is_null($request->data) ) ? '' : $request->data;
            if( $lbProdPlan->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("production planning")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("production planning")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function prodTimedtble(Request $request)
    {
        $prodPlanTime = DB::table('lb_prod_times')
                            ->select('id')
                            ->where('lb_prod_plan_id', $request->query('prod-plan-id'))
                            ->where('time', $request->query('time'))
                            ->first();


        $query = DB::table('lb_prod_time_details')
                    ->select('id', 'quantity', 'exp_prod_code', 'fryer', 'temperature', 'holding_time', 'self_life',
                             'vendor')
                    ->where('lb_prod_time_id', $prodPlanTime->id);

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('quantity_input', function ($data) {
                            $quantity = ($data->quantity) ? $data->quantity : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodtimequantity' . $data->id . '" value="' . $quantity . '" onchange="changelbprodtimedetail(' . $data->id . ', \'quantity\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('exp_prod_code_input', function ($data) {
                            $exp_prod_code = ($data->exp_prod_code) ? $data->exp_prod_code : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodtimeexp_prod_code' . $data->id . '" value="' . $exp_prod_code . '" onchange="changelbprodtimedetail(' . $data->id . ', \'exp_prod_code\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('fryer_input', function ($data) {
                            $slct = '<select id="lbprodtimefryer' . $data->id . '" class="form-control form-control-sm mul" onchange="changelbprodtimedetail(' . $data->id . ', \'fryer\')" style="min-width: 6rem;">';

                            if( $data->fryer == '' ){
                                $slct .= '<option value="" selected></option>';
                            }

                            $fryers = ['A', 'B', 'C', 'D'];
                            foreach ($fryers as $fryer) {
                                if( $fryer == $data->fryer ){
                                    $slct .= '<option value="' . $fryer . '" selected>' . $fryer . '</option>';
                                } else {
                                    $slct .= '<option value="' . $fryer . '">' . $fryer . '</option>';
                                }
                            }

                            $slct .= '</select>';
                            return $slct;
                        })
                        ->addColumn('temperature_input', function ($data) {
                            $temperature = ($data->temperature) ? $data->temperature : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodtimetemperature' . $data->id . '" value="' . $temperature . '" onchange="changelbprodtimedetail(' . $data->id . ', \'temperature\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('holding_time_input', function ($data) {
                            $holding_time = ($data->holding_time) ? $data->holding_time : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodtimeholding_time' . $data->id . '" value="' . $holding_time . '" onchange="changelbprodtimedetail(' . $data->id . ', \'holding_time\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('self_life_input', function ($data) {
                            $self_life = ($data->self_life) ? $data->self_life : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodtimeself_life' . $data->id . '" value="' . $self_life . '" onchange="changelbprodtimedetail(' . $data->id . ', \'self_life\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('vendor_input', function ($data) {
                            $vendor = ($data->vendor) ? $data->vendor : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodtimevendor' . $data->id . '" value="' . $vendor . '" onchange="changelbprodtimedetail(' . $data->id . ', \'vendor\')" style="min-width: 6rem;">';
                        })
                        ->rawColumns(['quantity_input', 'exp_prod_code_input', 'fryer_input', 'temperature_input', 'holding_time_input',
                                        'self_life_input', 'vendor_input'])
                        ->make();
    }

    public function prodTempdtble(Request $request)
    {
        $query = DB::table('lb_prod_temps')
                    ->select('id', 'food_name', 'time', 'fryer_temp', 'product_temp', 'result', 'corrective_action', 'pic')
                    ->where('lb_prod_plan_id', $request->query('prod-plan-id'));

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->make();
    }

    public function prodTempVerifydtble(Request $request)
    {
        $query = DB::table('lb_prod_temp_verifies')
                    ->select('id', 'fryer', 'shift1_temp', 'shift2_temp', 'shift3_temp', 'note')
                    ->where('lb_prod_plan_id', $request->query('prod-plan-id'));

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('shift1_temp_input', function ($data) {
                            $shift1_temp = ($data->shift1_temp) ? $data->shift1_temp : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodtempverifyshift1_temp' . $data->id . '" value="' . $shift1_temp . '" onchange="changelbprodtempverify(' . $data->id . ', \'shift1_temp\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('shift2_temp_input', function ($data) {
                            $shift2_temp = ($data->shift2_temp) ? $data->shift2_temp : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodtempverifyshift2_temp' . $data->id . '" value="' . $shift2_temp . '" onchange="changelbprodtempverify(' . $data->id . ', \'shift2_temp\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('shift3_temp_input', function ($data) {
                            $shift3_temp = ($data->shift3_temp) ? $data->shift3_temp : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodtempverifyshift3_temp' . $data->id . '" value="' . $shift3_temp . '" onchange="changelbprodtempverify(' . $data->id . ', \'shift3_temp\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('note_input', function ($data) {
                            $note = ($data->note) ? $data->note : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodtempverifynote' . $data->id . '" value="' . $note . '" onchange="changelbprodtempverify(' . $data->id . ', \'note\')" style="min-width: 6rem;">';
                        })
                        ->rawColumns(['shift1_temp_input', 'shift2_temp_input', 'shift3_temp_input', 'note_input'])
                        ->make();
    }

    public function prodQualitydtble(Request $request)
    {
        $query = DB::table('lb_prod_qualities')
                    ->select('id', 'fryer', 'tpm', 'temp', 'oil_status', 'filtration')
                    ->where('lb_prod_plan_id', $request->query('prod-plan-id'));

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('tpm_input', function ($data) {
                            $tpm = ($data->tpm) ? $data->tpm : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodqualitytpm' . $data->id . '" value="' . $tpm . '" onchange="changelbprodquality(' . $data->id . ', \'tpm\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('temp_input', function ($data) {
                            $temp = ($data->temp) ? $data->temp : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodqualitytemp' . $data->id . '" value="' . $temp . '" onchange="changelbprodquality(' . $data->id . ', \'temp\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('oil_status_input', function ($data) {
                            $oil_status = ($data->oil_status) ? $data->oil_status : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodqualityoil_status' . $data->id . '" value="' . $oil_status . '" onchange="changelbprodquality(' . $data->id . ', \'oil_status\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('filtration_input', function ($data) {
                            $slct = '<select id="lbprodqualityfiltration' . $data->id . '" class="form-control form-control-sm mul" onchange="changelbprodquality(' . $data->id . ', \'filtration\')" style="min-width: 6rem;">';

                            if( $data->filtration == '' ){
                                $slct .= '<option value="" selected></option>';
                            }

                            $options = ['Yes', 'No'];
                            foreach ($options as $option) {
                                if( $option == $data->filtration ){
                                    $slct .= '<option value="' . $option . '" selected>' . $option . '</option>';
                                } else {
                                    $slct .= '<option value="' . $option . '">' . $option . '</option>';
                                }
                            }

                            $slct .= '</select>';
                            return $slct;
                        })
                        ->rawColumns(['tpm_input', 'temp_input', 'oil_status_input', 'filtration_input'])
                        ->make();
    }

    public function prodUsedoildtble(Request $request)
    {
        $query = DB::table('lb_prod_used_oil')
                    ->select('id', 'stock_first', 'stock_in_gr', 'stock_in_fryer_a', 'stock_in_fryer_b', 'stock_in_fryer_c', 'stock_in_fryer_d',
                        'stock_change_oil', 'stock_out', 'stock_last', 'note')
                    ->where('lb_prod_plan_id', $request->query('prod-plan-id'));

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('stock_first_input', function ($data) {
                            $stock_first = ($data->stock_first) ? $data->stock_first : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodusedoilstock_first' . $data->id . '" value="' . $stock_first . '" onchange="changelbprodusedoil(' . $data->id . ', \'stock_first\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_gr_input', function ($data) {
                            $stock_in_gr = ($data->stock_in_gr) ? $data->stock_in_gr : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodusedoilstock_in_gr' . $data->id . '" value="' . $stock_in_gr . '" onchange="changelbprodusedoil(' . $data->id . ', \'stock_in_gr\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_fryer_a_input', function ($data) {
                            $stock_in_fryer_a = ($data->stock_in_fryer_a) ? $data->stock_in_fryer_a : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodusedoilstock_in_fryer_a' . $data->id . '" value="' . $stock_in_fryer_a . '" onchange="changelbprodusedoil(' . $data->id . ', \'stock_in_fryer_a\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_fryer_b_input', function ($data) {
                            $stock_in_fryer_b = ($data->stock_in_fryer_b) ? $data->stock_in_fryer_b : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodusedoilstock_in_fryer_b' . $data->id . '" value="' . $stock_in_fryer_b . '" onchange="changelbprodusedoil(' . $data->id . ', \'stock_in_fryer_b\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_fryer_c_input', function ($data) {
                            $stock_in_fryer_c = ($data->stock_in_fryer_c) ? $data->stock_in_fryer_c : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodusedoilstock_in_fryer_c' . $data->id . '" value="' . $stock_in_fryer_c . '" onchange="changelbprodusedoil(' . $data->id . ', \'stock_in_fryer_c\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_in_fryer_d_input', function ($data) {
                            $stock_in_fryer_d = ($data->stock_in_fryer_d) ? $data->stock_in_fryer_d : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodusedoilstock_in_fryer_d' . $data->id . '" value="' . $stock_in_fryer_d . '" onchange="changelbprodusedoil(' . $data->id . ', \'stock_in_fryer_d\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_change_oil_input', function ($data) {
                            $stock_change_oil = ($data->stock_change_oil) ? $data->stock_change_oil : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodusedoilstock_change_oil' . $data->id . '" value="' . $stock_change_oil . '" onchange="changelbprodusedoil(' . $data->id . ', \'stock_change_oil\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_out_input', function ($data) {
                            $stock_out = ($data->stock_out) ? $data->stock_out : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodusedoilstock_out' . $data->id . '" value="' . $stock_out . '" onchange="changelbprodusedoil(' . $data->id . ', \'stock_out\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('stock_last_input', function ($data) {
                            $stock_last = ($data->stock_last) ? $data->stock_last : '';
                            return '<input type="number" class="form-control form-control-sm mul" id="lbprodusedoilstock_last' . $data->id . '" value="' . $stock_last . '" onchange="changelbprodusedoil(' . $data->id . ', \'stock_last\')" style="min-width: 6rem;">';
                        })
                        ->addColumn('note_input', function ($data) {
                            $note = ($data->note) ? $data->note : '';
                            return '<input type="text" class="form-control form-control-sm mul" id="lbprodusedoilnote' . $data->id . '" value="' . $note . '" onchange="changelbprodusedoil(' . $data->id . ', \'note\')" style="min-width: 6rem;">';
                        })
                        ->rawColumns(['stock_first_input', 'stock_in_gr_input', 'stock_in_fryer_a_input', 'stock_in_fryer_b_input',
                            'stock_in_fryer_c_input', 'stock_in_fryer_d_input', 'stock_change_oil_input', 'stock_out_input',
                            'stock_last_input', 'note_input'])
                        ->make();
    }

    public function prodTimeUpdate(Request $request)
    {
        $lbProdTime = LbProdTime::find($request->id);
        $lbProdPlan = LbProdPlan::find($lbProdTime->lb_prod_plan_id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbProdPlan->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbProdTime[$request->field] = ( is_null($request->data) ) ? '' : $request->data;
            if( $lbProdTime->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("production planning")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("production planning")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function prodTimeDetailUpdate(Request $request)
    {
        $lbProdTimeDetail = LbProdTimeDetail::find($request->id);
        $lbProdTime = LbProdTime::find($lbProdTimeDetail->lb_prod_time_id);
        $lbProdPlan = LbProdPlan::find($lbProdTime->lb_prod_plan_id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbProdPlan->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbProdTimeDetail[$request->field] = ( is_null($request->data) ) ? '' : $request->data;
            if( $lbProdTimeDetail->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("production planning")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("production planning")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function prodTimedetail(Request $request)
    {
        $prodPlanTime = DB::table('lb_prod_times')
                            ->select('act_cooking', 'act_cooking_total', 'plan_cooking', 'plan_cooking_total')
                            ->where('lb_prod_plan_id', $request->query('prod-plan-id'))
                            ->where('time', $request->query('time'))
                            ->first();

        return response()->json( Helper::resJSON( 'success', '', $prodPlanTime ) );
    }

    public function prodTempStore(Request $request)
    {
        $request->validate([
                        'food_name' => 'required',
                        'time' => 'required',
                        'fryer_temperature' => 'required',
                        'product_temperature' => 'required',
                        'product_status' => 'required',
                        'corrective_action' => 'required',
                        'pic' => 'required',
                    ]);

        $lbProdTemp = new LbProdTemp;
        $lbProdTemp->lb_prod_plan_id = $request->lb_prod_plan_id;
        $lbProdTemp->food_name = $request->food_name;
        $lbProdTemp->time = $request->time;
        $lbProdTemp->fryer_temp = $request->fryer_temperature;
        $lbProdTemp->product_temp = $request->product_temperature;
        $lbProdTemp->result = $request->product_status;
        $lbProdTemp->corrective_action = $request->corrective_action;
        $lbProdTemp->pic = $request->pic;
        if ($lbProdTemp->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("chicken internal temperature")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("chicken internal temperature")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function prodTempUpdate(Request $request, $id)
    {
        $request->validate([
                        'food_name' => 'required',
                        'time' => 'required',
                        'fryer_temperature' => 'required',
                        'product_temperature' => 'required',
                        'product_status' => 'required',
                        'corrective_action' => 'required',
                        'pic' => 'required',
                    ]);

        $lbProdTemp = LbProdTemp::find($request->id);
        $lbProdTemp->food_name = $request->food_name;
        $lbProdTemp->time = $request->time;
        $lbProdTemp->fryer_temp = $request->fryer_temperature;
        $lbProdTemp->product_temp = $request->product_temperature;
        $lbProdTemp->result = $request->product_status;
        $lbProdTemp->corrective_action = $request->corrective_action;
        $lbProdTemp->pic = $request->pic;
        if ($lbProdTemp->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("chicken internal temperature")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("chicken internal temperature")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function prodTempDestroy($id)
    {
        $lbProdTemp = LbProdTemp::find($id);
        if ($lbProdTemp->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("chicken internal temperature")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("chicken internal temperature")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function prodTempVerifyUpdate(Request $request)
    {
        $lbProdTempVerify = LbProdTempVerify::find($request->id);
        $lbProdPlan = LbProdPlan::find($lbProdTempVerify->lb_prod_plan_id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbProdPlan->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbProdTempVerify[$request->field] = ( is_null($request->data) ) ? '' : $request->data;
            if( $lbProdTempVerify->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("production planning")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("production planning")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function prodQualityUpdate(Request $request)
    {
        $lbProdQuality = LbProdQuality::find($request->id);
        $lbProdPlan = LbProdPlan::find($lbProdQuality->lb_prod_plan_id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbProdPlan->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbProdQuality[$request->field] = ( is_null($request->data) ) ? '' : $request->data;
            if( $lbProdQuality->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("production planning")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("production planning")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function prodUsedoilUpdate(Request $request)
    {
        $lbProdUsedOil = LbProdUsedOil::find($request->id);
        $lbProdPlan = LbProdPlan::find($lbProdUsedOil->lb_prod_plan_id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbProdPlan->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbProdUsedOil[$request->field] = ( is_null($request->data) ) ? '' : $request->data;
            if( $lbProdUsedOil->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("production planning")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("production planning")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function preview($lbAppReviewId, Request $request)
    {
        $appReview = LbAppReview::getFullDataById($lbAppReviewId);

        $header = [
            'outlet' => $appReview->outlet,
            'date' => date("d-m-Y", strtotime($appReview->date)),
            'product' => $request->query('product')
        ];

        $qLbProdPlan = DB::table('lb_prod_plans')
                        ->where('lb_app_review_id', $lbAppReviewId)
                        ->where('product', $request->query('product'));

        $times = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 0, 1, 2, 3, 4, 5];
        $lfryers = ['A', 'B', 'C', 'D'];

        $lbProdPlan = [];
        $lbProdTimes = [];
        $lbProdTemps = [];
        $lbProdTempVerifys = [];
        $lbProdQualities = [];
        $lbProdUsedOil = [];

        if( $qLbProdPlan->count() > 0 ){

            $lbProdPlan = $qLbProdPlan->first();

            // get data for prod time
            foreach ($times as $t) {
                $time = $t . ':00';

                $lbProdTime = DB::table('lb_prod_times')
                                ->where('lb_prod_plan_id', $lbProdPlan->id)
                                ->where('time', $time)
                                ->select('id', 'plan_cooking', 'plan_cooking_total', 'act_cooking', 'act_cooking_total')
                                ->first();

                $lbProdTimes['time'][] = $time;
                $lbProdTimes['planning'][] = $lbProdTime->plan_cooking . ' / ' . $lbProdTime->plan_cooking_total;
                $lbProdTimes['actual'][] = $lbProdTime->act_cooking . ' / ' . $lbProdTime->act_cooking_total;

                $lbProdTimeDetails = DB::table('lb_prod_time_details')
                                        ->where('lb_prod_time_id', $lbProdTime->id)
                                        ->select('quantity', 'exp_prod_code', 'fryer', 'temperature', 'holding_time',
                                            'self_life', 'vendor')
                                        ->get();

                $quantities = [];
                $expProdCodes = [];
                $fryers = [];
                $temperatures = [];
                $holdingTimes = [];
                $selfLifes = [];
                $vendors = [];

                foreach ($lbProdTimeDetails as $lbProdTimeDetail) {
                    $quantities[] = $lbProdTimeDetail->quantity;
                    $expProdCodes[] = $lbProdTimeDetail->exp_prod_code;
                    $fryers[] = $lbProdTimeDetail->fryer;
                    $temperatures[] = $lbProdTimeDetail->temperature;
                    $holdingTimes[] = $lbProdTimeDetail->holding_time;
                    $selfLifes[] = $lbProdTimeDetail->self_life;
                    $vendors[] = $lbProdTimeDetail->vendor;
                }

                $lbProdTimes['quantity'][] = $quantities;
                $lbProdTimes['exp_prod_code'][] = $expProdCodes;
                $lbProdTimes['fryer'][] = $fryers;
                $lbProdTimes['temperature'][] = $temperatures;
                $lbProdTimes['holding_time'][] = $holdingTimes;
                $lbProdTimes['self_life'][] = $selfLifes;
                $lbProdTimes['vendor'][] = $vendors;
            }

            // get prod temp
            $lbProdTemps = DB::table('lb_prod_temps')
                            ->where('lb_prod_plan_id', $lbProdPlan->id)
                            ->select('food_name', 'time', 'fryer_temp', 'product_temp', 'result', 'corrective_action', 'pic')
                            ->get();

            // get prod temp verify
            $lbProdTempVerifys = DB::table('lb_prod_temp_verifies')
                            ->where('lb_prod_plan_id', $lbProdPlan->id)
                            ->select('fryer', 'shift1_temp', 'shift2_temp', 'shift3_temp', 'note')
                            ->get();

            // get prod quality
            $lbProdQualities = [];
            foreach ($lfryers as $lfryer) {
                $lbProdQuality = DB::table('lb_prod_qualities')
                                    ->where('lb_prod_plan_id', $lbProdPlan->id)
                                    ->where('fryer', $lfryer)
                                    ->select('tpm', 'temp', 'oil_status', 'filtration')
                                    ->first();

                $lbProdQualities[$lfryer] = $lbProdQuality;
            }

            // get prod used oil
            $lbProdUsedOil = DB::table('lb_prod_used_oil')
                                ->where('lb_prod_plan_id', $lbProdPlan->id)
                                ->select('stock_first', 'stock_in_gr', 'stock_in_fryer_a', 'stock_in_fryer_b', 'stock_in_fryer_c',
                                    'stock_in_fryer_d', 'stock_change_oil', 'stock_out', 'stock_last')
                                ->first();

        }

        $dataview = [
            'title' => 'PRODUCTION PLANNING',
            'lbProdPlan' => $lbProdPlan,
            'lbProdTimes' => $lbProdTimes,
            'lbProdTemps' => $lbProdTemps,
            'lbProdTempVerifys' => $lbProdTempVerifys,
            'lfryers' => $lfryers,
            'lbProdQualities' => $lbProdQualities,
            'lbProdUsedOil' => $lbProdUsedOil,
            'header' => $header
        ];

        return view('logbook.preview.production-planning-preview', $dataview)->render();
    }
}
