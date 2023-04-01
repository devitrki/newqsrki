<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Logbook\LbDlyDuties;
use App\Models\Logbook\LbDlyDutiesDet;
use App\Models\Logbook\LbAppReview;
use App\User;
use App\Models\Configuration;

class DailyDutiesController extends Controller
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
        return view('logbook.daily-duties', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_dly_duties_dets')
                    ->join('lb_dly_duties', 'lb_dly_duties.id', 'lb_dly_duties_dets.lb_dly_duties_id')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_dly_duties.lb_app_review_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->select(['lb_dly_duties.id as duties_id', 'lb_dly_duties.task', 'lb_dly_duties.section',
                            'lb_dly_duties_dets.opening', 'lb_dly_duties_dets.closing', 'lb_dly_duties_dets.midnite',
                            'lb_dly_duties.note', 'lb_dly_duties_dets.id', 'lb_app_reviews.date', 'lb_dly_duties.updated_at'])
                    ->where('lb_dly_duties.section', $request->query('section'));

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
                        ->addColumn('opening_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->opening != 0) {
                                $check .= '<input type="checkbox" id="checklbdlydutieso' . $data->id . '" checked onclick="changeLbDlyDuties(' . $data->id . ', \'opening\')">';
                            } else {
                                $check .= '<input type="checkbox" id="checklbdlydutieso' . $data->id . '" onclick="changeLbDlyDuties(' . $data->id . ', \'opening\')">';
                            }
                            $check .= '<label for="checklbdlydutieso' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('closing_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->closing != 0) {
                                $check .= '<input type="checkbox" id="checklbdlydutiesc' . $data->id . '" checked onclick="changeLbDlyDuties(' . $data->id . ', \'closing\')">';
                            } else {
                                $check .= '<input type="checkbox" id="checklbdlydutiesc' . $data->id . '" onclick="changeLbDlyDuties(' . $data->id . ', \'closing\')">';
                            }
                            $check .= '<label for="checklbdlydutiesc' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->addColumn('midnite_desc', function ($data) {
                            $check = '<div class="checkbox" style="margin-bottom:5px;">';
                            if ($data->midnite != 0) {
                                $check .= '<input type="checkbox" id="checklbdlydutiesm' . $data->id . '" checked onclick="changeLbDlyDuties(' . $data->id . ', \'midnite\')">';
                            } else {
                                $check .= '<input type="checkbox" id="checklbdlydutiesm' . $data->id . '" onclick="changeLbDlyDuties(' . $data->id . ', \'midnite\')">';
                            }
                            $check .= '<label for="checklbdlydutiesm' . $data->id . '"></label>';
                            $check .= '</div>';
                            return $check;
                        })
                        ->filterColumn('task', function($query, $keyword) {
                            $query->whereRaw("lb_dly_duties.task like ?", ["%{$keyword}%"]);
                        })
                        ->rawColumns(['opening_desc', 'closing_desc', 'midnite_desc'])
                        ->make();
    }

    public function update(Request $request)
    {
        $lbDlyDutiesDet = LbDlyDutiesDet::find($request->id);

        $lbDlyDuties = DB::table('lb_dly_duties')
                        ->where('id', $lbDlyDutiesDet->lb_dly_duties_id )
                        ->first();

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbDlyDuties->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbDlyDutiesDet[$request->shift] = $request->checklist;
            if( $lbDlyDutiesDet->save() ){
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("daily duties")]);
            } else{
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("daily duties")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function updateNote(Request $request)
    {
        $lbDlyDuties = LbDlyDuties::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbDlyDuties->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            // validation user
            // if(in_array(Auth::id(), [$appReview->crew_opening_id, $appReview->crew_midnight_id, $appReview->crew_closing_id])){
                $lbDlyDuties->note = $request->note;
                if ($lbDlyDuties->save()) {
                    $stat = 'success';
                    $msg = Lang::get("message.update.success", ["data" => Lang::get("daily duties note")]);
                } else {
                    $stat = 'failed';
                    $msg = Lang::get("message.update.failed", ["data" => Lang::get("daily duties note")]);
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

        $duties = DB::table('lb_dly_duties_dets as ldd')
                    ->join('lb_dly_duties as ld', 'ld.id', 'ldd.lb_dly_duties_id')
                    ->where('ld.lb_app_review_id', $lbAppReviewId)
                    ->where('ld.section', $request->section)
                    ->select('ld.task', 'ld.section', 'ld.note', 'ldd.opening', 'ldd.closing', 'ldd.midnite')
                    ->get();

        $dataview = [
            'title' => 'DAILY DATIES',
            'data' => $duties,
            'section' => $request->section,
            'header' => $header
        ];

        return view('logbook.preview.daily-duties-preview', $dataview)->render();
    }

}
