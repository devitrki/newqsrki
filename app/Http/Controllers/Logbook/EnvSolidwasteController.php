<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbEnvSolid;
use App\Models\Plant;

class EnvSolidwasteController extends Controller
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
        return view('logbook.env-solidwaste', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_env_solids')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_env_solids.lb_app_review_id')
                    ->join('plants', 'plants.id', 'lb_app_reviews.plant_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->where('lb_app_reviews.plant_id', $request->query('plant-id'))
                    ->where('lb_env_solids.month', $request->query('month'))
                    ->where('lb_env_solids.year', $request->query('year'))
                    ->select(['lb_env_solids.id', 'lb_app_reviews.date',
                        'lb_env_solids.organik', 'lb_env_solids.non_organik', 'lb_env_solids.daur_ulang',
                        'lb_env_solids.b3', 'lb_env_solids.pic', 'lb_app_reviews.plant_id',
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
                        'organik' => 'required',
                        'non_organik' => 'required',
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

                    $lbEnvSolid = new LbEnvSolid;
                    $lbEnvSolid->lb_app_review_id = $appReview->id;
                    $lbEnvSolid->month = $request->month;
                    $lbEnvSolid->year = $request->year;
                    $lbEnvSolid->organik = $request->organik;
                    $lbEnvSolid->non_organik = $request->non_organik;
                    $lbEnvSolid->daur_ulang = $request->daur_ulang;
                    $lbEnvSolid->b3 = $request->b3;
                    $lbEnvSolid->pic = $request->pic;
                    if ($lbEnvSolid->save()) {
                        $stat = 'success';
                        $msg = Lang::get("message.save.success", ["data" => Lang::get("environmental solidwaste")]);
                    } else {
                        $stat = 'failed';
                        $msg = Lang::get("message.save.failed", ["data" => Lang::get("environmental solidwaste")]);
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
            'organik' => 'required',
            'non_organik' => 'required',
            'pic' => 'required',
        ]);

        $lbEnvSolid = LbEnvSolid::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbEnvSolid->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbEnvSolid->month = $request->month;
            $lbEnvSolid->year = $request->year;
            $lbEnvSolid->organik = $request->organik;
            $lbEnvSolid->non_organik = $request->non_organik;
            $lbEnvSolid->daur_ulang = $request->daur_ulang;
            $lbEnvSolid->b3 = $request->b3;
            $lbEnvSolid->pic = $request->pic;
            if ($lbEnvSolid->save()) {
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("environmental solidwaste")]);
            } else {
                DB::rollBack();
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("environmental solidwaste")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbEnvSolid = LbEnvSolid::find($id);
        if ($lbEnvSolid->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("environmental solidwaste")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("environmental solidwaste")]);
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

        $data = DB::table('lb_env_solids as les')
                    ->where('les.lb_app_review_id', $lbAppReviewId)
                    ->select('les.organik', 'les.non_organik', 'les.daur_ulang', 'les.b3', 'les.pic')
                    ->get();

        $dataview = [
            'title' => 'ENV CONTROL (SOLID WASTE)',
            'data' => $data,
            'header' => $header
        ];


        return view('logbook.preview.env-solidwaste-preview', $dataview)->render();
    }
}
