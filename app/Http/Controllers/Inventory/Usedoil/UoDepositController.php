<?php

namespace App\Http\Controllers\Inventory\Usedoil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\DataTables;
use App\Library\Helper;
use Intervention\Image\ImageManager as Image;

use App\Mail\Inventory\Usedoil\NotificationUoDeposit;

use App\Models\Configuration;
use App\Models\User;
use App\Models\Inventory\Usedoil\UoDeposit;
use App\Models\Inventory\Usedoil\UoVendor;
use App\Models\Inventory\Usedoil\UoSaldoVendorHistory;

class UoDepositController extends Controller
{
    public function index(Request $request){
        $userAuth = $request->get('userAuth');

        $bankRicheese = Configuration::getValueCompByKeyFor($userAuth->company_id_selected, 'inventory', 'uo_bank_richeese');
        $bankRicheese = explode(',', $bankRicheese);

        $dataview = [
            'menu_id' => $request->query('menuid'),
            'bank_richeese' => $bankRicheese
        ];

        return view('inventory.usedoil.uo-deposit', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('uo_deposits')
                    ->join('uo_vendors', 'uo_vendors.id', 'uo_deposits.uo_vendor_id')
                    ->where('uo_deposits.company_id', $userAuth->company_id_selected)
                    ->select(['uo_deposits.id', 'uo_deposits.uo_vendor_id', 'uo_deposits.document_number', 'uo_deposits.deposit_date',
                    'uo_deposits.richeese_bank', 'uo_deposits.type_deposit', 'uo_deposits.transfer_bank_account',
                    'uo_deposits.transfer_bank_account_name', 'uo_deposits.deposit_nominal', 'uo_deposits.submit', 'uo_deposits.confirmation_fa',
                    'uo_deposits.transfer_bank', 'uo_deposits.created_by', 'uo_deposits.reject_description','uo_vendors.name as vendor_name',
                    'uo_deposits.image']);

        if($request->has('vendor-id') && $request->query('vendor-id') != '0'){
            if($request->query('vendor-id') != ''){
                $query = $query->where('uo_deposits.uo_vendor_id', $request->query('vendor-id'));
            }
        }

        if($request->has('from') && $request->has('until')){
            if($request->query('from') != '' && $request->query('until') != ''){
                $query = $query->whereBetween('uo_deposits.deposit_date', [$request->query('from') . ' 00:00:00', $request->query('until') . ' 23:59:59' ]);
            }
        }

        $user = User::find(Auth::id());

        if(!$user->hasRole('superadmin') && $user->hasRole('finance staff')){
            $query = $query->where('uo_deposits.submit', 1)
                            ->where('uo_deposits.confirmation_fa', '<>', 2);
        }

        $query = $query->orderByDesc('uo_deposits.deposit_date');
        $query = $query->orderByDesc('uo_deposits.id');

        return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('image_desc', function ($data) {
                    return '<a href="' . '/storage/usedoil/transfer/' . $data->image . '" target="blank">Show Image Transfer</a>';
                })
                ->addColumn('submit_desc', function ($data) {
                    if ($data->submit != 0) {
                        return '<i class="bx bxs-check-circle text-success"></i>';
                    } else {
                        return '<i class="bx bx-time-five text-default"></i>';
                    }
                })
                ->addColumn('confirmation_fa_desc', function ($data) {
                    if ($data->confirmation_fa == 0) {
                        return '<i class="bx bx-time-five text-default"></i>';
                    } else if( $data->confirmation_fa == 1 ){
                        return '<i class="bx bxs-check-circle text-success"></i>';
                    } else {
                        return '<i class="bx bxs-x-circle text-danger"></i>';
                    }
                })
                ->addColumn('deposit_nominal_desc', function ($data) {
                    return Helper::convertNumberToInd($data->deposit_nominal, 'Rp ', 0);
                })
                ->addColumn('type_deposit_desc', function ($data) {
                    if( $data->type_deposit != '1'){
                        return Lang::get('Bank Transfer');
                    } else {
                        return Lang::get('Deposit Cash');
                    }
                })
                ->filterColumn('vendor_name', function($query, $keyword) {
                    $query->whereRaw("LOWER(uo_vendors.name) like '%" . strtolower($keyword) . "%'");
                })
                ->rawColumns(['submit_desc', 'confirmation_fa_desc', 'image_desc'])
                ->make();
    }

    public function select(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('companies')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', 'name as text']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        if ($request->query('init') == 'false' && !$request->has('search')) {
            $data = [];
        } else {
            $data = $query->get();
        }

        if ($request->has('ext')) {
            if ($request->query('ext') == 'all') {
                if (!is_array($data)) {
                    $data->prepend(['id' => 0, 'text' => Lang::get('All')]);
                }
            }
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validate = [
            'vendor' => 'required',
            'deposit_date' => 'required',
            'deposit_type' => 'required',
            'richeese_bank' => 'required',
            'nominal' => 'required',
            'image_transfer' => 'required|image|mimes:jpeg,png,jpg|max:1024',
        ];

        $userAuth = $request->get('userAuth');

        if($request->deposit_type != '1'){
            $validate['bank'] = 'required';
            $validate['bank_account_number'] = 'required';
            $validate['bank_account_under_the_name'] = 'required';
        }

        $request->validate($validate);

        $uoDeposit = new UoDeposit;
        $uoDeposit->company_id = $userAuth->company_id_selected;
        $uoDeposit->uo_vendor_id = $request->vendor;
        $uoDeposit->document_number = Helper::generateDocNumber('215', 'uo_deposits', 'document_number', 11);
        $uoDeposit->deposit_date = $request->deposit_date;
        $uoDeposit->richeese_bank = $request->richeese_bank;
        $uoDeposit->type_deposit = $request->deposit_type;
        $uoDeposit->transfer_bank = $request->bank;
        $uoDeposit->transfer_bank_account = $request->bank_account_number;
        $uoDeposit->transfer_bank_account_name = $request->bank_account_under_the_name;
        $uoDeposit->deposit_nominal = $request->nominal;
        $uoDeposit->created_by = User::getNameById(Auth::id());
        $uoDeposit->created_id = Auth::id();

        if ($request->file('image_transfer')) {

            if (!is_dir(storage_path('app/public/usedoil/transfer/'))) {
                Storage::makeDirectory('public/usedoil/transfer/', 0777, true, true);
            }

            $filename = 'ut' . date('mdYHis') . uniqid();
            $filetype = '.jpg';
            $image = new Image();
            $image_transfer = $request->file('image_transfer');
            $image = $image->make($image_transfer)
                        ->encode('jpg', 100)
                        ->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })
                        ->orientate()
                        ->save(storage_path('app/public/usedoil/transfer/' . $filename . $filetype));

            $uoDeposit->image = $filename . $filetype;
        }

