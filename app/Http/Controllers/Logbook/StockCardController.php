<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Logbook\LbAppReview;
use App\Models\Logbook\LbStockCard;
use App\Models\Plant;

class StockCardController extends Controller
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
        return view('logbook.stock-card', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('lb_stock_cards')
                    ->join('lb_app_reviews', 'lb_app_reviews.id', 'lb_stock_cards.lb_app_review_id')
                    ->join('plants', 'plants.id', 'lb_app_reviews.plant_id')
                    ->join('material_logbooks', 'material_logbooks.id', 'lb_stock_cards.material_logbook_id')
                    ->where('lb_app_reviews.company_id', $userAuth->company_id_selected)
                    ->where('lb_app_reviews.plant_id', $request->query('plant-id'))
                    ->where('lb_stock_cards.month', $request->query('month'))
                    ->where('lb_stock_cards.year', $request->query('year'))
                    ->where('lb_stock_cards.material_logbook_id', $request->query('material'))
                    ->select(['lb_stock_cards.id', 'lb_app_reviews.date', 'material_logbooks.name', 'material_logbooks.uom',
                        'lb_stock_cards.no_po', 'lb_stock_cards.stock_initial', 'lb_stock_cards.stock_in_gr',
                        'lb_stock_cards.stock_in_tf', 'lb_stock_cards.stock_out_used', 'lb_stock_cards.stock_out_waste',
                        'lb_stock_cards.stock_out_tf', 'lb_stock_cards.stock_last', 'lb_stock_cards.description',
                        'lb_stock_cards.pic', 'lb_stock_cards.material_logbook_id', 'lb_app_reviews.plant_id',
                        DB::raw("CONCAT(plants.initital ,' ', plants.short_name) AS plant")
                    ]);

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('date_desc', function ($data) {
                    return date("d-m-Y", strtotime($data->date));
                })
                ->addColumn('item', function ($data) {
                    return $data->name . ' (' . $data->uom .  ')';
                })
                ->filterColumn('item', function($query, $keyword) {
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
                        'no_po' => 'required',
                        'stock_initial' => 'required',
                        'stock_last' => 'required',
                        'stock_gr_in' => 'required',
                        'stock_tf_in' => 'required',
                        'stock_used_out' => 'required',
                        'stock_waste_out' => 'required',
                        'stock_tf_gi_out' => 'required',
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

            // check item exist
            $countCheck = DB::table('lb_stock_cards')
                            ->where('lb_app_review_id', $appReview->id)
                            ->where('material_logbook_id', $request->material)
                            ->count();

            if($countCheck < 1){

                // validation application logbook not yet approved
                if($appReview->mod_approval != '1'){
                    // validation user
                    // if(!in_array(Auth::id(), [$appReview->crew_opening_id, $appReview->crew_midnight_id, $appReview->crew_closing_id])){

                        $lbStockCard = new LbStockCard;
                        $lbStockCard->lb_app_review_id = $appReview->id;
                        $lbStockCard->month = $request->month;
                        $lbStockCard->year = $request->year;
                        $lbStockCard->material_logbook_id = $request->material;
                        $lbStockCard->no_po = $request->no_po;
                        $lbStockCard->stock_initial = $request->stock_initial;
                        $lbStockCard->stock_in_gr = $request->stock_gr_in;
                        $lbStockCard->stock_in_tf = $request->stock_tf_in;
                        $lbStockCard->stock_out_used = $request->stock_used_out;
                        $lbStockCard->stock_out_waste = $request->stock_waste_out;
                        $lbStockCard->stock_out_tf = $request->stock_tf_gi_out;
                        $lbStockCard->stock_last = $request->stock_last;
                        $lbStockCard->description = $request->description;
                        $lbStockCard->pic = $request->pic;
                        if ($lbStockCard->save()) {
                            $stat = 'success';
                            $msg = Lang::get("message.save.success", ["data" => Lang::get("stock card")]);
                        } else {
                            $stat = 'failed';
                            $msg = Lang::get("message.save.failed", ["data" => Lang::get("stock card")]);
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
                // already created
                $stat = 'failed';
                $msg = Lang::get("validation.unique", ["attribute" => Lang::get("stock card")]);
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
            'material' => 'required',
            'no_po' => 'required',
            'stock_initial' => 'required',
            'stock_last' => 'required',
            'stock_gr_in' => 'required',
            'stock_tf_in' => 'required',
            'stock_used_out' => 'required',
            'stock_waste_out' => 'required',
            'stock_tf_gi_out' => 'required',
            'pic' => 'required',
        ]);

        $lbStockCard = LbStockCard::find($request->id);
        $lbStockCard->no_po = $request->no_po;
        $lbStockCard->stock_initial = $request->stock_initial;
        $lbStockCard->stock_in_gr = $request->stock_gr_in;
        $lbStockCard->stock_in_tf = $request->stock_tf_in;
        $lbStockCard->stock_out_used = $request->stock_used_out;
        $lbStockCard->stock_out_waste = $request->stock_waste_out;
        $lbStockCard->stock_out_tf = $request->stock_tf_gi_out;
        $lbStockCard->stock_last = $request->stock_last;
        $lbStockCard->description = $request->description;
        $lbStockCard->pic = $request->pic;
        if ($lbStockCard->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("stock card")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("stock card")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $lbStockCard = LbStockCard::find($id);
        if ($lbStockCard->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("stock card")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("stock card")]);
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

        $data = DB::table('lb_stock_cards as lsc')
                    ->join('material_logbooks as ml', 'ml.id', 'lsc.material_logbook_id')
                    ->where('lsc.lb_app_review_id', $lbAppReviewId)
                    ->select('ml.name', 'ml.uom', 'lsc.no_po', 'lsc.stock_initial', 'lsc.stock_in_gr',
                            'lsc.stock_in_tf', 'lsc.stock_out_used', 'lsc.stock_out_waste', 'lsc.stock_out_tf',
                            'lsc.stock_last', 'lsc.description', 'lsc.pic')
                    ->get();

        $dataview = [
            'title' => 'STOCK CARD',
            'data' => $data,
            'header' => $header
        ];


        return view('logbook.preview.stock-card-preview', $dataview)->render();
    }
}
