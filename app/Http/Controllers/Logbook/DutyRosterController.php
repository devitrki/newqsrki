<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Logbook\LbBriefing;
use App\Models\Logbook\LbDutyRoster;
use App\Models\Logbook\LbAppReview;

class DutyRosterController extends Controller
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
        return view('logbook.duty-roster', $dataview)->render();
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
        $lbBriefings = [];
        if($qAppReview->count() > 0){
            $appReview = $qAppReview->first();

            // check already created or not
            $qLbBriefing = LbBriefing::where('lb_app_review_id', $appReview->id)
                                        ->where('shift', $section);

            if($qLbBriefing->count() > 0){
                $lbBriefings = $qLbBriefing->first();
                $created = true;
            }

        }

        if( $created ){
            $dataview = [
                'lbBreafings' => $lbBriefings,
                'appReview' => $appReview,
                'date' => $request->query('date'),
                'section' => $request->query('section'),
            ];
            return view('logbook.duty-roster-dataview', $dataview)->render();
        } else {
            return view('logbook.not-found')->render();
        }
    }

    public function dtble(Request $request)
    {
        $query = DB::table('lb_duty_rosters')
                    ->where('lb_briefing_id', $request->query('briefing-id'))
                    ->select('shift', 'mod', 'cashier', 'lobby', 'kitchen', 'id');

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('shift_input', function ($data) {
                    $options = ['Opening', 'Closing', 'Midnite'];
                    $slct = '<select name="lbdtrstshift[]" class="form-control form-control-sm mul" style="min-width: 6rem;">';

                    if( !in_array($data->shift, $options) ){
                        $slct .= '<option value="" selected></option>';
                    }

                    foreach ($options as $opt) {
                        if( $opt == $data->shift ){
                            $slct .= '<option value="' . $opt . '" selected>' . $opt . '</option>';
                        } else {
                            $slct .= '<option value="' . $opt . '">' . $opt . '</option>';
                        }
                    }
                    $slct .= '</select>';
                    return $slct;
                })
                ->addColumn('mod_input', function ($data) {
                    return '<input type="text" class="form-control form-control-sm mul" name="lbdtrstmod[]" value="' . $data->mod . '" style="min-width: 6rem;">';
                })
                ->addColumn('cashier_input', function ($data) {
                    return '<input type="text" class="form-control form-control-sm mul" name="lbdtrstcashier[]" value="' . $data->cashier . '" style="min-width: 6rem;">';
                })
                ->addColumn('lobby_input', function ($data) {
                    return '<input type="text" class="form-control form-control-sm mul" name="lbdtrstlobby[]" value="' . $data->lobby . '" style="min-width: 6rem;">';
                })
                ->addColumn('kitchen_input', function ($data) {
                    return '<input type="text" class="form-control form-control-sm mul" name="lbdtrstkitchen[]" value="' . $data->kitchen . '" style="min-width: 6rem;">';
                })
                ->rawColumns(['shift_input', 'mod_input', 'cashier_input', 'lobby_input', 'kitchen_input'])
                ->make();
    }

    public function update(Request $request, $id)
    {
        $lbBriefing = LbBriefing::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbBriefing->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbBriefing->sales_target = $request->sales_target;
            $lbBriefing->highlight = $request->highlight;
            $lbBriefing->mtd_sales = $request->mtd_sales;
            $lbBriefing->rf_updates = Str::title($request->rf_updates);
            if ($lbBriefing->save()) {

                $dutyRosterId = json_decode($request->duty_roster_id);
                $shifts = json_decode($request->shifts);
                $mods = json_decode($request->mods);
                $cashiers = json_decode($request->cashiers);
                $lobbys = json_decode($request->lobbys);
                $kitchens = json_decode($request->kitchens);

                for ($i=0; $i < sizeof($dutyRosterId); $i++) {

                    $lbDutyRoster = LbDutyRoster::find($dutyRosterId[$i]);
                    $lbDutyRoster->shift = Str::title($shifts[$i]);
                    $lbDutyRoster->mod = Str::title($mods[$i]);
                    $lbDutyRoster->cashier = Str::title($cashiers[$i]);
                    $lbDutyRoster->kitchen = Str::title($lobbys[$i]);
                    $lbDutyRoster->lobby = Str::title($kitchens[$i]);
                    $lbDutyRoster->save();

                }

                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("briefing")]);
            } else {
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("briefing")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }


        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function storeDuty(Request $request)
    {
        $request->validate([
                        'shift' => 'required',
                        'mod' => 'required',
                        'cashier' => 'required',
                        'kitchen' => 'required',
                        'lobby' => 'required',
                    ]);

        $lbDutyRoster = new LbDutyRoster;
        $lbDutyRoster->shift = $request->shift;
        $lbDutyRoster->mod = $request->mod;
        $lbDutyRoster->cashier = $request->cashier;
        $lbDutyRoster->kitchen = $request->kitchen;
        $lbDutyRoster->lobby = $request->lobby;
        $lbDutyRoster->lb_briefing_id = $request->lb_briefing_id;
        if ($lbDutyRoster->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("duty roster")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("duty roster")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function updateDuty(Request $request, $id)
    {
        $request->validate([
                        'shift' => 'required',
                        'mod' => 'required',
                        'cashier' => 'required',
                        'kitchen' => 'required',
                        'lobby' => 'required',
                    ]);

        $lbDutyRoster = LbDutyRoster::find($request->id);
        $lbDutyRoster->shift = $request->shift;
        $lbDutyRoster->mod = $request->mod;
        $lbDutyRoster->cashier = $request->cashier;
        $lbDutyRoster->kitchen = $request->kitchen;
        $lbDutyRoster->lobby = $request->lobby;
        if ($lbDutyRoster->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("duty roster")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("duty roster")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {

        $lbDutyRoster = LbDutyRoster::find($id);
        if ($lbDutyRoster->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("duty roster")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("duty roster")]);
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

        $shifts = ['Morning', 'Afternoon', 'Midnite'];
        $data = [];
        foreach ($shifts as $shift) {

            $briefings = DB::table('lb_briefings')
                            ->where('shift', $shift)
                            ->where('lb_app_review_id', $appReview->id)
                            ->select('id', 'sales_target', 'highlight', 'mtd_sales', 'rf_updates')
                            ->first();

            $duties = DB::table('lb_duty_rosters')
                        ->where('lb_briefing_id', $briefings->id)
                        ->select('shift', 'mod', 'cashier', 'kitchen', 'lobby')
                        ->get();

            $rows = [];

            $rows[] = [
                'col1' => 'SALES TARGET',
                'col2' => ( is_numeric($briefings->sales_target) ) ? Helper::convertNumberToInd($briefings->sales_target, '', 0) : 0,
                'col3' => 'SHIFT',
                'col4' => $duties[0]->shift,
                'col5' => $duties[1]->shift,
                'col6' => $duties[2]->shift,
                'col7' => $duties[3]->shift,
            ];

            $rows[] = [
                'col1' => 'MTD SALES',
                'col2' => ( is_numeric($briefings->mtd_sales) ) ? Helper::convertNumberToInd($briefings->mtd_sales, '', 0) : 0,
                'col3' => 'MOD',
                'col4' => $duties[0]->mod,
                'col5' => $duties[1]->mod,
                'col6' => $duties[2]->mod,
                'col7' => $duties[3]->mod,
            ];

            $rows[] = [
                'col1' => 'TODAY HIGHLIGHT',
                'col2' => $briefings->highlight,
                'col3' => 'CASHIER',
                'col4' => $duties[0]->cashier,
                'col5' => $duties[1]->cashier,
                'col6' => $duties[2]->cashier,
                'col7' => $duties[3]->cashier,
            ];

            $rows[] = [
                'col1' => 'RF UPDATES',
                'col2' => $briefings->rf_updates,
                'col3' => 'KITCHEN',
                'col4' => $duties[0]->kitchen,
                'col5' => $duties[1]->kitchen,
                'col6' => $duties[2]->kitchen,
                'col7' => $duties[3]->kitchen,
            ];

            $rows[] = [
                'col1' => '',
                'col2' => '',
                'col3' => 'LOBBY',
                'col4' => $duties[0]->lobby,
                'col5' => $duties[1]->lobby,
                'col6' => $duties[2]->lobby,
                'col7' => $duties[3]->lobby,
            ];

            $data[] = [
                'shift' => $shift,
                'rows' => $rows
            ];
        }

        $dataview = [
            'title' => 'Daily Briefing & Duty Roster',
            'data' => $data,
            'header' => $header
        ];


        return view('logbook.preview.duty-roster-preview', $dataview)->render();
    }
}
