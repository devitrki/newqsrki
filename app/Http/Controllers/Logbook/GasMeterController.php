<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbGasMeter;
use App\Models\Plant;

class GasMeterController extends Controller
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
        return view('logbook.gas-meter', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_gas_meters')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_gas_meters.lb_app_review_id')
                    ->join('plants', 'plants.id', 'lb_app_reviews.plant_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->where('lb_app_reviews.plant_id', $request->query('plant-id'))
                    ->where('lb_gas_meters.month', $request->query('month'))
                    ->where('lb_gas_meters.year', $request->query('year'))
                    ->select(['lb_gas_meters.id', 'lb_app_reviews.date',
                        'lb_gas_meters.initial_meter', 'lb_gas_meters.final_meter', 'lb_gas_meters.usage',
                        'lb_gas_meters.pic', 'lb_app_reviews.plant_id',
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
                $lbGasMeter = new LbGasMeter;
                $lbGasMeter->lb_app_review_id = $appReview->id;
                $lbGasMeter->month = $request->month;
                $lbGasMeter->year = $request->year;
                $lbGasMeter->initial_meter = round( $request->initial_meter,2 );
                $lbGasMeter->final_meter = round($request->final_meter,2);
                $lbGasMeter->usage = round($request->usage,2);
                $lbGasMeter->pic = $request->pic;
                if ($lbGasMeter->save()) {
                    $stat = 'success';
                    $msg = Lang::get("message.save.success", ["data" => Lang::get("gas meter")]);
                } else {
                    $stat = 'failed';
                    $msg = Lang::get("message.save.failed", ["data" => Lang::get("gas meter")]);
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

        $lbGasMeter = LbGasMeter::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbGasMeter->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbGasMeter->month = $request->month;
            $lbGasMeter->year = $request->year;
            $lbGasMeter->initial_meter = round( $request->initial_meter,2 );
            $lbGasMeter->final_meter = round($request->final_meter,2);
            $lbGasMeter->usage = round($request->usage,2);
            $lbGasMeter->pic = $request->pic;
            if ($lbGasMeter->save()) {
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("gas meter")]);
            } else {
                DB::rollBack();
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("gas meter")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbGasMeter = LbGasMeter::find($id);
        if ($lbGasMeter->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("gas meter")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("gas meter")]);
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

        $data = DB::table('lb_gas_meters')
                    ->where('lb_app_review_id', $lbAppReviewId)
                    ->select('initial_meter', 'final_meter', 'usage', 'pic')
                    ->get();

        $dataview = [
            'title' => 'GAS METER FORM',
            'data' => $data,
            'header' => $header
        ];


        return view('logbook.preview.gas-meter-preview', $dataview)->render();
    }
}
