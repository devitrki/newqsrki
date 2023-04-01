<?php

namespace App\Http\Controllers\Logbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Models\Plant;
use App\Models\Logbook\LbMonSls;
use App\Models\Logbook\LbMonSlsCas;
use App\Models\Logbook\LbMonSlsCasDet;
use App\Models\Logbook\LbMonSlsDet;
use App\Models\Logbook\LbAppReview;
use App\User;

class MoneySalesController extends Controller
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
        return view('logbook.money-sales', $dataview)->render();
    }

    public function dataview(Request $request){
        $userAuth = $request->get('userAuth');
        $plantId = $request->query('plant-id');
        $date = $request->query('date');
        $shift = $request->query('shift');

        // check to app review
        $qAppReview = LbAppReview::where('date', $date)
                                ->where('company_id', $userAuth->company_id_selected)
                                ->where('plant_id', $plantId);
        $created = false;
        $lbMonSls = [];
        $lbMonSlsCas = [];
        if($qAppReview->count() > 0){
            $appReview = $qAppReview->first();

            // check already created or not
            $qLbMonSls = LbMonSls::where('lb_app_review_id', $appReview->id);

            if($qLbMonSls->count() > 0){
                $lbMonSls = $qLbMonSls->first();

                $lbMonSlsCas = LbMonSlsCas::where('lb_mon_sls_id', $lbMonSls->id)
                                            ->where('shift', $shift)
                                            ->first();

                $created = true;
            }

        }

        if( $created ){
            $dataview = [
                'lbMonSls' => $lbMonSls,
                'lbMonSlsCas' => $lbMonSlsCas,
                'appReview' => $appReview,
                'date' => $request->query('date'),
                'shift' => $request->query('shift'),
            ];
            return view('logbook.money-sales-dataview', $dataview)->render();
        } else {
            return view('logbook.not-found')->render();
        }
    }

    public function dtbleCashier(Request $request)
    {
        $query = DB::table('lb_mon_sls_cas_dets')
                    ->where('lb_mon_sls_cas_dets.lb_mon_sls_cas_id', $request->query('mon-sls-cas-id'));

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('opening_cash_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->opening_cash, 'Rp. ', 0);
                        })
                        ->addColumn('total_sales_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->total_sales, 'Rp. ', 0);
                        })
                        ->addColumn('bca_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->bca, 'Rp. ', 0);
                        })
                        ->addColumn('mandiri_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->mandiri, 'Rp. ', 0);
                        })
                        ->addColumn('go_pay_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->go_pay, 'Rp. ', 0);
                        })
                        ->addColumn('grab_pay_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->grab_pay, 'Rp. ', 0);
                        })
                        ->addColumn('gobiz_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->gobiz, 'Rp. ', 0);
                        })
                        ->addColumn('ovo_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->ovo, 'Rp. ', 0);
                        })
                        ->addColumn('shoope_pay_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->shoope_pay, 'Rp. ', 0);
                        })
                        ->addColumn('shopee_food_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->shopee_food, 'Rp. ', 0);
                        })
                        ->addColumn('dana_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->dana, 'Rp. ', 0);
                        })
                        ->addColumn('voucher_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->voucher, 'Rp. ', 0);
                        })
                        ->addColumn('delivery_sales_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->delivery_sales, 'Rp. ', 0);
                        })
                        ->addColumn('drive_thru_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->drive_thru, 'Rp. ', 0);
                        })
                        ->addColumn('compliment_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->compliment, 'Rp. ', 0);
                        })
                        ->addColumn('total_cash_hand_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->total_cash_hand, 'Rp. ', 0);
                        })
                        ->rawColumns([
                            'opening_cash_desc',
                            'total_sales_desc',
                            'bca_desc',
                            'mandiri_desc',
                            'go_pay_desc',
                            'grab_pay_desc',
                            'gobiz_desc',
                            'ovo_desc',
                            'shoope_pay_desc',
                            'shopee_food_desc',
                            'dana_desc',
                            'voucher_desc',
                            'delivery_sales_desc',
                            'drive_thru_desc',
                            'compliment_desc',
                            'total_cash_hand_desc'
                        ])
                        ->make();
    }

    public function dtbleDetail(Request $request)
    {
        $query = DB::table('lb_mon_sls_dets')
                    ->where('lb_mon_sls_dets.lb_mon_sls_id', $request->query('mon-sls-id'));

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('date_desc', function ($data) {
                            return date("d-m-Y", strtotime($data->updated_at));
                        })
                        ->addColumn('cash_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->cash, 'Rp. ', 0);
                        })
                        ->addColumn('total_non_cash_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->total_non_cash, 'Rp. ', 0);
                        })
                        ->addColumn('total_sales_desc', function ($data) {
                            return \App\Library\Helper::convertNumberToInd($data->total_sales, 'Rp. ', 0);
                        })
                        ->make();
    }

    public function cashierDetUpdate(Request $request, $id)
    {
        $lbMonSlsCasDet = LbMonSlsCasDet::find($request->id);
        $lbMonSlsCas = LbMonSlsCas::find($lbMonSlsCasDet->lb_mon_sls_cas_id);
        $lbMonSls = LbMonSls::find($lbMonSlsCas->lb_mon_sls_id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbMonSls->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbMonSlsCasDet->cashier_name = ($request->cashier_name) ? $request->cashier_name : '';
            $lbMonSlsCasDet->total_sales = ($request->total_sales) ? $request->total_sales : 0;
            $lbMonSlsCasDet->bca = ($request->bca) ? $request->bca : 0;
            $lbMonSlsCasDet->mandiri = ($request->mandiri) ? $request->mandiri : 0;
            $lbMonSlsCasDet->go_pay = ($request->go_pay) ? $request->go_pay : 0;
            $lbMonSlsCasDet->grab_pay = ($request->grab_pay) ? $request->grab_pay : 0;
            $lbMonSlsCasDet->gobiz = ($request->gobiz) ? $request->gobiz : 0;
            $lbMonSlsCasDet->ovo = ($request->ovo) ? $request->ovo : 0;
            $lbMonSlsCasDet->shoope_pay = ($request->shoope_pay) ? $request->shoope_pay : 0;
            $lbMonSlsCasDet->shopee_food = ($request->shopee_food) ? $request->shopee_food : 0;
            $lbMonSlsCasDet->dana = ($request->dana) ? $request->dana : 0;
            $lbMonSlsCasDet->voucher = ($request->voucher) ? $request->voucher : 0;
            $lbMonSlsCasDet->delivery_sales = ($request->delivery_sales) ? $request->delivery_sales : 0;
            $lbMonSlsCasDet->drive_thru = ($request->drive_thru) ? $request->drive_thru : 0;
            $lbMonSlsCasDet->compliment = ($request->compliment) ? $request->compliment : 0;
            $lbMonSlsCasDet->total_cash_hand = ($request->total_cash_hand) ? $request->total_cash_hand : 0;
            if ($lbMonSlsCasDet->save()) {
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("money sales handling cashier")]);
            } else {
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("money sales handling cashier")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $lbMonSls = LbMonSls::find($request->id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbMonSls->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbMonSls->name = $request->name;
            $lbMonSls->nik = $request->nik;
            $lbMonSls->function = $request->function;
            $lbMonSls->total_money = $request->total_money;
            $lbMonSls->deposit_date = $request->deposit_date;
            $lbMonSls->deposit_to = $request->deposit_to;
            $lbMonSls->dp_ulang_tahun = $request->dp_ulang_tahun;
            $lbMonSls->dp_big_order = $request->dp_big_order;
            if ($lbMonSls->save()) {
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("money sales handling")]);
            } else {
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("money sales handling")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function cashierUpdate(Request $request)
    {
        $lbMonSlsCas = LbMonSlsCas::find($request->id);
        $lbMonSls = LbMonSls::find($lbMonSlsCas->lb_mon_sls_id);

        $appReview = DB::table('lb_app_reviews')
                        ->where('id', $lbMonSls->lb_app_review_id )
                        ->first();

        // validation application logbook not yet approved
        if($appReview->mod_approval != '1'){
            $lbMonSlsCas->total_sales = $request->total_sales;
            $lbMonSlsCas->total_non_cash = $request->total_non_cash;
            $lbMonSlsCas->total_cash = $request->total_cash;
            $lbMonSlsCas->brankas_money = $request->brankas_money;
            $lbMonSlsCas->pending_pc = $request->pending_pc;
            $lbMonSlsCas->hand_over_by = $request->hand_over_by;
            $lbMonSlsCas->received_by = $request->received_by;
            $lbMonSlsCas->p100 = $request->{'100'};
            $lbMonSlsCas->p200 = $request->{'200'};
            $lbMonSlsCas->p500 = $request->{'500'};
            $lbMonSlsCas->p1000 = $request->{'1000'};
            $lbMonSlsCas->p2000 = $request->{'2000'};
            $lbMonSlsCas->p5000 = $request->{'5000'};
            $lbMonSlsCas->p10000 = $request->{'10000'};
            $lbMonSlsCas->p20000 = $request->{'20000'};
            $lbMonSlsCas->p50000 = $request->{'50000'};
            $lbMonSlsCas->p100000 = $request->{'100000'};
            if ($lbMonSlsCas->save()) {
                $stat = 'success';
                $msg = Lang::get("message.update.success", ["data" => Lang::get("money sales handling cashier")]);
            } else {
                $stat = 'failed';
                $msg = Lang::get("message.update.failed", ["data" => Lang::get("money sales handling cashier")]);
            }
        } else {
            $stat = 'failed';
            $msg = Lang::get("You do not change data, application Logbook this date have approved store manager. Please confirm to store manager.");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // detail
    public function storeDetail(Request $request)
    {
        $request->validate([
                        'date' => 'required',
                        'day' => 'required',
                        'total_cash' => 'required',
                        'total_non_cash' => 'required',
                        'total_sales' => 'required',
                    ]);

        $lbMonSlsDet = new LbMonSlsDet;
        $lbMonSlsDet->date = $request->date;
        $lbMonSlsDet->day = $request->day;
        $lbMonSlsDet->cash = $request->total_cash;
        $lbMonSlsDet->total_non_cash = $request->total_non_cash;
        $lbMonSlsDet->total_sales = $request->total_sales;
        $lbMonSlsDet->hand_over_by = $request->hand_over_by;
        $lbMonSlsDet->received_by = $request->received_by;
        $lbMonSlsDet->lb_mon_sls_id = $request->lb_mon_sls_id;
        if ($lbMonSlsDet->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("money sales handling detail")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("money sales handling detail")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function updateDetail(Request $request, $id)
    {
        $request->validate([
                        'date' => 'required',
                        'day' => 'required',
                        'total_cash' => 'required',
                        'total_non_cash' => 'required',
                        'total_sales' => 'required',
                    ]);

        $lbMonSlsDet = LbMonSlsDet::find($request->id);
        $lbMonSlsDet->date = $request->date;
        $lbMonSlsDet->day = $request->day;
        $lbMonSlsDet->cash = $request->total_cash;
        $lbMonSlsDet->total_non_cash = $request->total_non_cash;
        $lbMonSlsDet->total_sales = $request->total_sales;
        $lbMonSlsDet->hand_over_by = $request->hand_over_by;
        $lbMonSlsDet->received_by = $request->received_by;
        if ($lbMonSlsDet->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("money sales handling detail")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("money sales handling detail")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroyDetail($id)
    {
        $lbMonSlsDet = LbMonSlsDet::find($id);
        if ($lbMonSlsDet->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("money sales handling detail")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("money sales handling detail")]);
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

        $qLbMonSls = DB::table('lb_mon_sls as lms')
                        ->where('lms.lb_app_review_id', $lbAppReviewId);

        $cashiers = [];
        $cashier_dets = [];

        $listLabels = [
            ['cashier_name', 'Name'],
            ['opening_cash', 'Opening Cash'],
            ['total_sales', 'Total Sales'],
            ['bca', 'BCA'],
            ['mandiri', 'Mandiri'],
            ['go_pay', 'Go-Pay'],
            ['grab_pay', 'Grab-Pay'],
            ['gobiz', 'GoBiz'],
            ['ovo', 'OVO'],
            ['shoope_pay', 'Shoope-Pay'],
            ['dana', 'Dana'],
            ['voucher', 'Voucher'],
            ['delivery_sales', 'Delivery Sales'],
            ['drive_thru', 'Drive Thru'],
            ['compliment', 'Compliment'],
            ['total_cash_hand', 'Total Cash In Hand']
        ];

        $lbMonSls = [];
        $lbMonSlsDets = [];

        if( $qLbMonSls->count() > 0 ){

            $lbMonSls = $qLbMonSls->first();

            $lbMonSlsDets = DB::table('lb_mon_sls_dets')
                        ->where('lb_mon_sls_id', $lbMonSls->id)
                        ->get();

            foreach ($listLabels as $list) {

                $shifts = ['Opening', 'Midnite', 'Closing'];

                $dataCashier = [
                    $list[1], //label
                ];

                foreach ($shifts as $sh => $shift) {

                    $lbMonSlsCas = DB::table('lb_mon_sls_cas')
                                    ->where('lb_mon_sls_id', $lbMonSls->id)
                                    ->where('shift', $shift)
                                    ->select('id', 'total_sales', 'total_non_cash', 'total_cash', 'brankas_money', 'pending_pc',
                                            'hand_over_by', 'received_by', 'p100', 'p200', 'p500', 'p1000', 'p2000', 'p5000',
                                            'p10000', 'p20000', 'p50000', 'p100000')
                                    ->first();

                    $totalSales = ( is_numeric($lbMonSlsCas->total_sales) ) ? $lbMonSlsCas->total_sales : 0 ;
                    $p100 = ( is_numeric($lbMonSlsCas->p100) ) ? $lbMonSlsCas->p100 : 0 ;
                    $total_non_cash = ( is_numeric($lbMonSlsCas->total_non_cash) ) ? $lbMonSlsCas->total_non_cash : 0 ;
                    $total_cash = ( is_numeric($lbMonSlsCas->total_cash) ) ? $lbMonSlsCas->total_cash : 0 ;
                    $p200 = ( is_numeric($lbMonSlsCas->p200) ) ? Helper::convertNumberToInd($lbMonSlsCas->p200, '', 0) : 0 ;
                    $p500 = ( is_numeric($lbMonSlsCas->p500) ) ? Helper::convertNumberToInd($lbMonSlsCas->p500, '', 0) : 0 ;
                    $p1000 = ( is_numeric($lbMonSlsCas->p1000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p1000, '', 0) : 0 ;
                    $brankas_money = ( is_numeric($lbMonSlsCas->brankas_money) ) ? $lbMonSlsCas->brankas_money : 0 ;
                    $pending_pc = ( is_numeric($lbMonSlsCas->pending_pc) ) ? $lbMonSlsCas->pending_pc : 0 ;
                    $p2000 = ( is_numeric($lbMonSlsCas->p2000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p2000, '', 0) : 0 ;
                    $p5000 = ( is_numeric($lbMonSlsCas->p5000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p5000, '', 0) : 0 ;
                    $p10000 = ( is_numeric($lbMonSlsCas->p10000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p10000, '', 0) : 0 ;
                    $p20000 = ( is_numeric($lbMonSlsCas->p20000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p20000, '', 0) : 0 ;
                    $p50000 = ( is_numeric($lbMonSlsCas->p50000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p50000, '', 0) : 0 ;
                    $p100000 = ( is_numeric($lbMonSlsCas->p100000) ) ? Helper::convertNumberToInd($lbMonSlsCas->p100000, '', 0) : 0 ;
                    $cashiers['row2'][$sh] = [Helper::convertNumberToInd($totalSales, 'Rp. ', 0)];
                    $cashiers['row3'][$sh] = [$p100];
                    $cashiers['row4'][$sh] = [Helper::convertNumberToInd($total_non_cash, 'Rp. ', 0), Helper::convertNumberToInd($total_cash, 'Rp. ', 0), $p200];
                    $cashiers['row5'][$sh] = [$p500];
                    $cashiers['row6'][$sh] = [$p1000];
                    $cashiers['row7'][$sh] = [Helper::convertNumberToInd($brankas_money, 'Rp. ', 0), Helper::convertNumberToInd($pending_pc, 'Rp. ', 0), $p2000];
                    $cashiers['row8'][$sh] = [$p5000];
                    $cashiers['row9'][$sh] = [$p10000];
                    $cashiers['row10'][$sh] = [$lbMonSlsCas->hand_over_by, $lbMonSlsCas->received_by, $p20000];
                    $cashiers['row11'][$sh] = [$p50000];
                    $cashiers['row12'][$sh] = [$p100000];

                    for ($i=1; $i <= 4; $i++) {
                        $lbMonSlsCasDet = DB::table('lb_mon_sls_cas_dets')
                                            ->where('lb_mon_sls_cas_id', $lbMonSlsCas->id)
                                            ->where('cashier_no', 'Cashier ' . $i)
                                            ->select($list[0])
                                            ->first();

                        $dataCashier[] = (is_numeric($lbMonSlsCasDet->{$list[0]})) ? Helper::convertNumberToInd($lbMonSlsCasDet->{$list[0]}, '', 0) : $lbMonSlsCasDet->{$list[0]};
                    }

                }

                $cashier_dets[] = $dataCashier;

            }

        }

        $dataview = [
            'title' => 'MONEY AND SALES HANDLING',
            'cashiers' => $cashiers,
            'cashier_dets' => $cashier_dets,
            'lbMonSls' => $lbMonSls,
            'lbMonSlsDets' => $lbMonSlsDets,
            'header' => $header
        ];


        return view('logbook.preview.money-sales-preview', $dataview)->render();
    }

}
