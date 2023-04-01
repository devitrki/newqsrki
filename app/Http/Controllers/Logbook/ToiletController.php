<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Logbook\LbToilet;
use App\Models\Logbook\LbAppReview;
use App\Models\User;
use App\Models\Configuration;

class ToiletController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $range_shift_1 = Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'logbook', 'range_shift_1');
        $range_shift_2 = Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'logbook', 'range_shift_2');
        $range_shift_3 = Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'logbook', 'range_shift_3');

        $range_shift_1 = explode(',', $range_shift_1);
        $shift1 = [];
        for ($i = trim($range_shift_1[0]); $i <= trim($range_shift_1[1]) ; $i++) {
            $shift1[] = ($i == 24) ? '0:00' : $i . ':00';
        }

        $range_shift_2 = explode(',', $range_shift_2);
        $shift2 = [];
        for ($i = trim($range_shift_2[0]); $i <= trim($range_shift_2[1]) ; $i++) {
            $shift2[] = ($i == 24) ? '0:00' : $i . ':00';
        }

        $range_shift_3 = explode(',', $range_shift_3);
        $shift3 = [];
        for ($i = trim($range_shift_3[0]); $i <= trim($range_shift_3[1]) ; $i++) {
            $shift3[] = ($i == 24) ? '0:00' : $i . ':00';
        }

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'shift1' => $shift1,
            'shift2' => $shift2,
            'shift3' => $shift3,
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.toilet-checklist', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_toilets')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_toilets.lb_app_review_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->select(['lb_toilets.id', 'lb_toilets.task', 'lb_toilets.shift', 'lb_toilets.checklis_1',
                            'lb_toilets.checklis_2', 'lb_toilets.checklis_3', 'lb_toilets.checklis_4', 'lb_toilets.checklis_5',
                            'lb_toilets.checklis_6', 'lb_toilets.checklis_7', 'lb_toilets.checklis_8',
                            'lb_toilets.updated_at', 'lb_app_reviews.date'])
                    ->where('lb_toilets.shift', $request->query('shift'));

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
                        ->addColumn('shift_desc', function ($data) {
                            if( $data->shift == '1' ){
                                return 'Opening';
                            } else if( $data->shift == '2' ){
                                return 'Closing';
                            } else {
                                return 'Midnite';
                            }
                        })
                        ->addColumn('checklis_1_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->checklis_1  != 0) {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_1' . $data->id . '" checked onclick="changeLbToilet(' . $data->id . ', \'checklis_1\')">';
                            } else {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_1' . $data->id . '" onclick="changeLbToilet(' . $data->id . ', \'checklis_1\')">';
                            }
                            $check .= '<label for="lbtoiletchecklis_1' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('checklis_2_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->checklis_2  != 0) {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_2' . $data->id . '" checked onclick="changeLbToilet(' . $data->id . ', \'checklis_2\')">';
                            } else {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_2' . $data->id . '" onclick="changeLbToilet(' . $data->id . ', \'checklis_2\')">';
                            }
                            $check .= '<label for="lbtoiletchecklis_2' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('checklis_3_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->checklis_3  != 0) {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_3' . $data->id . '" checked onclick="changeLbToilet(' . $data->id . ', \'checklis_3\')">';
                            } else {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_3' . $data->id . '" onclick="changeLbToilet(' . $data->id . ', \'checklis_3\')">';
                            }
                            $check .= '<label for="lbtoiletchecklis_3' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('checklis_4_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->checklis_4  != 0) {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_4' . $data->id . '" checked onclick="changeLbToilet(' . $data->id . ', \'checklis_4\')">';
                            } else {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_4' . $data->id . '" onclick="changeLbToilet(' . $data->id . ', \'checklis_4\')">';
                            }
                            $check .= '<label for="lbtoiletchecklis_4' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('checklis_5_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->checklis_5  != 0) {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_5' . $data->id . '" checked onclick="changeLbToilet(' . $data->id . ', \'checklis_5\')">';
                            } else {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_5' . $data->id . '" onclick="changeLbToilet(' . $data->id . ', \'checklis_5\')">';
                            }
                            $check .= '<label for="lbtoiletchecklis_5' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('checklis_6_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->checklis_6  != 0) {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_6' . $data->id . '" checked onclick="changeLbToilet(' . $data->id . ', \'checklis_6\')">';
                            } else {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_6' . $data->id . '" onclick="changeLbToilet(' . $data->id . ', \'checklis_6\')">';
                            }
                            $check .= '<label for="lbtoiletchecklis_6' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('checklis_7_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->checklis_7  != 0) {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_7' . $data->id . '" checked onclick="changeLbToilet(' . $data->id . ', \'checklis_7\')">';
                            } else {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_7' . $data->id . '" onclick="changeLbToilet(' . $data->id . ', \'checklis_7\')">';
                            }
                            $check .= '<label for="lbtoiletchecklis_7' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('checklis_8_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->checklis_8  != 0) {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_8' . $data->id . '" checked onclick="changeLbToilet(' . $data->id . ', \'checklis_8\')">';
                            } else {
                                $check .= '<input type="checkbox" id="lbtoiletchecklis_8' . $data->id . '" onclick="changeLbToilet(' . $data->id . ', \'checklis_8\')">';
                            }
                            $check .= '<label for="lbtoiletchecklis_8' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->rawColumns(['checklis_1_desc', 'checklis_2_desc', 'checklis_3_desc', 'checklis_4_desc', 'checklis_5_desc',
                                    'checklis_6_desc', 'checklis_7_desc', 'checklis_8_desc'])
                        ->make();
    }

    public function update(Request $request)
    {
        $lbToilet = LbToilet::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbToilet->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbToilet[$request->field] = $request->value;
            if( $lbToilet->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("toilet checklist")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("toilet checklist")]);
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
            'shift' => $request->shift
        ];

        $range_shift_1 = Configuration::getValueCompByKeyFor($appReview->company_id, 'logbook', 'range_shift_1');
        $range_shift_2 = Configuration::getValueCompByKeyFor($appReview->company_id, 'logbook', 'range_shift_2');
        $range_shift_3 = Configuration::getValueCompByKeyFor($appReview->company_id, 'logbook', 'range_shift_3');

        $shifts = [];
        if( $request->shift == 1 ){
            $range_shift_1 = explode(',', $range_shift_1);
            for ($i = trim($range_shift_1[0]); $i <= trim($range_shift_1[1]) ; $i++) {
                $shifts[] = ($i == 24) ? '0:00' : $i . ':00';
            }
        } else if( $request->shift == 2 ){
            $range_shift_2 = explode(',', $range_shift_2);
            for ($i = trim($range_shift_2[0]); $i <= trim($range_shift_2[1]) ; $i++) {
                $shifts[] = ($i == 24) ? '0:00' : $i . ':00';
            }
        } else {
            $range_shift_3 = explode(',', $range_shift_3);
            for ($i = trim($range_shift_3[0]); $i <= trim($range_shift_3[1]) ; $i++) {
                $shifts[] = ($i == 24) ? '0:00' : $i . ':00';
            }
        }

        $lbToilet = DB::table('lb_toilets')
                            ->where('lb_app_review_id', $lbAppReviewId)
                            ->select('task', 'checklis_1', 'checklis_2', 'checklis_3', 'checklis_4', 'checklis_5',
                                    'checklis_6', 'checklis_7', 'checklis_8')
                            ->where('shift', $request->shift)
                            ->get();

        $dataview = [
            'title' => 'TOILET CHECKLIST',
            'data' => $lbToilet,
            'shift' => $request->shift,
            'shifts' => $shifts,
            'header' => $header
        ];


        return view('logbook.preview.toilet-preview', $dataview)->render();
    }
}
