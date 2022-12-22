<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Imports\Financeacc\MassClearingImport;
use App\Exports\Financeacc\MassClearingExport;
use App\Jobs\Financeacc\ScheduleMassClearing;

use App\Models\Financeacc\MassClearing;
use App\Models\Financeacc\MassClearingDetail;
use App\Models\Interfaces\AlohaTransactionLog;
use App\Models\Interfaces\VtecTransactionLog;
use App\Models\Interfaces\VtecOrderPayDetail;
use App\Models\Plant;
use App\Models\Pos;
use App\Models\Pos\Aloha;
use App\Models\SpecialGl;
use App\Models\BankChargeGl;

use App\Repositories\SapRepositorySapImpl;
use App\Entities\SapMiddleware;

class MassClearingController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('financeacc.mass-clearing', $dataview)->render();
    }

    public function dtble()
    {
        $query = DB::table('mass_clearings')
                    ->join('users', 'users.id', 'mass_clearings.user_id')
                    ->join('profiles', 'profiles.id', 'users.profile_id')
                    ->select('mass_clearings.*', 'profiles.name')
                    ->orderByDesc('id');

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->filterColumn('name', function($query, $keyword) {
                            $sql = "profiles.name like ?";
                            $query->whereRaw($sql, ["%{$keyword}%"]);
                        })
                        ->addColumn('time_process_start_desc', function ($data) {
                            return ($data->time_process_start != '' && $data->time_process_start != null ) ? Helper::DateConvertFormat($data->time_process_start, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
                        })
                        ->addColumn('time_process_finish_desc', function ($data) {
                            return ($data->time_process_finish != '' && $data->time_process_finish != null ) ? Helper::DateConvertFormat($data->time_process_finish, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
                        })
                        ->addColumn('upload_time', function ($data) {
                            return ($data->created_at != '' && $data->created_at != null ) ? Helper::DateConvertFormat($data->created_at, 'Y-m-d H:i:s', 'd-m-Y H:i:s') : '-';
                        })
                        ->addColumn('status_generate_desc', function ($data) {
                            if( $data->status_generate == 0 ){
                                $status = "<div class = 'badge badge-warning'>Waiting</div>";
                            } else if( $data->status_generate == 1 ){
                                $status = "<div class = 'badge badge-info'>On Process</div>";
                            } else{
                                $status = "<div class = 'badge badge-success'>Finish</div>";
                            }
                            return $status;
                        })
                        ->rawColumns(['status_generate_desc'])
                        ->make();
    }

    public function dtblePreview(Request $request)
    {
        $query = DB::table('mass_clearing_details')
                    ->join('plants', 'plants.id', 'mass_clearing_details.plant_id')
                    ->select(
                        'mass_clearing_details.*',
                        'plants.short_name',
                        'plants.initital',
                        'plants.customer_code',
                    )
                    ->where('mass_clearing_id', $request->id);

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->addColumn('bank_in_date_desc', function ($data) {
                            return ($data->bank_in_date != '' && $data->bank_in_date != null ) ? Helper::DateConvertFormat($data->bank_in_date, 'Y-m-d', 'd-m-Y') : '-';
                        })
                        ->addColumn('outlet_name', function ($data) {
                            return $data->initital . ' ' . $data->short_name;
                        })
                        ->addColumn('nominal_desc', function ($data) {
                            return Helper::convertNumberToInd($data->bank_in_nominal, '', 0);
                        })
                        ->addColumn('charge_desc', function ($data) {
                            return Helper::convertNumberToInd($data->bank_in_charge, '', 0);
                        })
                        ->addColumn('status_process_desc', function ($data) {
                            if( $data->status_process == 0 ){
                                $status = "<div class = 'badge badge-warning'>Waiting</div>";
                            } else if( $data->status_process == 1 ){
                                $status = "<div class = 'badge badge-info'>On Process</div>";
                            } else{
                                $status = "<div class = 'badge badge-success'>Finish</div>";
                            }
                            return $status;
                        })
                        ->addColumn('status_generate_desc', function ($data) {
                            if( $data->status_generate == 0 ){
                                return '-';
                            } else if( $data->status_generate == 1 ){
                                return 'yes';
                            } else{
                                return 'no';
                            }
                        })
                        ->rawColumns(['status_process_desc'])
                        ->make();
    }

    public function downloadTemplate()
    {
        return response()->download( public_path('template-bank-in.xlsx') );
    }

    public function downloadGenerate($id)
    {
        $massClearing = DB::table('mass_clearings')
                            ->where('id', $id)
                            ->select('filename')
                            ->first();

        if( $massClearing->filename == '' ){
            echo "This file cannot downloaded, please wait.";
            return false;
        }

        return response()->download( storage_path('app/public/mass-clearing/' . $massClearing->filename . '.xlsx') );
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx',
            'description' => 'required',
        ]);

        $stat = 'success';
        $msg = Lang::get("message.upload.success", ["data" => Lang::get("Mass Clearing")]);

        if ($request->file('file_excel')) {

            $userAuth = $request->get('userAuth');

            try {
                $import = new MassClearingImport($userAuth->company_id_selected, $request->description);
                Excel::import($import, request()->file('file_excel'));
                $return = $import->return;

                $stat = $return['status'];
                $msg = ($return['message'] != '') ? $return['message'] : $msg;

                if( $stat == 'success' ){
                    if (ScheduleMassClearing::dispatch($return['id'])->onQueue('massclearing')) {
                        $stat = 'success';
                    } else {
                        $stat = 'failed';
                        $msg = 'Create job generate mass clearing failed';
                    }
                }

            } catch (\Throwable $th) {
                $msg = Lang::get("File excel not valid. Please download the valid file.");
            }
        }

        return response()->json(Helper::resJSON($stat, $msg));
    }

    public function generate()
    {
        $massClearing = DB::table('mass_clearings')
                            ->where('id', 7)
                            ->first();

        $massClearingDetails = DB::table('mass_clearing_details')
                            ->where('mass_clearing_id', 7)
                            ->select(
                                'mass_clearing_id',
                                'bank_in_bank_gl',
                                'bank_in_date',
                                'sales_date',
                                'sales_month',
                                'sales_year',
                                'special_gl',
                                'plant_id',
                                DB::raw('SUM(bank_in_nominal) as bank_in_nominal, SUM(bank_in_charge) as bank_in_charge, COUNT(id) as total_row')
                            )
                            ->groupBy('mass_clearing_id', 'bank_in_bank_gl', 'bank_in_date', 'sales_date', 'sales_month', 'sales_year', 'special_gl', 'plant_id')
                            ->get();

        $no = 1;
        $generateItem = [];
        $resultProcess = [];

        foreach ($massClearingDetails as $massClearingDetail) {
            $pos_id = Plant::getPosById($massClearingDetail->plant_id);
            $pos = Pos::find($pos_id);
            $sapCode = SpecialGl::getSapCodebySpecialGl($massClearingDetail->special_gl);
            $reference = SpecialGl::getRefbySpecialGl($massClearingDetail->special_gl);
            $salesDates = MassClearingDetail::getSalesDate($massClearingDetail);
            $customerCodePlant = Plant::getCustomerCodeById($massClearingDetail->plant_id);

            $nominalPos = 0;
            $postingSap = true;
            $totalBankCharge = (int)$massClearingDetail->bank_in_charge;
            $totalBankIn = $massClearingDetail->bank_in_nominal + $totalBankCharge;
            $generate = true;

            foreach ($salesDates as $salesDate) {

                $dataUpload = [
                    'outlet_id' => $customerCodePlant,
                    'transaction_date' => $salesDate
                ];

                $sapRepository = new SapRepositorySapImpl($massClearing->company_id, true);
                $sapResponse = $sapRepository->getTransactionLog($dataUpload);
                if ($sapResponse['status']) {
                    $respSap = $sapResponse['response'];
                    if (!$respSap['sales']['success']) {
                        $postingSap = false;
                        break;
                    }

                    $posRepository = Pos::getInstanceRepo($pos);
                    $initConnectionAloha = $posRepository->initConnectionDB();
                    if (!$initConnectionAloha['status']) {
                        $postingSap = false;
                        break;
                    }

                    $nominalPosDate = $posRepository->getTotalPaymentByMethodPayment($massClearingDetail->plant_id, $salesDate, $sapCode);

                } else {
                    $postingSap = false;
                    break;
                }

                $nominalPos += $nominalPosDate;
            }

            if( !$postingSap ){
                $generate = false;
                !dd("Sales not yet posting to SAP");
            }

            $selisih = $nominalPos - $totalBankIn;
            $selisihPercent = round(( abs($selisih) / $nominalPos) * 100, 2);

            if( $selisih <> 0){
                if( strtoupper($massClearingDetail->special_gl) != 'Z'  ){
                    // not edc mandiri
                    !dd("Bank In vs Sales not match z");
                    $generate = false;
                } else {
                    // edc mandiri
                    if( $selisihPercent > 5 ){
                        !dd("Bank In vs Sales not match");
                        $generate = false;
                    } else {
                        $totalBankCharge += $selisih;
                    }
                }
            }

            $documentNumber = '';
            $shortNamePlant = strtoupper(Plant::getShortNameById($massClearingDetail->plant_id, false));
            $bankInDate = Helper::DateConvertFormat($massClearingDetail->bank_in_date, 'Y-m-d', 'Ymd');
            $bankInMonthDate = Helper::DateConvertFormat($massClearingDetail->bank_in_date, 'Y-m-d', 'n');
            $salesDateDesc = '';
            if( sizeof($salesDates) > 1 ){
                $documentNumber = $shortNamePlant . ' ' . $massClearingDetail->sales_date;
                $fromDate = Helper::DateConvertFormat($salesDates[0], 'Y-m-d', 'd/m/Y');
                $untilDate = Helper::DateConvertFormat($salesDates[sizeof($salesDate)-1], 'Y-m-d', 'd/m/Y');
                $salesDateDesc = $fromDate . '-' . $untilDate;
            } else {
                $salesDateDesc = Helper::DateConvertFormat($salesDates[0], 'Y-m-d', 'd/m/Y');
            }

            if( $generate && sizeof($salesDates) == 1 ){
                $item = 1;

                if( $massClearingDetail->total_row > 1 ){

                    $transactions = DB::table('mass_clearing_details')
                                    ->where('bank_in_bank_gl', $massClearingDetail->bank_in_bank_gl)
                                    ->where('bank_in_date', $massClearingDetail->bank_in_date)
                                    ->where('sales_date', $massClearingDetail->sales_date)
                                    ->where('sales_month', $massClearingDetail->sales_month)
                                    ->where('sales_year', $massClearingDetail->sales_year)
                                    ->where('special_gl', $massClearingDetail->special_gl)
                                    ->where('plant_id', $massClearingDetail->plant_id)
                                    ->where('mass_clearing_id', $massClearingDetail->mass_clearing_id)
                                    ->select('bank_in_nominal', 'bank_in_description')
                                    ->get();

                    foreach ($transactions as $transaction) {

                        $generateItem[] = [
                            'no' => $no,
                            'item' => $item,
                            'customer_code' => $customerCodePlant,
                            'posting_date' => $bankInDate,
                            'periode' => $bankInMonthDate,
                            'company_code' => 'RKI',
                            'special_gl' => $massClearingDetail->special_gl,
                            'document_number' => $documentNumber,
                            'currency' => 'IDR',
                            'ar_value' => $nominalPos,
                            'reference' => $shortNamePlant,
                            'header_text' => '',
                            'posting_key' => 40,
                            'gl_account' => $massClearingDetail->bank_in_bank_gl,
                            'value' => $transaction->bank_in_nominal,
                            'payment_date_bank' => $bankInDate,
                            'tax_code' => '',
                            'assigment' => $reference,
                            'text' => $reference . ' ' . $shortNamePlant . ' ' . $salesDateDesc,
                            'cost_center' => '',
                        ];

                        $resultProcess[] = [
                            'bank_in_gl' => $massClearingDetail->bank_in_bank_gl,
                            'bank_in_date' => Helper::DateConvertFormat($massClearingDetail->bank_in_date, 'Y-m-d', 'd/m/Y'),
                            'bank_in_description' => $transaction->bank_in_description,
                            'sales_date' => $massClearingDetail->sales_date,
                            'sales_month' => $massClearingDetail->sales_month,
                            'sales_year' => $massClearingDetail->sales_year,
                            'special_gl' => $massClearingDetail->special_gl,
                            'customer_code' => $customerCodePlant,
                            'bank_in_nominal' => $massClearingDetail->bank_in_nominal,
                            'bank_in_charge' => $massClearingDetail->bank_in_charge,
                            'nominal_sales' => $nominalPos,
                            'selisih' => $selisih,
                            'selisih_percent' => $selisihPercent,
                            'status_generate' => 'yes',
                            'description' => 'generated'
                        ];

                        $item++;
                    }

                } else {

                    $generateItem[] = [
                        'no' => $no,
                        'item' => $item,
                        'customer_code' => $customerCodePlant,
                        'posting_date' => $bankInDate,
                        'periode' => $bankInMonthDate,
                        'company_code' => 'RKI',
                        'special_gl' => $massClearingDetail->special_gl,
                        'document_number' => $documentNumber,
                        'currency' => 'IDR',
                        'ar_value' => $nominalPos,
                        'reference' => $shortNamePlant,
                        'header_text' => '',
                        'posting_key' => 40,
                        'gl_account' => $massClearingDetail->bank_in_bank_gl,
                        'value' => $massClearingDetail->bank_in_nominal,
                        'payment_date_bank' => $bankInDate,
                        'tax_code' => '',
                        'assigment' => $reference,
                        'text' => $reference . ' ' . $shortNamePlant . ' ' . $salesDateDesc,
                        'cost_center' => '',
                    ];

                    $transactions = DB::table('mass_clearing_details')
                                    ->where('bank_in_bank_gl', $massClearingDetail->bank_in_bank_gl)
                                    ->where('bank_in_date', $massClearingDetail->bank_in_date)
                                    ->where('sales_date', $massClearingDetail->sales_date)
                                    ->where('sales_month', $massClearingDetail->sales_month)
                                    ->where('sales_year', $massClearingDetail->sales_year)
                                    ->where('special_gl', $massClearingDetail->special_gl)
                                    ->where('plant_id', $massClearingDetail->plant_id)
                                    ->where('mass_clearing_id', $massClearingDetail->mass_clearing_id)
                                    ->select('bank_in_description')
                                    ->first();

                    $resultProcess[] = [
                        'bank_in_gl' => $massClearingDetail->bank_in_bank_gl,
                        'bank_in_date' => Helper::DateConvertFormat($massClearingDetail->bank_in_date, 'Y-m-d', 'd/m/Y'),
                        'bank_in_description' => $transactions->bank_in_description,
                        'sales_date' => $massClearingDetail->sales_date,
                        'sales_month' => $massClearingDetail->sales_month,
                        'sales_year' => $massClearingDetail->sales_year,
                        'special_gl' => $massClearingDetail->special_gl,
                        'customer_code' => $customerCodePlant,
                        'bank_in_nominal' => $massClearingDetail->bank_in_nominal,
                        'bank_in_charge' => $massClearingDetail->bank_in_charge,
                        'nominal_sales' => $nominalPos,
                        'selisih' => $selisih,
                        'selisih_percent' => $selisihPercent,
                        'status_generate' => 'yes',
                        'description' => 'generated'
                    ];
                }

                if( $totalBankCharge > 0 ){

                    $referenceCharge = strtoupper(BankChargeGl::getRefbySpecialGl($massClearingDetail->special_gl,  $massClearingDetail->bank_in_bank_gl));
                    $referenceChargeText = $referenceCharge;
                    if( $referenceCharge != 'BANK CHARGE' ){
                        $referenceChargeText = $reference;
                    }

                    $generateItem[] = [
                        'no' => $no,
                        'item' => $item + 1,
                        'customer_code' => $customerCodePlant,
                        'posting_date' => $bankInDate,
                        'periode' => $bankInMonthDate,
                        'company_code' => 'RKI',
                        'special_gl' => $massClearingDetail->special_gl,
                        'document_number' => $documentNumber,
                        'currency' => 'IDR',
                        'ar_value' => $nominalPos,
                        'reference' => $shortNamePlant,
                        'header_text' => '',
                        'posting_key' => 40,
                        'gl_account' => BankChargeGl::getGlAccountCharge( $massClearingDetail->special_gl,  $massClearingDetail->bank_in_bank_gl),
                        'value' => $totalBankCharge,
                        'payment_date_bank' => $bankInDate,
                        'tax_code' => 'A0',
                        'assigment' => $referenceCharge,
                        'text' => $referenceChargeText . ' ' . $shortNamePlant . ' ' . $salesDateDesc,
                        'cost_center' => Plant::getCostCenterById($massClearingDetail->plant_id),
                    ];
                }
            } else {

                $transactions = DB::table('mass_clearing_details')
                                ->where('bank_in_bank_gl', $massClearingDetail->bank_in_bank_gl)
                                ->where('bank_in_date', $massClearingDetail->bank_in_date)
                                ->where('sales_date', $massClearingDetail->sales_date)
                                ->where('sales_month', $massClearingDetail->sales_month)
                                ->where('sales_year', $massClearingDetail->sales_year)
                                ->where('special_gl', $massClearingDetail->special_gl)
                                ->where('plant_id', $massClearingDetail->plant_id)
                                ->where('mass_clearing_id', $massClearingDetail->mass_clearing_id)
                                ->select('bank_in_description')
                                ->first();

                $resultProcess[] = [
                    'bank_in_gl' => $massClearingDetail->bank_in_bank_gl,
                    'bank_in_date' => Helper::DateConvertFormat($massClearingDetail->bank_in_date, 'Y-m-d', 'd/m/Y'),
                    'bank_in_description' => $transactions->bank_in_description,
                    'sales_date' => $massClearingDetail->sales_date,
                    'sales_month' => $massClearingDetail->sales_month,
                    'sales_year' => $massClearingDetail->sales_year,
                    'special_gl' => $massClearingDetail->special_gl,
                    'customer_code' => $customerCodePlant,
                    'bank_in_nominal' => $massClearingDetail->bank_in_nominal,
                    'bank_in_charge' => $massClearingDetail->bank_in_charge,
                    'nominal_sales' => $nominalPos,
                    'selisih' => $selisih,
                    'selisih_percent' => $selisihPercent,
                    'status_generate' => 'yes',
                    'description' => 'generated'
                ];
            }

            $no++;
        }

        // export to excel
        $path = 'mass-clearing/';
        $filename = $massClearing->user_id . Helper::generateRandomStr(10);
        $typefile = '.xlsx';
        if (Excel::store(new MassClearingExport($generateItem, $resultProcess), $path . $filename . $typefile, 'public')) {
            echo "success";
        } else {
            echo "failed";
        };
    }




}
