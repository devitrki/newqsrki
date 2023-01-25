<?php

namespace App\Http\Controllers\Inventory\Usedoil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;

use App\Library\Helper;

use App\Models\User;
use App\Models\Inventory\Usedoil\UoVendor;
use App\Models\Inventory\Usedoil\UoMutationSaldo;
use App\Models\Inventory\Usedoil\UoSaldoVendorHistory;

class UoMutationSaldoController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid')
        ];
        return view('inventory.usedoil.uo-mutation-saldo', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_mutation_saldos')
                    ->join('uo_vendors as vendor_sender', 'vendor_sender.id', 'uo_mutation_saldos.uo_vendor_id_sender')
                    ->join('uo_vendors as vendor_receiver', 'vendor_receiver.id', 'uo_mutation_saldos.uo_vendor_id_receiver')
                    ->where('uo_mutation_saldos.company_id', $userAuth->company_id_selected)
                    ->select('uo_mutation_saldos.id', 'uo_mutation_saldos.date', 'uo_mutation_saldos.nominal',
                            'uo_mutation_saldos.description', 'vendor_sender.name as vendor_sender_name',
                            'vendor_receiver.name as vendor_receiver_name');

        if($request->has('from') && $request->has('until')){
            if($request->query('from') != '' && $request->query('until') != ''){
                $query = $query->whereBetween('uo_mutation_saldos.date', [$request->query('from') . ' 00:00:00', $request->query('until') . ' 23:59:59' ]);
            }
        }

        return Datatables::of($query)
                    ->addIndexColumn()
                    ->addColumn('nominal_desc', function ($data) {
                        return Helper::convertNumberToInd($data->nominal, 'Rp ', 0);
                    })
                    ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
                        'vendor_sender' => 'required',
                        'vendor_receiver' => 'required',
                        'nominal' => 'required',
                    ]);

        $userAuth = $request->get('userAuth');

        // check saldo vendor sender enought or not
        $saldoVendorSender = UoVendor::getSaldoVendor($request->vendor_sender);
        if( $saldoVendorSender < $request->nominal){
            return response()
                ->json( Helper::resJSON( 'failed', Lang::get('Insufficient balance of vendor sender') . ', ' .
                        Lang::get('the balance amount of the sending vendor is ') . Helper::convertNumberToInd($saldoVendorSender, '', 0) )
            );
        }

        DB::beginTransaction();

        $uoMutationSaldo = new UoMutationSaldo;
        $uoMutationSaldo->company_id = $userAuth->company_id_selected;
        $uoMutationSaldo->document_number = Helper::generateDocNumber('253', 'uo_mutation_saldos', 'document_number', 11);
        $uoMutationSaldo->date = date('Y-m-d');
        $uoMutationSaldo->uo_vendor_id_sender = $request->vendor_sender;
        $uoMutationSaldo->uo_vendor_id_receiver = $request->vendor_receiver;
        $uoMutationSaldo->nominal = $request->nominal;
        $uoMutationSaldo->description = $request->description;
        $uoMutationSaldo->created_by = User::getNameById(Auth::id());
        $uoMutationSaldo->created_id = Auth::id();
        if ($uoMutationSaldo->save()) {

            // update saldo vendor sender (-)
            $saldoVendorSenderNow = UoVendor::updateSaldoVendor($request->vendor_sender, $request->nominal * -1);
            // insert to saldo histories vendor sender
            $uoSaldoVendorHistory = new UoSaldoVendorHistory;
            $uoSaldoVendorHistory->uo_vendor_id = $request->vendor_sender;
            $uoSaldoVendorHistory->date = date('Y-m-d');
            $uoSaldoVendorHistory->transaction_type = 3;
            $uoSaldoVendorHistory->transaction_id = $uoMutationSaldo->id;
            $uoSaldoVendorHistory->nominal = $request->nominal;
            $uoSaldoVendorHistory->saldo = $saldoVendorSenderNow;
            $uoSaldoVendorHistory->description = 'Mutation Saldo To ' . UoVendor::getNameVendorById($request->vendor_receiver);
            $uoSaldoVendorHistory->save();


            // update saldo vendor receiver(+)
            $saldoVendorReceiverNow = UoVendor::updateSaldoVendor($request->vendor_receiver, $request->nominal);
            // insert to saldo histories vendor receiver
            $uoSaldoVendorHistory = new UoSaldoVendorHistory;
            $uoSaldoVendorHistory->uo_vendor_id = $request->vendor_receiver;
            $uoSaldoVendorHistory->date = date('Y-m-d');
            $uoSaldoVendorHistory->transaction_type = 2;
            $uoSaldoVendorHistory->transaction_id = $uoMutationSaldo->id;
            $uoSaldoVendorHistory->nominal = $request->nominal;
            $uoSaldoVendorHistory->saldo = $saldoVendorReceiverNow;
            $uoSaldoVendorHistory->description = 'Mutation Receive From ' . UoVendor::getNameVendorById($request->vendor_sender);
            $uoSaldoVendorHistory->save();

            DB::commit();
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("mutation saldo vendor")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("mutation saldo vendor")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