        if ($uoDeposit->save()) {
            $stat = 'success';
            $msg = Lang::get("message.save.success", ["data" => Lang::get("vendor deposit")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("vendor deposit")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $validate = [
            'vendor' => 'required',
            'deposit_date' => 'required',
            'deposit_type' => 'required',
            'richeese_bank' => 'required',
            'nominal' => 'required',
        ];

        if($request->deposit_type != '1'){
            $validate['bank'] = 'required';
            $validate['bank_account_number'] = 'required';
            $validate['bank_account_under_the_name'] = 'required';
        }

        $request->validate($validate);

        $uoDeposit = UoDeposit::find($request->id);
        $uoDeposit->uo_vendor_id = $request->vendor;
        $uoDeposit->deposit_date = $request->deposit_date;
        $uoDeposit->richeese_bank = $request->richeese_bank;
        $uoDeposit->type_deposit = $request->deposit_type;
        $uoDeposit->transfer_bank = $request->bank;
        $uoDeposit->transfer_bank_account = $request->bank_account_number;
        $uoDeposit->transfer_bank_account_name = $request->bank_account_under_the_name;
        $uoDeposit->deposit_nominal = $request->nominal;

        if ($request->file('image_transfer')) {

            // delete file old
            Storage::disk('public')->delete('usedoil/transfer/' . $uoDeposit->image);

            if (!is_dir(storage_path('app/public/usedoil/transfer/'))) {
                Storage::makeDirectory('public/usedoil/transfer/', 0777, true, true);
            }

            $filename = 'ut' . date('mdYHis') . uniqid();
            $filetype = '.jpg';
            $image = new Image();
            $image_transfer = $request->file('image_transfer');
            $image = $image->make($image_transfer)
                        ->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })
                        ->orientate()
                        ->save(storage_path('app/public/usedoil/transfer/' . $filename . $filetype));

            $uoDeposit->image = $filename . $filetype;
        }

        if ($uoDeposit->save()) {
            $stat = 'success';
            $msg = Lang::get("message.update.success", ["data" => Lang::get("vendor deposit")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("vendor deposit")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function submit($id)
    {
        DB::beginTransaction();
        $uoDeposit = UoDeposit::find($id);
        $uoDeposit->submit = 1;
        $uoDeposit->confirmation_fa = 0;
        $uoDeposit->reject_description = '';
        if ($uoDeposit->save()) {

            Mail::queue(new NotificationUoDeposit($uoDeposit->id, 'submit'));

            DB::commit();

            $stat = 'success';
            $msg = Lang::get("message.submit.success", ["data" => Lang::get("vendor deposit")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.submit.failed", ["data" => Lang::get("vendor deposit")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function approve($id)
    {
        DB::beginTransaction();
        $uoDeposit = UoDeposit::find($id);
        $uoDeposit->confirmation_fa = 1;
        if ($uoDeposit->save()) {

            // add saldo
            $saldoVendorNow = UoVendor::updateSaldoVendor($uoDeposit->uo_vendor_id, $uoDeposit->deposit_nominal);

            // insert to saldo histories
            $uoSaldoVendorHistory = new UoSaldoVendorHistory;
            $uoSaldoVendorHistory->uo_vendor_id = $uoDeposit->uo_vendor_id;
            $uoSaldoVendorHistory->date = $uoDeposit->deposit_date;
            $uoSaldoVendorHistory->transaction_type = 1;
            $uoSaldoVendorHistory->transaction_id = $uoDeposit->id;
            $uoSaldoVendorHistory->nominal = $uoDeposit->deposit_nominal;
            $uoSaldoVendorHistory->saldo = $saldoVendorNow;
            $uoSaldoVendorHistory->description = 'Deposit';
            $uoSaldoVendorHistory->save();


            Mail::queue(new NotificationUoDeposit($uoDeposit->id, 'confirm'));

            DB::commit();

            $stat = 'success';
            $msg = Lang::get("message.confirmation.success", ["data" => Lang::get("vendor deposit")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.confirmation.failed", ["data" => Lang::get("vendor deposit")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function reject(Request $request, $id)
    {
        $validate = [
            'reject_description' => 'required',
        ];

        $request->validate($validate);

        DB::beginTransaction();
        $uoDeposit = UoDeposit::find($id);
        $uoDeposit->confirmation_fa = 2;
        $uoDeposit->submit = 0;
        $uoDeposit->reject_description = $request->reject_description;
        if ($uoDeposit->save()) {

            Mail::send(new NotificationUoDeposit($uoDeposit->id, 'reject'));

            DB::commit();

            $stat = 'success';
            $msg = Lang::get("message.confirmation.success", ["data" => Lang::get("vendor deposit")]);
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.confirmation.failed", ["data" => Lang::get("vendor deposit")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        $uoDeposit = UoDeposit::find($id);
        if ($uoDeposit->delete()) {
            $stat = 'success';
            $msg = Lang::get("message.destroy.success", ["data" => Lang::get("vendor deposit")]);
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.destroy.failed", ["data" => Lang::get("vendor deposit")]);
        }
        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
