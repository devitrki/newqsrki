<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbRecMaterial;
use App\Models\Plant;

class ReceptionMaterialController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'outlet', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);
        $status = [
            [ 'id' => 'Pass', 'text' => 'Pass'],
            [ 'id' => 'Hold', 'text' => 'Hold'],
            [ 'id' => 'Reject', 'text' => 'Reject'],
        ];
        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'status' => $status,
            'menu_id' => $request->query('menuid')
        ];
        return view('logbook.reception-material', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_rec_materials')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_rec_materials.lb_app_review_id')
                    ->join('plants', 'plants.id', 'lb_app_reviews.plant_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->where('lb_app_reviews.plant_id', $request->query('plant-id'))
                    ->whereBetween('lb_app_reviews.date', [$request->query('from-date'), $request->query('until-date')])
                    ->select(['lb_rec_materials.id', 'lb_app_reviews.date', 'lb_rec_materials.product', 'lb_rec_materials.transport_temperature',
                        'lb_rec_materials.transport_cleanliness', 'lb_rec_materials.product_temperature', 'lb_rec_materials.producer',
                        'lb_rec_materials.country', 'lb_rec_materials.supplier', 'lb_rec_materials.logo_halal', 'lb_rec_materials.product_condition',
                        'lb_rec_materials.production_code', 'lb_rec_materials.product_qty', 'lb_rec_materials.product_uom', 'lb_rec_materials.expired_date', 'lb_rec_materials.status',
                        'lb_rec_materials.pic', 'lb_app_reviews.plant_id',
                        DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS plant")
                    ]);

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('date_desc', function ($data) {
                    return date("d-m-Y", strtotime($data->date));
                })
                ->addColumn('expired_date_desc', function ($data) {
                    return date("d-m-Y", strtotime($data->expired_date));
                })
                ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'date' => 'required',
                        'plant' => 'required',
                        'product' => 'required',
                        'transport_temperature' => 'required',
                        'transport_cleanliness' => 'required',
                        'product_temperature' => 'required',
                        'producer' => 'required',
                        'country' => 'required',
                        'supplier' => 'required',
                        'logo_halal' => 'required',
                        'product_condition' => 'required',
                        'production_code' => 'required',
                        'product_qty' => 'required',
                        'product_uom' => 'required',
                        'expired_date' => 'required',
                        'status' => 'required',
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
                    $lbRecMaterial = new LbRecMaterial;
                    $lbRecMaterial->lb_app_review_id = $appReview->id;
                    $lbRecMaterial->product = $request->product;
                    $lbRecMaterial->transport_temperature = $request->transport_temperature;
                    $lbRecMaterial->transport_cleanliness = $request->transport_cleanliness;
                    $lbRecMaterial->product_temperature = $request->product_temperature;
                    $lbRecMaterial->producer = $request->producer;
                    $lbRecMaterial->country = $request->country;
                    $lbRecMaterial->supplier = $request->supplier;
                    $lbRecMaterial->logo_halal = $request->logo_halal;
                    $lbRecMaterial->product_condition = $request->product_condition;
                    $lbRecMaterial->production_code = $request->production_code;
                    $lbRecMaterial->product_qty = $request->product_qty;
                    $lbRecMaterial->product_uom = strtoupper($request->product_uom);
                    $lbRecMaterial->expired_date = $request->expired_date;
                    $lbRecMaterial->status = $request->status;
                    $lbRecMaterial->pic = $request->pic;
                    if ($lbRecMaterial->save()) {
                        $stat = 'success';
                        $msg = Lang::get("message.save.success", ["data" => Lang::get("reception material / product")]);
                    } else {
                        $stat = 'failed';
                        $msg = Lang::get("message.save.failed", ["data" => Lang::get("reception material / product")]);
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
            'transport_temperature' => 'required',
            'transport_cleanliness' => 'required',
            'product_temperature' => 'required',
            'producer' => 'required',
            'country' => 'required',
            'supplier' => 'required',
            'logo_halal' => 'required',
            'product_condition' => 'required',
            'production_code' => 'required',
            'product_qty' => 'required',
            'product_uom' => 'required',
            'expired_date' => 'required',
            'status' => 'required',
            'pic' => 'required',
        ]);

        $lbRecMaterial = LbRecMaterial::find($request->id);
        $lbRecMaterial->product = $request->product;
        $lbRecMaterial->transport_temperature = $request->transport_temperature;
        $lbRecMaterial->transport_cleanliness = $request->transport_cleanliness;
        $lbRecMaterial->product_temperature = $request->product_temperature;
        $lbRecMaterial->producer = $request->producer;
        $lbRecMaterial->country = $request->country;
        $lbRecMaterial->supplier = $request->supplier;
        $lbRecMaterial->logo_halal = $request->logo_halal;
        $lbRecMaterial->product_condition = $request->product_condition;
        $lbRecMaterial->production_code = $request->production_code;
        $lbRecMaterial->product_qty = $request->product_qty;
        $lbRecMaterial->product_uom = strtoupper($request->product_uom);
        $lbRecMaterial->expired_date = $request->expired_date;
        $lbRecMaterial->status = $request->status;
        $lbRecMaterial->pic = $request->pic;
        if ($lbRecMaterial->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("reception material / product")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("reception material / product")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbRecMaterial = LbRecMaterial::find($id);
        if ($lbRecMaterial->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("reception material / product")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("reception material / product")]);
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

        $data = DB::table('lb_rec_materials as lrm')
                    ->where('lrm.lb_app_review_id', $lbAppReviewId)
                    ->select('lrm.id', 'lrm.product', 'lrm.transport_temperature',
                            'lrm.transport_cleanliness', 'lrm.product_temperature', 'lrm.producer',
                            'lrm.country', 'lrm.supplier', 'lrm.logo_halal', 'lrm.product_condition',
                            'lrm.production_code', 'lrm.product_qty', 'lrm.product_uom', 'lrm.expired_date', 'lrm.status',
                            'lrm.pic')
                    ->get();

        $dataview = [
            'title' => 'RECEPTION MATERIAL / PRODUCT OUTLET',
            'data' => $data,
            'header' => $header
        ];


        return view('logbook.preview.reception-material-preview', $dataview)->render();
    }
}
