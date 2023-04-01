<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbElectricMeter;
use App\Models\Plant;

class ElectricMeterController extends Controller
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
        return view('logbook.electric-meter', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_electric_meters')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_electric_meters.lb_app_review_id')
                    ->join('plants', 'plants.id', 'lb_app_reviews.plant_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->where('lb_app_reviews.plant_id', $request->query('plant-id'))
                    ->where('lb_electric_meters.month', $request->query('month'))
                    ->where('lb_electric_meters.year', $request->query('year'))
                    ->select(['lb_electric_meters.id', 'lb_app_reviews.date',
                        'lb_electric_meters.initial_meter', 'lb_electric_meters.final_meter', 'lb_electric_meters.usage',
                        'lb_electric_meters.pic', 'lb_app_reviews.plant_id',
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
                        'initial_meter' => 'required',
                        'final_meter' => 'required',
                        'usage' => 'required',
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
                $lbElectricMeter = new LbElectricMeter;
                $lbElectricMeter->lb_app_review_id = $appReview->id;
                $lbElectricMeter->month = $request->month;
                $lbElectricMeter->year = $request->year;
                $lbElectricMeter->initial_meter = round( $request->initial_meter,2 );
                $lbElectricMeter->final_meter = round($request->final_meter,2);
                $lbElectricMeter->usage = round($request->usage,2);
                $lbElectricMeter->pic = $request->pic;
                if ($lbElectricMeter->save()) {
                    $stat = 'success';
                    $msg = Lang::get("message.save.success", ["data" => Lang::get("electric meter")]);
                } else {
                    $stat = 'failed';
                    $msg = Lang::get("message.save.failed", ["data" => Lang::get("electric meter")]);
                }
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
            'initial_meter' => 'required',
            'final_meter' => 'required',
            'usage' => 'required',
            'pic' => 'required',
        ]);

        $lbElectricMeter = LbElectricMeter::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbElectricMeter->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbElectricMeter->month = $request->month;
            $lbElectricMeter->year = $request->year;
            $lbElectricMeter->initial_meter = round( $request->initial_meter,2 );
            $lbElectricMeter->final_meter = round($request->final_meter,2);
            $lbElectricMeter->usage = round($request->usage,2);
            $lbElectricMeter->pic = $request->pic;
            if ($lbElectricMeter->save()) {
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("electric meter")]);
            } else {
                DB::rollBack();
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("electric meter")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbElectricMeter = LbElectricMeter::find($id);
        if ($lbElectricMeter->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("electric meter")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("electric meter")]);
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

        $data = DB::table('lb_electric_meters')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->select('initial_meter', 'final_meter', 'usage', 'pic')
                    ->get();

        $dataview = [
            'title' => 'ELECTRIC METER FORM',
            'data' => $data,
            'header' => $header
        ];


        return view('logbook.preview.electric-meter-preview', $dataview)->render();
    }
}
