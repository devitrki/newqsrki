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
use App\Models\Configuration;
use App\Models\Logbook\LbTemperature;

class TemperatureController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $start_time_temp = Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'logbook', 'start_time_temp');
        $interval_temp = Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'logbook', 'interval_temp');

        $range_check_temp = [];
        for ($i=1; $i <= 5; $i++) {
            $range_check_temp[] = $start_time_temp . ':00';

            $start_time_temp += $interval_temp;
        }

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'range_check_temp' => $range_check_temp,
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.temperature', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_temperatures')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_temperatures.lb_app_review_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->select(['lb_temperatures.id', 'lb_temperatures.name', 'lb_temperatures.top_value', 'lb_temperatures.bottom_value',
                            'lb_temperatures.top_value_center', 'lb_temperatures.bottom_value_center', 'lb_temperatures.interval',
                            'lb_temperatures.uom', 'lb_temperatures.temp_1', 'lb_temperatures.temp_2', 'lb_temperatures.temp_3',
                            'lb_temperatures.temp_4', 'lb_temperatures.temp_5', 'lb_temperatures.updated_at', 'lb_app_reviews.date',
                            'lb_temperatures.note']);

        if($request->has('plant-id') && $request->query('plant-id')){
            $query = $query->where('lb_app_reviews.plant_id', $request->query('plant-id'));
        } else {
            $query = $query->where('lb_app_reviews.plant_id', 0);
        }

        if($request->has('date')){
            $query = $query->where('lb_app_reviews.date', $request->query('date'));
        }

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('date_desc', function ($data) {
                            return date("d-m-Y", strtotime($data->updated_at));
                        })
                        ->addColumn('temp_1_input', function ($data) {
                            $slct = '<select id="lbtemp_1' . $data->id . '" class="form-control form-control-sm mul" onchange="changeLbTemp(' . $data->id . ', \'temp_1\')" style="min-width: 6rem;">';

                            if( $data->temp_1 == '' ){
                                $slct .= '<option value="" selected></option>';
                            }

                            for ($i = $data->top_value; $i >= $data->bottom_value; $i = $i - $data->interval) {

                                if( $i == $data->top_value ){
                                    $temp = '> ' . $i . $data->uom;
                                } else if ( $i == $data->bottom_value ) {
                                    $temp = '< ' . $i . $data->uom;
                                } else {
                                    $temp = $i . $data->uom;
                                }

                                if( $temp == $data->temp_1 ){
                                    $slct .= '<option value="' . $temp . '" selected>' . $temp . '</option>';
                                } else {
                                    $slct .= '<option value="' . $temp . '">' . $temp . '</option>';
                                }

                            }

                            $slct .= '</select>';
                            return $slct;
                        })
                        ->addColumn('temp_2_input', function ($data) {
                            $slct = '<select id="lbtemp_2' . $data->id . '" class="form-control form-control-sm mul" onchange="changeLbTemp(' . $data->id . ', \'temp_2\')" style="min-width: 6rem;">';

                            if( $data->temp_2 == '' ){
                                $slct .= '<option value="" selected></option>';
                            }

                            for ($i = $data->top_value; $i >= $data->bottom_value; $i = $i - $data->interval) {

                                if( $i == $data->top_value ){
                                    $temp = '> ' . $i . $data->uom;
                                } else if ( $i == $data->bottom_value ) {
                                    $temp = '< ' . $i . $data->uom;
                                } else {
                                    $temp = $i . $data->uom;
                                }

                                if( $temp == $data->temp_2 ){
                                    $slct .= '<option value="' . $temp . '" selected>' . $temp . '</option>';
                                } else {
                                    $slct .= '<option value="' . $temp . '">' . $temp . '</option>';
                                }

                            }

                            $slct .= '</select>';
                            return $slct;
                        })
                        ->addColumn('temp_3_input', function ($data) {
                            $slct = '<select id="lbtemp_3' . $data->id . '" class="form-control form-control-sm mul" onchange="changeLbTemp(' . $data->id . ', \'temp_3\')" style="min-width: 6rem;">';

                            if( $data->temp_3 == '' ){
                                $slct .= '<option value="" selected></option>';
                            }

                            for ($i = $data->top_value; $i >= $data->bottom_value; $i = $i - $data->interval) {

                                if( $i == $data->top_value ){
                                    $temp = '> ' . $i . $data->uom;
                                } else if ( $i == $data->bottom_value ) {
                                    $temp = '< ' . $i . $data->uom;
                                } else {
                                    $temp = $i . $data->uom;
                                }

                                if( $temp == $data->temp_3 ){
                                    $slct .= '<option value="' . $temp . '" selected>' . $temp . '</option>';
                                } else {
                                    $slct .= '<option value="' . $temp . '">' . $temp . '</option>';
                                }

                            }

                            $slct .= '</select>';
                            return $slct;
                        })
                        ->addColumn('temp_4_input', function ($data) {
                            $slct = '<select id="lbtemp_4' . $data->id . '" class="form-control form-control-sm mul" onchange="changeLbTemp(' . $data->id . ', \'temp_4\')" style="min-width: 6rem;">';

                            if( $data->temp_4 == '' ){
                                $slct .= '<option value="" selected></option>';
                            }

                            for ($i = $data->top_value; $i >= $data->bottom_value; $i = $i - $data->interval) {

                                if( $i == $data->top_value ){
                                    $temp = '> ' . $i . $data->uom;
                                } else if ( $i == $data->bottom_value ) {
                                    $temp = '< ' . $i . $data->uom;
                                } else {
                                    $temp = $i . $data->uom;
                                }

                                if( $temp == $data->temp_4 ){
                                    $slct .= '<option value="' . $temp . '" selected>' . $temp . '</option>';
                                } else {
                                    $slct .= '<option value="' . $temp . '">' . $temp . '</option>';
                                }

                            }

                            $slct .= '</select>';
                            return $slct;
                        })
                        ->addColumn('temp_5_input', function ($data) {
                            $slct = '<select id="lbtemp_5' . $data->id . '" class="form-control form-control-sm mul" onchange="changeLbTemp(' . $data->id . ', \'temp_5\')" style="min-width: 6rem;">';

                            if( $data->temp_5 == '' ){
                                $slct .= '<option value="" selected></option>';
                            }

                            for ($i = $data->top_value; $i >= $data->bottom_value; $i = $i - $data->interval) {

                                if( $i == $data->top_value ){
                                    $temp = '> ' . $i . $data->uom;
                                } else if ( $i == $data->bottom_value ) {
                                    $temp = '< ' . $i . $data->uom;
                                } else {
                                    $temp = $i . $data->uom;
                                }

                                if( $temp == $data->temp_5 ){
                                    $slct .= '<option value="' . $temp . '" selected>' . $temp . '</option>';
                                } else {
                                    $slct .= '<option value="' . $temp . '">' . $temp . '</option>';
                                }

                            }

                            $slct .= '</select>';
                            return $slct;
                        })
                        ->addColumn('note_input', function ($data) {
                            return '<input type="text" class="form-control form-control-sm mul" id="lbtempnote' . $data->id . '" value="' . $data->note . '" onchange="changeLbTemp(' . $data->id . ', \'note\')" style="min-width: 6rem;">';
                        })
                        ->rawColumns([ 'temp_1_input', 'temp_2_input', 'temp_3_input', 'temp_4_input', 'temp_5_input', 'note_input' ])
                        ->make();
    }

    public function update(Request $request)
    {
        $lbTemperature = LbTemperature::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbTemperature->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbTemperature[$request->field] = ( is_null($request->data) ) ? '' : $request->data;
            if( $lbTemperature->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("temperature")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("temperature")]);
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
        ];

        $start_time_temp = Configuration::getValueCompByKeyFor($appReview->company_id, 'logbook', 'start_time_temp');
        $interval_temp = Configuration::getValueCompByKeyFor($appReview->company_id, 'logbook', 'interval_temp');

        $range_check_temp = [];
        for ($i=1; $i <= 5; $i++) {
            $range_check_temp[] = $start_time_temp . ':00';

            $start_time_temp += $interval_temp;
        }

        $data = DB::table('lb_temperatures')
                            ->where('lb_app_review_id', $lbAppReviewId)
                            ->select('name', 'temp_1', 'temp_2', 'temp_3', 'temp_4', 'temp_5', 'note')
                            ->get();

        $dataview = [
            'title' => 'TEMPERATURE FORM',
            'data' => $data,
            'range_check_temp' => $range_check_temp,
            'header' => $header
        ];


        return view('logbook.preview.temperature-preview', $dataview)->render();
    }
}
