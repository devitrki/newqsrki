<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use App\Library\Helper;

use App\Mail\Financeacc\NotificationPettycash;
use App\Imports\Financeacc\PettycashImport;
use App\Rules\CheckAmPlant;

use App\Models\Financeacc\Pettycash;
use App\Models\Plant;
use App\Models\User;

use App\Services\PettycashServiceSapImpl;

class PettycashController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $first_plant_id = Plant::getFirstPlantIdSelect($userAuth->company_id_selected, 'all', true);
        $first_plant_name = Plant::getShortNameById($first_plant_id);

        $dataview = [
            'first_plant_id' => $first_plant_id,
            'first_plant_name' => $first_plant_name,
            'menu_id' => $request->query('menuid')
        ];

        return view('financeacc.pettycash', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('pettycashes')
                    ->where('pettycashes.company_id', $userAuth->company_id_selected)
                    ->leftJoin('plants', 'plants.id', '=', 'pettycashes.plant_id')
                    ->select(
                        'pettycashes.id',
                        'pettycashes.submit',
                        'pettycashes.approve',
                        'pettycashes.plant_id',
                        'pettycashes.document_number',
                        'pettycashes.document_po',
                        'pettycashes.description',
                        'pettycashes.voucher_number',
                        'pettycashes.transaction_date',
                        'pettycashes.type',
                        'pettycashes.pic',
                        'pettycashes.debit',
                        'pettycashes.kredit',
                        'pettycashes.saldo',
                        'plants.initital',
                        'plants.short_name',
                        'pettycashes.gl_code',
                        'pettycashes.gl_desc',
                        'pettycashes.description_reject',
                        'pettycashes.transaction_id',
                        'pettycashes.receive_pic',
                        'pettycashes.created_at',
                    )
                    ->orderBy('pettycashes.transaction_date')
                    ->orderBy('pettycashes.id');

        if ($request->has('plant_id')) {
            if( $request->query('plant_id') != 0 ){
                $query = $query->where('pettycashes.plant_id', $request->query('plant_id'));
            }
        }

        if ( $request->has('from-date') && $request->has('until-date') ) {
            $query = $query->whereBetween('pettycashes.transaction_date', [$request->query('from-date'), $request->query('until-date')]);
        }

        if ($request->has('transaction-type')) {
            if( $request->query('transaction-type') != 3 ){
                $query = $query->where('pettycashes.type', $request->query('transaction-type'));
            }
        }

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('submit_desc', function ($data) {
                    if ($data->submit == 0) {
                        return '<i class="bx bx-square text-default"></i>';
                    } if ($data->submit == 1) {
                        return '<i class="bx bx-check-square text-success"></i>';
                    } else {
                        return '<i class="bx bxs-x-square text-danger"></i>';
                    }
                })
                ->addColumn('approve_desc', function ($data) {
                    if ($data->approve == 0) {
                        return '<i class="bx bx-square text-default"></i>';
                    } if ($data->approve == 1) {
                        return '<i class="bx bx-check-square text-success"></i>';
                    } else {
                        return '<i class="bx bxs-x-square text-danger"></i>';
                    }
                })
                ->addColumn('date_desc', function ($data) {
                    return date("d-m-Y", strtotime($data->transaction_date));
                })
                ->addColumn('plant', function ($data) {
                    return $data->initital . ' ' . $data->short_name;
                })
                ->addColumn('type_desc', function ($data) {
                    if( $data->type == '1' ){
                        return 'Debit';
                    } else if( $data->type == '0' ) {
                        return 'Credit';
                    } else{
                        return 'Credit By PO';
                    }
                })
                ->addColumn('debit_desc', function ($data) {
                    return Helper::convertNumberToInd($data->debit, '', 2);
                })
                ->addColumn('kredit_desc', function ($data) {
                    return Helper::convertNumberToInd($data->kredit, '', 2);
                })
                ->addColumn('saldo_desc', function ($data) {
                    return Helper::convertNumberToInd($data->saldo, '', 2);
                })
                ->addColumn('created_at_desc', function ($data) {
                    return date("d-m-Y H:i:s", strtotime($data->created_at));
                })
                ->rawColumns(['submit_desc', 'approve_desc'])
                ->make();
    }

    public function dtblePreview(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('pettycashes')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(
                        'pettycashes.pic',
                        'pettycashes.debit',
                        'pettycashes.kredit',
                        'pettycashes.gl_code',
                        'pettycashes.gl_desc',
                        'pettycashes.voucher_number',
                        'pettycashes.description',
                        'pettycashes.approved_at',
                        'pettycashes.unapproved_at',
                        'pettycashes.submited_at',
                        'pettycashes.rejected_at',
                    )
                    ->orderByDesc('pettycashes.id');

        if ($request->has('transaction-id')) {
            $query = $query->where('pettycashes.transaction_id', $request->query('transaction-id'));
        }

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('approved_at_desc', function ($data) {
                    return ( is_null($data->approved_at) ) ? '-' : date("d-m-Y H:i:s", strtotime($data->approved_at));
                })
                ->addColumn('unapproved_at_desc', function ($data) {
                    return ( is_null($data->unapproved_at) ) ? '-' : date("d-m-Y H:i:s", strtotime($data->unapproved_at));
                })
                ->addColumn('submited_at_desc', function ($data) {
                    return ( is_null($data->submited_at) ) ? '-' : date("d-m-Y H:i:s", strtotime($data->submited_at));
                })
                ->addColumn('rejected_at_desc', function ($data) {
                    return ( is_null($data->rejected_at) ) ? '-' : date("d-m-Y H:i:s", strtotime($data->rejected_at));
                })
                ->make();
    }

    public function store(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $request->validate([
                        'plant' => ['required', new CheckAmPlant($userAuth->company_id_selected)],
                        'date' => 'required',
                        'type' => 'required'
                    ]);

        // validate date transaction
        if (strtotime($request->date) > strtotime(date('Y-m-d'))) {
            $stat = 'failed';
            $msg = Lang::get("Transaction date cannot be more than today's");
            return response()->json( Helper::resJSON( $stat, $msg ) );
        }

        $lastDateCount = DB::table('pettycashes')
                            ->where('plant_id', $request->plant)
                            ->where('transaction_date', '>', $request->date)
                            ->count('id');

        if( $lastDateCount > 0 ){
            $stat = 'failed';
            $msg = Lang::get("Transaction date cannot be less than last transaction");
            return response()->json( Helper::resJSON( $stat, $msg ) );
        }

        if( $request->type == '0' ){
            // kredit
            $desc    = 'KK';
            $approve = 0;
            $submit  = 0;
        } else if ( $request->type == '1' ) {
            // debit
            $desc    = 'KD';
            $approve = 1;
            $submit  = 1;
        } else {
            // kredit po
            $desc    = 'KP';
            $approve = 0;
            $submit  = 0;
        }

        // get saldo last plant
        $lastTransaction = DB::table('pettycashes')
                            ->where('plant_id', $request->plant)
                            ->orderByDesc('id')
                            ->first('saldo');

        $lastSaldo = 0;

        if( isset($lastTransaction->saldo) ){
            $lastSaldo = floatVal($lastTransaction->saldo);
        }

        // get id transaction new
        $transactionIdNew = 0;
        $unique = false;
        while (!$unique) {

            $transactionIdLast = DB::table('pettycashes')
                                  ->max('transaction_id');

            $transactionIdNew = $transactionIdLast + 1;

            $countTransactionCheck = DB::table('pettycashes')
                                        ->where('transaction_id', $transactionIdNew)
                                        ->count();

            if ($countTransactionCheck <= 0) {
                $unique = true;
            }
        }

        // get total transaction per plant per type transaction
        $countTransactionPlantType = DB::table('pettycashes')
                                        ->where('plant_id', $request->plant)
                                        ->where('type', $request->type)
                                        ->distinct('transaction_id')
                                        ->count('transaction_id');

        $countTransactionPlantType = $countTransactionPlantType + 1;

        // initial data
        $orderNumber = 1;
        $totalKredit = 0;
        $totalDebit = 0;

        $plantShortName = Plant::getShortNameById($request->plant);
        $tglInd = Helper::DateConvertFormat( $request->date, 'Y/m/d', 'd-m-Y' );

        $kredit = json_decode($request->kredit, true);
        $debit = json_decode($request->debit, true);
        $pic = json_decode($request->pic, true);
        $voucher = json_decode($request->voucher, true);
        $description = json_decode($request->description, true);
        $glCode = json_decode($request->gl_code, true);
        $glDesc = json_decode($request->gl_desc, true);

        $insertItems = [];
        $type = $request->type;

        $lastItemId = 0;

        DB::beginTransaction();

        for ($i=0; $i < sizeof($glCode); $i++) {

            $totalKredit += floatVal($kredit[$i]);
            $totalDebit  += floatVal($debit[$i]);

            if (  in_array( $glCode[$i], ['21212000', '21217000', '0'] ) ){
                // debit
                $type = 1;
            } else if ( $glCode[$i] == 1) {
                // kredit by po
                $type = 2;
            } else {
                // kredit
                $type = 0;
            }

            $voucherDesc = ( $voucher[$i] != '' ) ? ' ' . $voucher[$i] : '';

            $pettycash = new Pettycash;
            $pettycash->company_id = $userAuth->company_id_selected;
            $pettycash->transaction_id = $transactionIdNew;
            $pettycash->order_number = $orderNumber;
            $pettycash->type_id = $desc . '-' . $countTransactionPlantType;
            $pettycash->type = $type;
            $pettycash->transaction_date = $request->date;
            $pettycash->pic = $pic[$i];
            $pettycash->voucher_number = $voucher[$i];
            $pettycash->plant_id = $request->plant;
            $pettycash->debit = ($debit[$i] != '') ? $debit[$i] : 0;
            $pettycash->kredit = ($kredit[$i] != '') ? $kredit[$i] : 0;
            $pettycash->saldo = $lastSaldo + ($totalDebit - $totalKredit);
            $pettycash->remark = $plantShortName . $voucherDesc . ' (' . $tglInd . ')';
            $pettycash->description = $description[$i];
            $pettycash->gl_code = $glCode[$i];
            $pettycash->gl_desc = $glDesc[$i];
            $pettycash->approve = $approve;
            $pettycash->submit = $submit;
            $pettycash->save();

            $lastItemId = $pettycash->id;

            $orderNumber += 1;
        }

        DB::commit();

        // send mail to am
        // Mail::queue(new NotificationPettycash('am_approve', $lastItemId));

        $stat = 'success';
        $msg = Lang::get("message.save.success", ["data" => Lang::get("petty cash")]);

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function storeMultiple(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file'
        ]);

        $stat = 'success';
        $msg = Lang::get("message.save.success", ["data" => Lang::get("Petty Cash")]);

        $userAuth = $request->get('userAuth');

        if ($request->file('file_excel')) {
            try {
                $import = new PettycashImport($userAuth->company_id_selected);
                Excel::import($import, request()->file('file_excel'));
                $return = $import->return;

                $stat = $return['status'];
                $msg = ($return['message'] != '') ? $return['message'] : $msg;

            } catch (\Throwable $th) {
                $msg = Lang::get("File excel not valid. Please download the valid file.");
            }
        }

        return response()->json(Helper::resJSON($stat, $msg));

    }

    public function edit(Request $request)
    {
        $request->validate([
            'no_voucher' => 'required',
            'description' => 'required',
        ]);

        $pettycash = Pettycash::find($request->id);
        $pettycash->voucher_number = $request->no_voucher;
        $pettycash->description = $request->description;
        if ($pettycash->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("petty cash")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("petty cash")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );

    }

    public function editNoPo(Request $request)
    {
        $request->validate([
            'no_po' => 'required|max:15',
        ]);

        $pettycash = Pettycash::find($request->id);
        $pettycash->document_po = $request->no_po;
        if ($pettycash->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("no po petty cash")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("no po petty cash")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );

    }

    public function approve($id)
    {
        $pettycash = Pettycash::find($id);
        $pettycash->approve = 1;
        $pettycash->approved_at = date('Y-m-d H:i:s');
        if ($pettycash->save()) {

            // send mail to outlet that transaction approved
            $plant = Plant::getDataPlantById($pettycash->plant_id);
            if( isset($plant->email) ){
                // Mail::queue(new NotificationPettycash('am_approved', $id));
            } else {
                Log::alert($plant->initital . ' ' . $plant->short_name . ' email empty, please mapping.');
            }

            $stat = 'success';
            $msg = Lang::get("message.approve.success", ["data" => Lang::get("petty cash")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.approve.failed", ["data" => Lang::get("petty cash")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );

    }

    public function downloadTemplate()
    {
        return response()->download( public_path('template-create-petty-cash.xlsx') );
    }

    public function unapprove(Request $request)
    {
        $request->validate([
            'description_unapprove' => 'required',
        ]);

        // get pettycash reject
        $pettycashReject = DB::table('pettycashes')
                            ->where('id', $request->id)
                            ->first();

        // create jurnal balik
        // get saldo last plant
        $lastTransaction = DB::table('pettycashes')
                            ->where('plant_id', $pettycashReject->plant_id)
                            ->orderByDesc('id')
                            ->first('saldo');

        $lastSaldo = 0;

        if( isset($lastTransaction->saldo) ){
            $lastSaldo = floatVal($lastTransaction->saldo);
        }

        // get id transaction new
        $transactionIdNew = 0;
        $unique = false;
        while (!$unique) {

            $transactionIdLast = DB::table('pettycashes')
                                  ->max('transaction_id');

            $transactionIdNew = $transactionIdLast + 1;

            $countTransactionCheck = DB::table('pettycashes')
                                        ->where('transaction_id', $transactionIdNew)
                                        ->count();

            if ($countTransactionCheck <= 0) {
                $unique = true;
            }
        }

        $type = '';
        $saldoBalik = 0;
        $approve = 1;
        $submit  = 1;

        if( $pettycashReject->type == '0' ){
            // kredit -> debit
            $desc    = 'KD';
            $type = '1';
            $saldoBalik = $lastSaldo + $pettycashReject->kredit;

        } else if ( $pettycashReject->type == '1' ) {
            // debit -> kredit
            $desc    = 'KK';
            $type = '0';
            $saldoBalik = $lastSaldo - $pettycashReject->debit;

        } else {
            // kredit po -> debit po
            $desc    = 'KP';
            $type = $pettycashReject->type;
            $saldoBalik = $lastSaldo + $pettycashReject->kredit;
        }

        // get total transaction per plant per type transaction
        $countTransactionPlantType = DB::table('pettycashes')
                                        ->where('plant_id', $pettycashReject->plant_id)
                                        ->where('type', $type)
                                        ->distinct('transaction_id')
                                        ->count('transaction_id');

        $countTransactionPlantType = $countTransactionPlantType + 1;

        // insert jurnal balik
        $pettycashInsert = new Pettycash;
        $pettycashInsert->company_id = $pettycashReject->company_id;
        $pettycashInsert->transaction_id = $transactionIdNew;
        $pettycashInsert->order_number = 1;
        $pettycashInsert->type_id = $desc . '-' . $countTransactionPlantType;
        $pettycashInsert->type = $type;
        $pettycashInsert->transaction_date = $pettycashReject->transaction_date;
        $pettycashInsert->pic = $pettycashReject->pic;
        $pettycashInsert->voucher_number = $pettycashReject->voucher_number;
        $pettycashInsert->plant_id = $pettycashReject->plant_id;
        $pettycashInsert->debit = $pettycashReject->kredit;
        $pettycashInsert->kredit = $pettycashReject->debit;
        $pettycashInsert->saldo = $saldoBalik;
        $pettycashInsert->remark = $pettycashReject->remark;
        $pettycashInsert->description = 'Ref. Trans ' . $pettycashReject->id . ' No.Vouc ' . $pettycashReject->voucher_number;
        $pettycashInsert->gl_code = $pettycashReject->gl_code;
        $pettycashInsert->gl_desc = $pettycashReject->gl_desc;
        $pettycashInsert->approve = $approve;
        $pettycashInsert->approved_at = date('Y-m-d H:i:s');
        $pettycashInsert->submit = $submit;
        $pettycashInsert->submited_at = date('Y-m-d H:i:s');
        $pettycashInsert->save();

        // update transaction data un approve
        $am_plant = Plant::getDataAMPlantById($pettycashReject->plant_id);

        $pettycash = Pettycash::find($request->id);
        $pettycash->approve = 2;
        $pettycash->submit = 2;
        $pettycash->unapproved_at = date('Y-m-d H:i:s');
        $pettycash->description_reject = $request->description_unapprove . ' By AM ' . $am_plant->name;
        if ($pettycash->save()) {

            // send mail to outlet that transaction approved
            $plant = Plant::getDataPlantById($pettycash->plant_id);
            if( isset($plant->email) ){
                // Mail::queue(new NotificationPettycash('am_unapproved', $pettycash->id));
            } else {
                Log::alert($plant->initital . ' ' . $plant->short_name . ' email empty, please mapping.');
            }

            $stat = 'success';
            $msg = Lang::get("message.unapprove.success", ["data" => Lang::get("petty cash")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.unapprove.failed", ["data" => Lang::get("petty cash")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );

    }

    public function reject(Request $request)
    {
        $request->validate([
            'description_reject' => 'required',
        ]);

        // get pettycash reject
        $pettycashReject = DB::table('pettycashes')
                            ->where('id', $request->id)
                            ->first();

        // create jurnal balik
        // get saldo last plant
        $lastTransaction = DB::table('pettycashes')
                            ->where('plant_id', $pettycashReject->plant_id)
                            ->orderByDesc('id')
                            ->first('saldo');

        $lastSaldo = 0;

        if( isset($lastTransaction->saldo) ){
            $lastSaldo = floatVal($lastTransaction->saldo);
        }

        // get id transaction new
        $transactionIdNew = 0;
        $unique = false;
        while (!$unique) {

            $transactionIdLast = DB::table('pettycashes')
                                  ->max('transaction_id');

            $transactionIdNew = $transactionIdLast + 1;

            $countTransactionCheck = DB::table('pettycashes')
                                        ->where('transaction_id', $transactionIdNew)
                                        ->count();

            if ($countTransactionCheck <= 0) {
                $unique = true;
            }
        }

        $type = '';
        $saldoBalik = 0;
        $approve = 1;
        $submit  = 1;

        if( $pettycashReject->type == '0' ){
            // kredit -> debit
            $desc    = 'KD';
            $type = '1';
            $saldoBalik = $lastSaldo + $pettycashReject->kredit;

        } else if ( $pettycashReject->type == '1' ) {
            // debit -> kredit
            $desc    = 'KK';
            $type = '0';
            $saldoBalik = $lastSaldo - $pettycashReject->debit;

        } else {
            // kredit po -> debit po
            $desc    = 'KP';
            $type = $pettycashReject->type;
            $saldoBalik = $lastSaldo + $pettycashReject->kredit;
        }

        // get total transaction per plant per type transaction
        $countTransactionPlantType = DB::table('pettycashes')
                                        ->where('plant_id', $pettycashReject->plant_id)
                                        ->where('type', $type)
                                        ->distinct('transaction_id')
                                        ->count('transaction_id');

        $countTransactionPlantType = $countTransactionPlantType + 1;

        // insert jurnal balik
        $pettycashInsert = new Pettycash;
        $pettycashInsert->company_id = $pettycashReject->company_id;
        $pettycashInsert->transaction_id = $transactionIdNew;
        $pettycashInsert->order_number = 1;
        $pettycashInsert->type_id = $desc . '-' . $countTransactionPlantType;
        $pettycashInsert->type = $type;
        $pettycashInsert->transaction_date = $pettycashReject->transaction_date;
        $pettycashInsert->pic = $pettycashReject->pic;
        $pettycashInsert->voucher_number = $pettycashReject->voucher_number;
        $pettycashInsert->plant_id = $pettycashReject->plant_id;
        $pettycashInsert->debit = $pettycashReject->kredit;
        $pettycashInsert->kredit = $pettycashReject->debit;
        $pettycashInsert->saldo = $saldoBalik;
        $pettycashInsert->remark = $pettycashReject->remark;
        $pettycashInsert->description = 'Ref. Trans ' . $pettycashReject->id . ' No.Vouc ' . $pettycashReject->voucher_number;
        $pettycashInsert->gl_code = $pettycashReject->gl_code;
        $pettycashInsert->gl_desc = $pettycashReject->gl_desc;
        $pettycashInsert->approve = $approve;
        $pettycashInsert->approved_at = date('Y-m-d H:i:s');
        $pettycashInsert->submit = $submit;
        $pettycashInsert->submited_at = date('Y-m-d H:i:s');
        $pettycashInsert->save();

        // update transaction data un approve
        $userName = User::getNameById( Auth::id() );

        $pettycash = Pettycash::find($request->id);
        $pettycash->approve = 2;
        $pettycash->submit = 2;
        $pettycash->rejected_at = date('Y-m-d H:i:s');
        $pettycash->description_reject = $request->description_reject . ' By FA ' . $userName;
        if ($pettycash->save()) {

            // send mail to outlet that transaction approved
            $plant = Plant::getDataPlantById($pettycash->plant_id);
            if( isset($plant->email) ){
                // Mail::queue(new NotificationPettycash('fa_rejected', $pettycash->id, $userName));
            } else {
                Log::alert($plant->initital . ' ' . $plant->short_name . ' email empty, please mapping.');
            }

            $stat = 'success';
            $msg = Lang::get("message.reject.success", ["data" => Lang::get("petty cash")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.reject.failed", ["data" => Lang::get("petty cash")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );

    }

    public function submit(Request $request)
    {
        $request->validate([
            'pic_fa' => 'required',
            'receive_date' => 'required',
        ]);

        $stat = 'success';
        $msg = Lang::get('Successfully Posted');

        $userAuth = $request->get('userAuth');
        $idSubmiteds = json_decode($request->id_submited, true);

        $pettycashService = new PettycashServiceSapImpl();
        $response = $pettycashService->uploadPettyCash($userAuth->company_id_selected, $request->pic_fa, $request->receive_date, $idSubmiteds);
        if (!$response['status']) {
            $stat = $response['status'];
            $msg = $response['message'];
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    // migration tools
    public function migration(Request $request)
    {
        $from = $request->query('from') . " 00:00:00";
        $until = $request->query('until') . " 23:59:59";

        $pettyApps = DB::connection('apps')
                    ->table('rf_petty')
                    ->whereBetween('trans_date', [$from, $until])
                    ->get();

        DB::beginTransaction();

        $insertQsr = [];
        $countInsert = 0;
        foreach ($pettyApps as $dataPettyApps) {

            $plantId = Plant::getIdByCode($dataPettyApps->plant);

            $countInQsr = DB::table('pettycashes')
                            ->where('id', $dataPettyApps->id_item)
                            ->count('id');

            if($countInQsr > 0){
                continue;
            }

            $insertQsr[] = [
                'id' => $dataPettyApps->id_item,
                'transaction_id' => $dataPettyApps->id_trans,
                'document_number' => $dataPettyApps->doc_number,
                'document_po' => $dataPettyApps->no_po,
                'type_id' => $dataPettyApps->type_id,
                'type' => $dataPettyApps->type,
                'transaction_date' => $dataPettyApps->trans_date,
                'pic' => ($dataPettyApps->pic != null) ? $dataPettyApps->pic : '',
                'voucher_number' => $dataPettyApps->voucher_no,
                'plant_id' => $plantId,
                'debit' => ($dataPettyApps->debit != null) ? $dataPettyApps->debit : 0,
                'kredit' => ($dataPettyApps->kredit != null) ? $dataPettyApps->kredit : 0,
                'saldo' => $dataPettyApps->saldo,
                'remark' => $dataPettyApps->description,
                'description' => $dataPettyApps->remark,
                'gl_code' => $dataPettyApps->gl_code,
                'gl_desc' => $dataPettyApps->gl_desc,
                'approve' => $dataPettyApps->approve,
                'submit' => $dataPettyApps->submit,
                'order_number' => $dataPettyApps->no,
                'receive_pic' => $dataPettyApps->receive_pic,
                'receive_date' => $dataPettyApps->receive_date,
                'description_reject' => $dataPettyApps->desc_reject,
                'created_at' => $dataPettyApps->created_at,
                'updated_at' => $dataPettyApps->created_at,
            ];

            $countInsert++;

        }

        DB::table('pettycashes')->insert($insertQsr);

        DB::commit();

        !dd([
            $countInsert
        ]);
    }

    public function migrationCheck(Request $request)
    {
        $plants = DB::table('plants')->select('id', 'code', 'short_name')->get();

        foreach ($plants as $plant) {

            echo $plant->short_name . ' ';

            $qSaldoInApps = DB::connection('apps')
                            ->table('rf_petty')
                            ->where('plant', $plant->code)
                            ->orderByDesc('id_item')
                            ->select('id_item', 'debit', 'kredit', 'saldo')
                            ->limit(1);

            $idApps = 0;
            $debitApps = 0;
            $kreditApps = 0;
            $saldoApps = 0;

            if($qSaldoInApps->count() > 0){
                $saldoInApps = $qSaldoInApps->first();
                $idApps = $saldoInApps->id_item;
                $debitApps = $saldoInApps->debit;
                $kreditApps = $saldoInApps->kredit;
                $saldoApps = $saldoInApps->saldo;
            }

            echo "APPS : " . $idApps . ' | '  . $debitApps . ' | ' . $kreditApps . ' | ' . $saldoApps;

            $qSaldoInQsr = DB::table('pettycashes')
                            ->where('plant_id', $plant->id)
                            ->orderByDesc('id')
                            ->select('id', 'debit', 'kredit', 'saldo')
                            ->limit(1);

            $idQsr = 0;
            $debitQsr = 0;
            $kreditQsr = 0;
            $saldoQsr = 0;

            if($qSaldoInQsr->count() > 0){
                $saldoInQsr = $qSaldoInQsr->first();
                $idQsr = $saldoInQsr->id;
                $debitQsr = $saldoInQsr->debit;
                $kreditQsr = $saldoInQsr->kredit;
                $saldoQsr = $saldoInQsr->saldo;
            }

            echo " QSRKI : " . $idQsr . ' | '  . $debitQsr . ' | ' . $kreditQsr . ' | ' . $saldoQsr;

            // check
            $match = "TIDAK";
            if( $idApps == $idQsr && $debitApps == $debitQsr && $kreditApps == $kreditQsr && $saldoApps == $saldoQsr ){
                $match = "YA";
            }

            echo " RESULT : " . $match . "</br>";
        }


    }

    public function fixSaldo(Request $request){
        $plant = $request->query('plant');
        $from = $request->query('from');
        $until = $request->query('until');

        if(!$plant) { !dd('plant must have already in param'); }

        $plantId = Plant::getIdByCode($plant);

        $pettycashes = DB::table('pettycashes')
                        ->whereBetween('transaction_date',  [$from, $until])
                        ->where('plant_id', $plantId)
                        ->orderBy('id')
                        ->get();

        $countDataPetty = sizeof($pettycashes);

        $saldo = ($countDataPetty > 0) ? $pettycashes[0]->saldo : 0;

        for ($i=1; $i < $countDataPetty; $i++) {
            $nominalDebit = $pettycashes[$i]->debit;
            $nominalKredit = $pettycashes[$i]->kredit;

            $saldo = $this->calculateNewSaldo($nominalDebit, $nominalKredit, $saldo);

            $this->updateSaldo($pettycashes[$i]->id, $saldo);
            $this->printTransaction($pettycashes[$i], $saldo);
        }

        !dd("fix saldo done. last saldo : " . Helper::convertNumberToInd($saldo, '', 0));
    }

    public function calculateNewSaldo($debit, $kredit, $saldoOld){

        if( $debit > 0 && $kredit > 0 ){
            $saldo = 0;
        } else if ( $debit > 0 ){
            $saldo = $saldoOld + $debit;
        } else {
            $saldo = $saldoOld - $kredit;
        }

        return $saldo;
    }

    public function updateSaldo($pettycashId, $saldoNew){

        DB::table('pettycashes')
            ->where('id', $pettycashId)
            ->update([
                'saldo' => $saldoNew
            ]);

    }

    public function printTransaction($pettycash, $saldoNew){
        echo "trans id : " . $pettycash->id .
            " debit : " . Helper::convertNumberToInd($pettycash->debit, '', 0) .
            " kredit : " . Helper::convertNumberToInd($pettycash->kredit, '', 0) .
            " saldo : " . Helper::convertNumberToInd($saldoNew, '', 0) .
            "<br>";
    }

}
