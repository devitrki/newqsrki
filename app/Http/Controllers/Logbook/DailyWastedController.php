<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbDlyWasted;
use App\Models\Plant;
use App\Models\User;

class DailyWastedController extends Controller
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
        return view('logbook.daily-wasted', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_dly_wasteds')
                    ->leftJoin('lb_app_reviews', 'lb_app_reviews.id', 'lb_dly_wasteds.lb_app_review_id')
                    ->join('plants', 'plants.id', 'lb_app_reviews.plant_id')
                    ->join('material_logbooks', 'material_logbooks.id', 'lb_dly_wasteds.material_logbook_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->select(['lb_dly_wasteds.id', 'lb_app_reviews.date', 'material_logbooks.name',
                        'lb_dly_wasteds.qty', 'lb_dly_wasteds.remark', 'lb_dly_wasteds.last_update', 'lb_dly_wasteds.time',
                        'lb_app_reviews.plant_id', DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS plant"),
                        'lb_dly_wasteds.uom'
                    ])
                    ->orderBy('lb_dly_wasteds.time')
                    ->orderBy('material_logbooks.name');

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
                    return date("d-m-Y", strtotime($data->date));
                })
                ->filterColumn('name', function($query, $keyword) {
                    $query->whereRaw("material_logbooks.name like ?", ["%{$keyword}%"]);
                })
                ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'date' => 'required',
                        'plant' => 'required',
                        'material' => 'required',
                        'qty' => 'required',
                        'uom' => 'required',
                        'time' => 'required',
                        'remark' => 'required'
                    ]);

        $userAuth = $request->get('userAuth');

        // check date and plant in app review logbook
        $queryCheck = DB::table('lb_app_reviews')
                        ->where('company_id', $userAuth->company_id_selected)
                        ->where('date', $request->date)
                        ->where('plant_id', $request->plant);

        if($queryCheck->count() > 0){

            $appReview = $queryCheck->first();

            if($appReview->mod_approval != '1'){
                // validation user
                // if(!in_array(Auth::id(), [$appReview->crew_opening_id, $appReview->crew_midnight_id, $appReview->crew_closing_id])){
                    $time = Str::of($request->time)->replace('_', '0');

                    $lbDlyWasted = new LbDlyWasted;
                    $lbDlyWasted->lb_app_review_id = $appReview->id;
                    $lbDlyWasted->material_logbook_id = $request->material;
                    $lbDlyWasted->qty = $request->qty;
                    $lbDlyWasted->uom = strtoupper($request->uom);
                    $lbDlyWasted->time = $time;
                    $lbDlyWasted->remark = $request->remark;
                    $lbDlyWasted->last_update = User::getNameById(Auth::id());
                    if ($lbDlyWasted->save()) {
                        $stat = 'success';
                        $msg = Lang::get("message.save.success", ["data" => Lang::get("daily wasted")]);
                    } else {
                        $stat = 'failed';
                        $msg = Lang::get("message.save.failed", ["data" => Lang::get("daily wasted")]);
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
            'qty' => 'required',
            'uom' => 'required',
            'time' => 'required',
            'remark' => 'required'
        ]);

        $time = Str::of($request->time)->replace('_', '0');

        $lbDlyWasted = LbDlyWasted::find($request->id);
        $lbDlyWasted->qty = $request->qty;
        $lbDlyWasted->uom = strtoupper($request->uom);
        $lbDlyWasted->time = $time;
        $lbDlyWasted->remark = $request->remark;
        $lbDlyWasted->last_update = User::getNameById(Auth::id());
        if ($lbDlyWasted->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("daily wasted")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("daily wasted")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbDlyWasted = LbDlyWasted::find($id);
        if ($lbDlyWasted->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("daily wasted")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("daily wasted")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function preview($lbAppReviewId)
    {
        $appReview = LbAppReview::getFullDataById($lbAppReviewId);

        $header = [
            'outlet' => $appReview->outlet,
            'date' => date("d-m-Y", strtotime($appReview->date)),
            'mod' => ($appReview->mod_pic) ? $appReview->mod_pic : '-',
        ];

        $data = DB::table('lb_dly_wasteds as ldw')
                    ->join('material_logbooks as ml', 'ml.id', 'ldw.material_logbook_id')
                    ->where('ldw.lb_app_review_id', $lbAppReviewId)
                    ->select('ml.name', 'ldw.uom', 'ldw.qty', 'ldw.time', 'ldw.remark', 'ldw.last_update')
                    ->get();

        $dataview = [
            'title' => 'DAILY WASTED',
            'data' => $data,
            'header' => $header
        ];


        return view('logbook.preview.daily-wasted-preview', $dataview)->render();
    }
}
