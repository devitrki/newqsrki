<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbEnvWater;
use App\Models\Plant;

class EnvWastewaterController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'months' => Helper::getListMonth(),
            'years' => Helper::getListYear(5),
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.env-wastewater', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_env_waters')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_env_waters.lb_app_review_id')
                    ->join('plants', 'plants.id', 'lb_app_reviews.plant_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->where('lb_app_reviews.plant_id', $request->query('plant-id'))
                    ->where('lb_env_waters.month', $request->query('month'))
                    ->where('lb_env_waters.year', $request->query('year'))
                    ->select(['lb_env_waters.id', 'lb_app_reviews.date',
                        'lb_env_waters.volume_inlet', 'lb_env_waters.volume_outlet',
                        'lb_env_waters.pic', 'lb_app_reviews.plant_id',
                        DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS plant")
                    ]);

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('date_desc', function ($data) {
                    return date("d-m-Y", strtotime($data->date));
                })
                ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'date' => 'required',
                        'plant' => 'required',
                        'volume_inlet' => 'required',
                        'volume_outlet' => 'required',
                        'pic' => 'required',
                    ]);

        $userAuth = $request->get('userAuth');

        // check date and plant in app review logbook
        $queryCheck = DB::table('lb_app_reviews')
                        ->where('company_id', $userAuth->company_id_selected)
                        ->where('date', $request->date)
                        ->where('plant_id', $request->plant);

        if($queryCheck->count() > 0){

            $appReview = $queryCheck->first();

            // validation application logbook not yet approved
            if($appReview->mod_approval != '1'){
                // validation user
                // if(!in_array(Auth::id(), [$appReview->crew_opening_id, $appReview->crew_midnight_id, $appReview->crew_closing_id])){

                    $lbEnvWater = new LbEnvWater;
                    $lbEnvWater->lb_app_review_id = $appReview->id;
                    $lbEnvWater->month = $request->month;
                    $lbEnvWater->year = $request->year;
                    $lbEnvWater->volume_inlet = $request->volume_inlet;
                    $lbEnvWater->volume_outlet = $request->volume_outlet;
                    $lbEnvWater->pic = $request->pic;
                    if ($lbEnvWater->save()) {
                        $stat = 'success';
                        $msg = Lang::get("message.save.success", ["data" => Lang::get("environmental wastewater")]);
                    } else {
                        $stat = 'failed';
                        $msg = Lang::get("message.save.failed", ["data" => Lang::get("environmental wastewater")]);
                    }

                // } else {
                //     $stat = 'failed';
                //     $msg = Lang::get("You do not have authorization, please confirm to store manager");
                // }
            } else {
                $stat = 'failed';
                $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
            }

        } else {
            $stat = 'failed';
            $msg = Lang::get("Cannot adding data this date. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required',
            'plant' => 'required',
            'volume_inlet' => 'required',
            'volume_outlet' => 'required',
            'pic' => 'required',
        ]);

        $lbEnvWater = LbEnvWater::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbEnvWater->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbEnvWater->month = $request->month;
            $lbEnvWater->year = $request->year;
            $lbEnvWater->volume_inlet = $request->volume_inlet;
            $lbEnvWater->volume_outlet = $request->volume_outlet;
            $lbEnvWater->pic = $request->pic;
            if ($lbEnvWater->save()) {
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("environmental wastewater")]);
            } else {
                DB::rollBack();
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("environmental wastewater")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbEnvWater = LbEnvWater::find($id);
        if ($lbEnvWater->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("environmental wastewater")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("environmental wastewater")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function preview($lbAppReviewId)
    {
        $appReview = LbAppReview::getFullDataById($lbAppReviewId);

        $header = [
            'outlet' => $appReview->outlet,
            'date' => date("d-m-Y", strtotime($appReview->date))
        ];

        $data = DB::table('lb_env_waters as lew')
                    ->where('lew.lb_app_review_id', $lbAppReviewId)
                    ->select('lew.volume_inlet', 'lew.volume_outlet', 'lew.pic')
                    ->get();

        $dataview = [
            'title' => 'ENV CONTROL (WASTEWATER)',
            'data' => $data,
            'header' => $header
        ];


        return view('logbook.preview.env-wastewater-preview', $dataview)->render();
    }
}
