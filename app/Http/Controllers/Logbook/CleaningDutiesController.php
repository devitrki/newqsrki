<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Logbook\LbCleanDuties;
use App\Models\Logbook\LbCleanDutiesDly;
use App\Models\Logbook\LbCleanDutiesWly;
use App\Models\Logbook\LbAppReview;
use App\User;
class CleaningDutiesController extends Controller
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
        return view('logbook.cleaning-duties', $dataview)->render();
    }

    public function dataview(Request $request){
        $userAuth = $request->get('userAuth');
        $plantId = $request->query('plant-id');
        $date = $request->query('date');
        $section = $request->query('section');

        // check to app review
        $qAppReview = LbAppReview::where('date', $date)
                                ->where('company_id', $userAuth->company_id_selected)
                                ->where('plant_id', $plantId);
        $created = false;
        $lbCleanDuties = [];
        if($qAppReview->count() > 0){
            $appReview = $qAppReview->first();

            // check already created or not
            $qLbCleanDuties = LbCleanDuties::where('lb_app_review_id', $appReview->id)
                                            ->where('section', $section);

            if($qLbCleanDuties->count() > 0){
                $lbCleanDuties = $qLbCleanDuties->first();
                $created = true;
            }

        }

        if( $created ){
            $dataview = [
                'lbCleanDuties' => $lbCleanDuties,
                'appReview' => $appReview,
                'date' => $request->query('date'),
                'section' => $request->query('section'),
            ];
            return view('logbook.cleaning-duties-dataview', $dataview)->render();
        } else {
            return view('logbook.not-found')->render();
        }
    }

    public function dailyDtble(Request $request)
    {
        $query = DB::table('lb_clean_duties_dlies')
                    ->join('lb_clean_duties', 'lb_clean_duties.id', 'lb_clean_duties_dlies.lb_clean_duties_id')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_clean_duties.lb_app_review_id')
                    ->select(['lb_clean_duties.id as duties_id', 'lb_clean_duties.note', 'lb_clean_duties.section',
                            'lb_clean_duties_dlies.opening', 'lb_clean_duties_dlies.closing', 'lb_clean_duties_dlies.midnite',
                            'lb_clean_duties.note', 'lb_clean_duties_dlies.id', 'lb_clean_duties_dlies.task', 'lb_app_reviews.date', 'lb_clean_duties.updated_at'])
                    ->where('lb_clean_duties.id', $request->query('clean-duties-id'));

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('opening_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->opening != 0) {
                                $check .= '<input type="checkbox" id="checkdlyclndtiso' . $data->id . '" checked onclick="changeLbDlyCleanDuties(' . $data->id . ', \'opening\')">';
                            } else {
                                $check .= '<input type="checkbox" id="checkdlyclndtiso' . $data->id . '" onclick="changeLbDlyCleanDuties(' . $data->id . ', \'opening\')">';
                            }
                            $check .= '<label for="checkdlyclndtiso' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('closing_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->closing != 0) {
                                $check .= '<input type="checkbox" id="checkdlyclndtisc' . $data->id . '" checked onclick="changeLbDlyCleanDuties(' . $data->id . ', \'closing\')">';
                            } else {
                                $check .= '<input type="checkbox" id="checkdlyclndtisc' . $data->id . '" onclick="changeLbDlyCleanDuties(' . $data->id . ', \'closing\')">';
                            }
                            $check .= '<label for="checkdlyclndtisc' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('midnite_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->midnite != 0) {
                                $check .= '<input type="checkbox" id="checkdlyclndtism' . $data->id . '" checked onclick="changeLbDlyCleanDuties(' . $data->id . ', \'midnite\')">';
                            } else {
                                $check .= '<input type="checkbox" id="checkdlyclndtism' . $data->id . '" onclick="changeLbDlyCleanDuties(' . $data->id . ', \'midnite\')">';
                            }
                            $check .= '<label for="checkdlyclndtism' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->rawColumns(['opening_desc', 'closing_desc', 'midnite_desc'])
                        ->make();
    }

    public function weeklyDtble(Request $request)
    {
        $query = DB::table('lb_clean_duties_wlies')
                    ->join('lb_clean_duties', 'lb_clean_duties.id', 'lb_clean_duties_wlies.lb_clean_duties_id')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_clean_duties.lb_app_review_id')
                    ->select(['lb_clean_duties.id as duties_id', 'lb_clean_duties.note', 'lb_clean_duties.section',
                            'lb_clean_duties_wlies.opening', 'lb_clean_duties_wlies.closing', 'lb_clean_duties_wlies.midnite',
                            'lb_clean_duties.note', 'lb_clean_duties_wlies.id', 'lb_clean_duties_wlies.task', 'lb_clean_duties_wlies.day',
                            'lb_clean_duties_wlies.pic', 'lb_app_reviews.date', 'lb_clean_duties.updated_at'])
                    ->where('lb_clean_duties.id', $request->query('clean-duties-id'));

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('opening_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->opening != 0) {
                                $check .= '<input type="checkbox" id="checkwlyclndtiso' . $data->id . '" checked onclick="changeLbWlyCleanDuties(' . $data->id . ', \'opening\')">';
                            } else {
                                $check .= '<input type="checkbox" id="checkwlyclndtiso' . $data->id . '" onclick="changeLbWlyCleanDuties(' . $data->id . ', \'opening\')">';
                            }
                            $check .= '<label for="checkwlyclndtiso' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('closing_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->closing != 0) {
                                $check .= '<input type="checkbox" id="checkwlyclndtisc' . $data->id . '" checked onclick="changeLbWlyCleanDuties(' . $data->id . ', \'closing\')">';
                            } else {
                                $check .= '<input type="checkbox" id="checkwlyclndtisc' . $data->id . '" onclick="changeLbWlyCleanDuties(' . $data->id . ', \'closing\')">';
                            }
                            $check .= '<label for="checkwlyclndtisc' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('midnite_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->midnite != 0) {
                                $check .= '<input type="checkbox" id="checkwlyclndtism' . $data->id . '" checked onclick="changeLbWlyCleanDuties(' . $data->id . ', \'midnite\')">';
                            } else {
                                $check .= '<input type="checkbox" id="checkwlyclndtism' . $data->id . '" onclick="changeLbWlyCleanDuties(' . $data->id . ', \'midnite\')">';
                            }
                            $check .= '<label for="checkwlyclndtism' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('pic_input', function ($data) {
                            return '<input type="text" class="form-control form-control-sm mul" id="tfkwlyclndtispic' . $data->id . '" value="' . $data->pic . '" onchange="changeLbWlyCleanDuties(' . $data->id . ', \'pic\')" style="min-width: 6rem;">';
                        })
                        ->rawColumns(['opening_desc', 'closing_desc', 'midnite_desc', 'task', 'pic_input'])
                        ->make();
    }

    public function dailyUpdate(Request $request)
    {
        $lbCleanDutiesDly = LbCleanDutiesDly::find($request->id);

        $lbCleanDuties = DB::table('lb_clean_duties')
                        ->where('id', $lbCleanDutiesDly->lb_clean_duties_id )
                        ->first();

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbCleanDuties->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbCleanDutiesDly[$request->shift] = $request->checklist;
            if( $lbCleanDutiesDly->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("daily cleaning duties")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("daily cleaning duties")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function weeklyUpdate(Request $request)
    {
        $lbCleanDutiesWly = LbCleanDutiesWly::find($request->id);

        $lbCleanDuties = DB::table('lb_clean_duties')
                        ->where('id', $lbCleanDutiesWly->lb_clean_duties_id )
                        ->first();

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbCleanDuties->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbCleanDutiesWly[$request->field] = $request->data;
            if( $lbCleanDutiesWly->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("daily cleaning duties")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("daily cleaning duties")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function updateNote(Request $request)
    {
        $lbCleanDuties = LbCleanDuties::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbCleanDuties->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            // validation user
            // if(in_array(Auth::id(), [$appReview->crew_opening_id, $appReview->crew_midnight_id, $appReview->crew_closing_id])){
                $lbCleanDuties->note = $request->note;
                if ($lbCleanDuties->save()) {
                    $stat = 'success';
                    $msg = Lang::get("message.update.success", ["data" => Lang::get("daily cleaning duties note")]);
                } else {
                    $stat = 'failed';
                    $msg = Lang::get("message.update.failed", ["data" => Lang::get("daily cleaning duties note")]);
                }
            // } else {
            //     $stat = 'failed';
            //     $msg = Lang::get("You do not have authorization, please confirm to store manager");
            // }
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
            'section' => $request->section
        ];

        $data = [];

        $dlyDut = DB::table('lb_clean_duties_dlies as lc')
                    ->join('lb_clean_duties as lcd', 'lcd.id', 'lc.lb_clean_duties_id')
                    ->where('lcd.lb_app_review_id', $lbAppReviewId)
                    ->where('lcd.section', $request->section)
                    ->select('lc.task', 'lcd.section', 'lcd.note', 'lc.opening', 'lc.closing', 'lc.midnite')
                    ->get();

        $wlyDut = DB::table('lb_clean_duties_wlies as lc')
                    ->join('lb_clean_duties as lcd', 'lcd.id', 'lc.lb_clean_duties_id')
                    ->where('lcd.lb_app_review_id', $lbAppReviewId)
                    ->where('lcd.section', $request->section)
                    ->select('lc.task', 'lcd.section', 'lcd.note', 'lc.day', 'lc.pic', 'lc.opening', 'lc.closing', 'lc.midnite')
                    ->get();

        $data['daily'] = $dlyDut;
        $data['weekly'] = $wlyDut;

        $dataview = [
            'title' => 'CLEANING DATIES',
            'data' => $data,
            'section' => $request->section,
            'header' => $header
        ];

        return view('logbook.preview.cleaning-duties-preview', $dataview)->render();
    }
}
