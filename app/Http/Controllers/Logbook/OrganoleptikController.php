<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbOrganoleptik;
use App\Models\Plant;


class OrganoleptikController extends Controller
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
        return view('logbook.organoleptik', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_organoleptiks')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_organoleptiks.lb_app_review_id')
                    ->join('plants', 'plants.id', 'lb_app_reviews.plant_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->where('lb_app_reviews.plant_id', $request->query('plant-id'))
                    ->whereBetween('lb_app_reviews.date', [$request->query('from-date'), $request->query('until-date')])
                    ->select(['lb_organoleptiks.id', 'lb_app_reviews.date', 'lb_organoleptiks.product', 'lb_organoleptiks.code',
                        'lb_organoleptiks.time', 'lb_organoleptiks.aroma', 'lb_organoleptiks.taste',
                        'lb_organoleptiks.texture', 'lb_organoleptiks.color',
                        'lb_organoleptiks.pic', 'lb_app_reviews.plant_id',
                        DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS plant")
                    ])
                    ->orderBy('lb_organoleptiks.time')
                    ->orderBy('lb_organoleptiks.product');

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
                        'product' => 'required',
                        'code' => 'required',
                        'time' => 'required',
                        'taste' => 'required',
                        'aroma' => 'required',
                        'texture' => 'required',
                        'color' => 'required',
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

                    $time = Str::of($request->time)->replace('_', '0');

                    $lbOrganoleptik = new LbOrganoleptik;
                    $lbOrganoleptik->lb_app_review_id = $appReview->id;
                    $lbOrganoleptik->product = $request->product;
                    $lbOrganoleptik->code = $request->code;
                    $lbOrganoleptik->time = $time;
                    $lbOrganoleptik->taste = $request->taste;
                    $lbOrganoleptik->aroma = $request->aroma;
                    $lbOrganoleptik->texture = $request->texture;
                    $lbOrganoleptik->color = $request->color;
                    $lbOrganoleptik->pic = $request->pic;
                    if ($lbOrganoleptik->save()) {
                        $stat = 'success';
                        $msg = Lang::get("message.save.success", ["data" => Lang::get("organoleptik")]);
                    } else {
                        $stat = 'failed';
                        $msg = Lang::get("message.save.failed", ["data" => Lang::get("organoleptik")]);
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
            'product' => 'required',
            'code' => 'required',
            'time' => 'required',
            'taste' => 'required',
            'aroma' => 'required',
            'texture' => 'required',
            'color' => 'required',
            'pic' => 'required',
        ]);

        $time = Str::of($request->time)->replace('_', '0');

        $lbOrganoleptik = LbOrganoleptik::find($request->id);
        $lbOrganoleptik->product = $request->product;
        $lbOrganoleptik->code = $request->code;
        $lbOrganoleptik->time = $time;
        $lbOrganoleptik->taste = $request->taste;
        $lbOrganoleptik->aroma = $request->aroma;
        $lbOrganoleptik->texture = $request->texture;
        $lbOrganoleptik->color = $request->color;
        $lbOrganoleptik->pic = $request->pic;
        if ($lbOrganoleptik->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("organoleptik")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("organoleptik")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbOrganoleptik = LbOrganoleptik::find($id);
        if ($lbOrganoleptik->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("organoleptik")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("organoleptik")]);
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

        $data = DB::table('lb_organoleptiks as lo')
                    ->where('lo.lb_app_review_id', $lbAppReviewId)
                    ->select('lo.id', 'lo.product', 'lo.code',
                            'lo.time', 'lo.taste', 'lo.aroma',
                            'lo.texture', 'lo.color', 'lo.pic')
                    ->get();

        $dataview = [
            'title' => 'ORGANOLEPTIK',
            'data' => $data,
            'header' => $header
        ];


        return view('logbook.preview.organoleptik-preview', $dataview)->render();
    }

}
