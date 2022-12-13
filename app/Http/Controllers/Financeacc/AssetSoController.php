<?php

namespace App\Http\Controllers\Financeacc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;

use App\Mail\Financeacc\Assets\NotificationSelisihAssetSo;

use App\Library\Helper;

use App\Exports\Financeacc\AssetSoExport;
use App\Imports\Financeacc\AssetSoImport;

use App\Models\Configuration;
use App\Models\User;
use App\Models\Plant;
use App\Models\Financeacc\AssetSo;
use App\Models\Financeacc\AssetSoPlant;
use App\Models\Financeacc\AssetSoDetail;
use App\Models\NotificationSystemRead;

class AssetSoController extends Controller
{
    public function index(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $periodeMonthSo = Date('n');
        $periodeYearSo = Date('Y');
        $periodeMonthSoLabel = Helper::getMonthByNumberMonth($periodeMonthSo);

        $checked = true;
        $checkedMessage = '';

        // get plant user
        $plantId = Plant::getPlantIdByUserId($userAuth->company_id_selected, Auth::id());
        $plantName = Plant::getShortNameById($plantId);
        $plantType = Plant::getTypeByPlantId($plantId);

        if (is_array($plantId) || $plantId == '') {
            $checked = true;
            $checkedMessage = Lang::get("You not authorized to input asset SO");
        }

        // check role must store manager
        $roleAuthId = User::getRoleIdById(Auth::id());
        $roleIdStoreManager = Configuration::getValueByKeyFor('general_master', 'role_sm');
        if( $checked && ($roleAuthId != $roleIdStoreManager) ){
            $checked = false;
            $checkedMessage = Lang::get("You not authorized to input asset SO");
        }

        // check periode so already or not
        $checkAssetSOAlreadyGenerate = DB::table('asset_sos')
                                        ->where('company_id', $userAuth->company_id_selected)
                                        ->where('month', $periodeMonthSo)
                                        ->where('year', $periodeYearSo);

        if( $plantType != 'DC' ){
            $checkAssetSOAlreadyGenerate->where('status_generate_outlet', '1')
                                        ->where('status_submit_outlet', '0');
        } else {
            $checkAssetSOAlreadyGenerate->where('status_generate_dc', '1')
                                        ->where('status_submit_dc', '0');
        }

        if($checked){
            if ($checkAssetSOAlreadyGenerate->count() > 0) {

                $assetSo = $checkAssetSOAlreadyGenerate->first();

                // check plant already generate or not yet
                $checkAssetSoPlantGenerate = DB::table('asset_so_plants')
                                                ->where('company_id', $userAuth->company_id_selected)
                                                ->where('asset_so_id', $assetSo->id)
                                                ->where('plant_id', $plantId);

                if( $checkAssetSoPlantGenerate->count() <= 0){
                    $checked = false;
                    $checkedMessage = Lang::get("Asset SO not available");
                }

            } else {
                $checked = false;
                $checkedMessage = Lang::get("Asset SO not available");
            }
        }

        $dataview = [
            'menu_id' => $request->query('menuid'),
            'checked' => [
                'status' => $checked,
                'message' => $checkedMessage
            ],
            'periode' => [
                'month' => $periodeMonthSo,
                'year' => $periodeYearSo,
                'label_month' => $periodeMonthSoLabel
            ],
            'plant' => [
                'id' => $plantId,
                'name' => $plantName,
                'type' => $plantType
            ]
        ];

        return view('financeacc.asset-so', $dataview)->render();
    }

    public function selectCostCenter(Request $request, $plant_id)
    {
        $userAuth = $request->get('userAuth');

        $costCenters = DB::table('asset_so_plants')
                            ->select(['cost_center_code as id', 'cost_center as text'])
                            ->where('company_id', $userAuth->company_id_selected)
                            ->where('plant_id', $plant_id)
                            ->distinct()
                            ->get();

        return response()->json($costCenters);
    }

    public function selectPeriode(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $query = DB::table('asset_sos')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->select(['id', DB::raw("CONCAT(month_label, ' ', year) as text")]);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereRaw("LOWER(month_label) like '%" . strtolower($request->search) . "%'")
                    ->orWhereRaw("LOWER(year) like '%" . strtolower($request->search) . "%'");
            });
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

    public function download(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $plantID = $request->query('plant-id');
        $plantType = Plant::getTypeByPlantId($plantID);
        $costCenter = $request->query('costcenter');
        $periodeMonth = $request->query('periode-month');
        $periodeYear = $request->query('periode-year');

        $plantCode = Plant::getCodeById($plantID);
        $nameFileDownload = 'SO-' . $plantCode . '-' . $costCenter . '-'. $periodeMonth . '-' . $periodeYear . '.xlsx';

        $assetSoPlant = DB::table('asset_so_plants')
                            ->join('asset_sos', 'asset_sos.id', 'asset_so_plants.asset_so_id')
                            ->where('asset_so_plants.company_id', $userAuth->company_id_selected)
                            ->where('asset_so_plants.plant_id', $plantID)
                            ->where('asset_so_plants.cost_center_code', $costCenter)
                            ->where('asset_sos.month', $periodeMonth)
                            ->where('asset_sos.year', $periodeYear)
                            ->select('asset_so_plants.id');

        if( $plantType != 'DC' ){
            $assetSoPlant->where('asset_sos.status_generate_outlet', '1')
                        ->where('asset_sos.status_submit_outlet', '0');
        } else {
            $assetSoPlant->where('asset_sos.status_generate_dc', '1')
                        ->where('asset_sos.status_submit_dc', '0');
        }

        if($assetSoPlant->count() <= 0){
            echo "You not authorized to download asset SO";
            return false;
        }

        $dataAssetSoPlant = $assetSoPlant->first();

        return Excel::download(new AssetSoExport($dataAssetSoPlant->id), $nameFileDownload);

    }

    public function preview(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $plantID = $request->query('plant-id');
        $plantType = Plant::getTypeByPlantId($plantID);
        $costCenter = $request->query('costcenter');
        $periodeMonth = $request->query('periode-month');
        $periodeYear = $request->query('periode-year');

        $plantCode = Plant::getCodeById($plantID);

        $assetSoPlant = DB::table('asset_so_plants')
                            ->join('asset_sos', 'asset_sos.id', 'asset_so_plants.asset_so_id')
                            ->where('asset_so_plants.company_id', $userAuth->company_id_selected)
                            ->where('asset_so_plants.plant_id', $plantID)
                            ->where('asset_so_plants.cost_center_code', $costCenter)
                            ->where('asset_sos.month', $periodeMonth)
                            ->where('asset_sos.year', $periodeYear)
                            ->select('asset_so_plants.id');

        if( $plantType != 'DC' ){
            $assetSoPlant->where('asset_sos.status_generate_outlet', '1')
                        ->where('asset_sos.status_submit_outlet', '0');
        } else {
            $assetSoPlant->where('asset_sos.status_generate_dc', '1')
                        ->where('asset_sos.status_submit_dc', '0');
        }

        if($assetSoPlant->count() <= 0){
            echo "You not authorized to preview asset SO";
            return false;
        }

        $dataAssetSoPlant = $assetSoPlant->first();

        $assetSoPlant = DB::table('asset_so_plants')
                            ->join('asset_sos', 'asset_sos.id', 'asset_so_plants.asset_so_id')
                            ->where('asset_so_plants.id', $dataAssetSoPlant->id)
                            ->select('asset_so_plants.*', 'asset_sos.month', 'asset_sos.year')
                            ->first();

        $detailAssetSoPlant = DB::table('asset_so_details')
                                ->where('asset_so_plant_id', $assetSoPlant->id)
                                ->get();

        $periodeMonthSoLabel = Helper::getMonthByNumberMonth($assetSoPlant->month);

        $plantCode = Plant::getCodeById($assetSoPlant->plant_id);
        $plantName = Plant::getShortNameById($assetSoPlant->plant_id);
        $plantType = Plant::getTypeByPlantId($assetSoPlant->plant_id);

        $dataview = [
            'assetSoPlant' => [
                'head' => $assetSoPlant,
                'detail' => $detailAssetSoPlant,
                'label' => $periodeMonthSoLabel
            ],
            'plant' => [
                'code' => $plantCode,
                'name' => $plantName,
                'type' => $plantType
            ]
        ];

        return view('financeacc.asset-so-preview', $dataview)->render();

    }

    public function upload(Request $request)
    {
        $userAuth = $request->get('userAuth');

        $request->validate([
            'file_excel' => 'required'
        ]);

        $stat = 'success';
        $msg = Lang::get("message.import.success", ["data" => Lang::get("asset so")]);

        if ($request->file('file_excel')) {
            try {
                $import = new AssetSoImport($userAuth->company_id_selected);
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

    public function manualGenerateSO($typePlant)
    {
        // get asset so id this periode
        $assetSo = $this->getAssetSoId();

        // get cost center exclude
        $costCenterExludes = Configuration::getValueCompByKeyFor($assetSo->company_id, 'financeacc', 'cost_center_exclude');
        $costCenterExludes = explode(',', $costCenterExludes);

        // type plant, 1 = outlet, 2 = DC
        if ($typePlant != 'dc') {
            // update status so asset outlet true
            $statusAssetSoOutlet = Configuration::getValueCompByKeyFor($assetSo->company_id, 'financeacc', 'status_outlet_asset_so');
            if ($statusAssetSoOutlet != 'Running') {
                Configuration::setValueByKeyFor('financeacc', 'status_outlet_asset_so', 'Running');
            }
        } else {
            // update status so asset dc true
            $statusAssetSoDC = Configuration::getValueCompByKeyFor($assetSo->company_id, 'financeacc', 'status_dc_asset_so');
            if ($statusAssetSoDC != 'Running') {
                Configuration::setValueByKeyFor('financeacc', 'status_dc_asset_so', 'Running');
            }
        }

        // get template notification system for asset so by key
        $keyNotificationAsset = Configuration::getValueCompByKeyFor($assetSo->company_id, 'financeacc', 'key_notification_asset_so');
        $qNotifSystem = DB::table('notification_systems')
                            ->select('id')
                            ->where('key', $keyNotificationAsset);
        $notificationSystemId = 0;
        if($qNotifSystem->count() > 0){
            $notifSystem = $qNotifSystem->first();
            $notificationSystemId = $notifSystem->id;
        }

        if($assetSo){
            if ($typePlant != 'dc') {
                Log::info('Manual Generate asset outlet periode ' . date('n-Y') . ' running');
            } else {
                Log::info('Manual Generate asset dc periode ' . date('n-Y') . ' running');
            }

            // get plant already generated asset so
            $qPlantAlreadyGenerate = DB::table('asset_so_plants')
                                        ->leftJoin('plants', 'plants.id', 'asset_so_plants.plant_id')
                                        ->where('asset_so_plants.asset_so_id', $assetSo->id);

            // type plant, 1 = outlet, 2 = DC
            if($typePlant != 'dc'){
                // outlet
                $qPlantAlreadyGenerate = $qPlantAlreadyGenerate->where('plants.type', 1);
            } else {
                // dc
                $qPlantAlreadyGenerate = $qPlantAlreadyGenerate->where('plants.type', 2);
            }

            $plantAlreadyGenerate = $qPlantAlreadyGenerate
                                        ->distinct()
                                        ->pluck('asset_so_plants.plant_id')
                                        ->toArray();

            // get 10 plant to generate not in plant already generate
            $qPlantToGenerates = DB::table('assets')
                                    ->leftJoin('plants', 'plants.id', 'assets.plant_id')
                                    ->whereNotIn('assets.plant_id', $plantAlreadyGenerate)
                                    ->distinct()
                                    ->limit(10);

            // type plant, 1 = outlet, 2 = DC
            if ($typePlant != 'dc') {
                // outlet
                $qPlantToGenerates = $qPlantToGenerates->where('plants.type', 1);
            } else {
                // dc
                $qPlantToGenerates = $qPlantToGenerates->where('plants.type', 2);
            }

            // check if there are still plants that have not been generated
            if($qPlantToGenerates->count() > 0){

                // get plant id to generate to array
                $plantToGenerates = $qPlantToGenerates->pluck('assets.plant_id')->toArray();

                foreach ($plantToGenerates as $plantToGenerate) {

                    // get all cost center having asset in plant
                    $costCenters = DB::table('assets')
                                    ->where('plant_id', $plantToGenerate)
                                    ->whereNotIn('cost_center_code', $costCenterExludes)
                                    ->distinct()
                                    ->pluck('cost_center', 'cost_center_code')
                                    ->toArray();

                    foreach ($costCenters as $costCenterCode => $costCenter) {

                        DB::beginTransaction();

                        // insert into to asset so plant
                        $assetSoPlant = new AssetSoPlant;
                        $assetSoPlant->upload_code = AssetSo::generateUploadCode($plantToGenerate, $costCenterCode);
                        $assetSoPlant->asset_so_id = $assetSo->id;
                        $assetSoPlant->plant_id = $plantToGenerate;
                        $assetSoPlant->cost_center = $costCenter;
                        $assetSoPlant->cost_center_code = $costCenterCode;
                        if($assetSoPlant->save()){

                            // get all asset by plant and costcenter
                            $assetPlants = DB::table('assets')
                                            ->select(
                                                'number',
                                                'number_sub',
                                                'description',
                                                'spec_user',
                                                'qty_web',
                                                'uom',
                                                'remark'
                                            )
                                            ->where('plant_id', $plantToGenerate)
                                            ->where('cost_center_code', $costCenterCode)
                                            ->get();

                            foreach ($assetPlants as $assetPlant) {

                                // insert into asset so detail
                                $assetSoDetail = new AssetSoDetail;
                                $assetSoDetail->asset_so_plant_id = $assetSoPlant->id;
                                $assetSoDetail->number = $assetPlant->number;
                                $assetSoDetail->number_sub = $assetPlant->number_sub;
                                $assetSoDetail->description = $assetPlant->description;
                                $assetSoDetail->spec_user = $assetPlant->spec_user;
                                $assetSoDetail->qty_web  = $assetPlant->qty_web;
                                $assetSoDetail->uom = $assetPlant->uom;
                                $assetSoDetail->remark = $assetPlant->remark;
                                $assetSoDetail->qty_so = 0;
                                $assetSoDetail->qty_selisih = 0 - $assetPlant->qty_web;
                                $assetSoDetail->remark_so = '';
                                if($assetSoDetail->save()){

                                    DB::commit();

                                } else {
                                    DB::rollback();
                                    Log::alert('Manual Generate asset so failed save so detail, plant : ' .
                                        $plantToGenerate . ' costcenter : ' . $costCenter);
                                }

                            }

                        } else {
                            DB::rollback();
                            Log::alert('Manual Generate asset so failed save so plant, plant : ' .
                                $plantToGenerate . ' costcenter : ' . $costCenter);
                        }

                    }

                    // send notification for store manager plant that asset so already generated
                    $userModId = Plant::getMODIdPlantById($plantToGenerate);

                    // check mod for store already mapping or not
                    // if not yet mappping don't send notification
                    if ($userModId != 0 && $notificationSystemId != 0) {

                        // send notification for outlet
                        $notificationSystemRead = new NotificationSystemRead;
                        $notificationSystemRead->notification_system_id = $notificationSystemId;
                        $notificationSystemRead->user_id = $userModId;
                        $notificationSystemRead->read = 0;
                        $notificationSystemRead->save();
                    }

                }

            } else {
                // all already generate
                // update status
                $assetSo = AssetSo::find($assetSo->id);
                if ($typePlant != 'dc') {
                    $assetSo->status_generate_outlet = 1;
                } else {
                    $assetSo->status_generate_dc = 1;
                }

                $assetSo->save();
            }

        } else {
            if ($typePlant != 'dc') {
                Log::alert('Manual Generate asset so outlet failed to get asset so id');
            } else {
                Log::alert('Manual Generate asset so dc failed to get asset so id');
            }
        }
    }

    public function getAssetSoId()
    {
        $periodeMonth = Date('n');
        $periodeYear = Date('Y');

        $qAssetSo = DB::table('asset_sos')
                        ->select('id')
                        ->where('month', $periodeMonth)
                        ->where('year', $periodeYear);

        if($qAssetSo->count() > 0){
            // exist
            $assetSo = $qAssetSo->first();
        } else {
            // not exist and create
            $assetSo = new AssetSo;
            $assetSo->month = $periodeMonth;
            $assetSo->month_label = Helper::getMonthByNumberMonth($periodeMonth);
            $assetSo->year = $periodeYear;
            $assetSo->save();
        }

        return $assetSo;

    }

    // submit so manual
    public function manualSubmitSO($typePlant)
    {
        // get asset so id this periode
        $assetSoId = $this->getAssetSoIdSubmit();

        if ($assetSoId != 0) {

            $assetSo = DB::table('asset_sos')
                        ->where('id', $assetSoId)
                        ->first();

            $sendSelisihDepartAsset = true;
            $periode = $assetSo->month_label . ' ' . $assetSo->year;

            if ($typePlant != 'dc') {

                // log run
                Log::info('Submit asset so outlet periode ' . date('n-Y') . ' running');

                // check status submit outlet
                if($assetSo->status_submit_outlet != 1){
                    // update status submit outlet
                    $uAssetSo = AssetSo::find($assetSoId);
                    $uAssetSo->status_submit_outlet = 1;
                    $uAssetSo->save();
                }

                // update status so asset outlet false
                $statusAssetSoOutlet = Configuration::getValueCompByKeyFor($assetSo->company_id, 'financeacc', 'status_outlet_asset_so');
                if ($statusAssetSoOutlet != 'Not Running') {
                    Configuration::setValueCompByKeyFor($assetSo->company_id, 'financeacc', 'status_outlet_asset_so', 'Not Running');
                }

                // check send selisih to depart asset
                if ($assetSo->send_depart_asset_outlet == '0') {
                    $sendSelisihDepartAsset = false;
                }

                // get data sended am outlet
                $listSendAm = json_decode($assetSo->send_am_outlet);

                // subject email
                $subject = Lang::get('Result Stock Opname Asset Outlet Periode ') . ' ' . $periode;

            } else {

                // log run
                Log::info('Submit asset so dc periode ' . date('n-Y') . ' running');

                // check status submit dc
                if ($assetSo->status_submit_dc != 1) {
                    // update status submit dc
                    $uAssetSo = AssetSo::find($assetSoId);
                    $uAssetSo->status_submit_dc = 1;
                    $uAssetSo->save();
                }

                // update status so asset dc false
                $statusAssetSoDC = Configuration::getValueCompByKeyFor($assetSo->company_id, 'financeacc', 'status_dc_asset_so');
                if ($statusAssetSoDC != 'Not Running') {
                    Configuration::setValueCompByKeyFor($assetSo->company_id, 'financeacc', 'status_dc_asset_so', 'Not Running');
                }

                // check send selisih to depart asset
                if ($assetSo->send_depart_asset_dc == '0') {
                    $sendSelisihDepartAsset = false;
                }

                // get data sended am dc
                $listSendAm = json_decode($assetSo->send_am_dc);

                // subject email
                $subject = Lang::get('Result Stock Opname Asset DC Periode ') . ' ' . $periode;
            }

            // send email selisih to depart asset if false
            if (!$sendSelisihDepartAsset) {

                // create file excel selisih
                $fileSelisihAssetSo = AssetSo::GenerateSelisihSoExcel($assetSo->id, $typePlant, 0);

                // send selisih to depart asset
                $emailDepartAsset = Configuration::getValueCompByKeyFor($assetSo->company_id, 'financeacc', 'email_depart_asset');
                $to = explode(',', $emailDepartAsset);
                $cc = [];

                try {
                    Mail::queue(new NotificationSelisihAssetSo($periode, $subject, $to, $cc, $fileSelisihAssetSo, 'Depart Asset'));
                } catch (\Throwable $th) {
                    Log::alert('Send mail selisih asset to depart asset error : ' . $th->getMessage());
                }

                // update send depart asset complete
                $update = [];

                if ($typePlant != 'dc') {
                    $update = [
                        'send_depart_asset_outlet' => 1
                    ];
                } else {
                    $update = [
                        'send_depart_asset_dc' => 1
                    ];
                }

                DB::table('asset_sos')
                    ->where('id', $assetSoId)
                    ->update($update);
            }

            // check already or not send selisih to am
            if (!$listSendAm) {
                $listSendAm = [];
            }

            $qAmNotYetSends = DB::table('mapping_area_plants')
                                ->join('plants', 'plants.id', 'mapping_area_plants.plant_id')
                                ->whereNotIn('mapping_area_plants.area_plant_id', $listSendAm)
                                ->distinct();

            if ($typePlant != 'dc') {
                $qAmNotYetSends = $qAmNotYetSends->where('plants.type', 1);
            } else {
                $qAmNotYetSends = $qAmNotYetSends->where('plants.type', 2);
            }

            if ($qAmNotYetSends->count() > 0) {

                // have area not yet send
                $amNotYetSends = $qAmNotYetSends
                                    ->limit('10')
                                    ->pluck('mapping_area_plants.area_plant_id');

                foreach ($amNotYetSends as $amNotYetSend) {

                    // check am have email or not
                    $qUserAM = DB::table('user_areas')
                                ->join('users', 'users.id', 'user_areas.user_id')
                                ->join('area_plants', 'area_plants.id', 'user_areas.area_plant_id')
                                ->where('user_areas.area_plant_id', $amNotYetSend)
                                ->select('users.email', 'user_areas.area_plant_id', 'area_plants.name');

                    if ($qUserAM->count() > 0) {

                        // am have email to send file selisih
                        $userAm = $qUserAM->first();

                        // create file excel selisih
                        $fileSelisihAssetSo = AssetSo::GenerateSelisihSoExcel($assetSo->id, $typePlant, $amNotYetSend);

                        // send selisih to depart asset
                        $to = [$userAm->email];
                        $cc = [];

                        // get email rm by am
                        $qRmUserAm = DB::table('user_regionals')
                                        ->join('mapping_regional_areas', 'mapping_regional_areas.regional_plant_id', 'user_regionals.regional_plant_id')
                                        ->join('users', 'users.id', 'user_regionals.user_id')
                                        ->where('mapping_regional_areas.area_plant_id', $amNotYetSend)
                                        ->select('users.email');

                        if($qRmUserAm->count()){
                            $rmUserAm = $qRmUserAm->first();
                            $cc[] = $rmUserAm->email;
                        }

                        try {
                            Mail::queue(new NotificationSelisihAssetSo($periode, $subject, $to, $cc, $fileSelisihAssetSo, 'Area Manager ' . $userAm->name));
                        } catch (\Throwable $th) {
                            Log::alert('Send mail selisih asset to am asset error : ' . $th->getMessage());
                        }

                    }

                }

                // update am sended file selisih
                $sendAmOutletSended = array_merge($listSendAm, $amNotYetSends->toArray());
                $update = [];

                if ($typePlant != 'dc') {
                    $update = [
                        'send_am_outlet' => json_encode($sendAmOutletSended)
                    ];
                } else {
                    $update = [
                        'send_am_dc' => json_encode($sendAmOutletSended)
                    ];
                }

                DB::table('asset_sos')
                    ->where('id', $assetSoId)
                    ->update($update);

            }

        } else {

            if ($typePlant != 'dc') {
                Log::alert('Manual submmit asset so outlet failed : asset not yet created');
            } else {
                Log::alert('Manual submmit asset so dc failed : asset not yet created');
            }
        }
    }

    public function getAssetSoIdSubmit()
    {
        $periodeMonth = Date('n');
        $periodeYear = Date('Y');

        $qAssetSo = DB::table('asset_sos')
                        ->select('id')
                        ->where('month', $periodeMonth)
                        ->where('year', $periodeYear);

        $assetSoId = 0;

        if($qAssetSo->count() > 0){
            // exist
            $assetSo = $qAssetSo->first();
            $assetSoId = $assetSo->id;
        }

        return $assetSoId;

    }

    public function checkCountAssetSo(Request $request) {
        $userAuth = $request->get('userAuth');

        $periodeMonth = $request->month;
        $periodeYear = $request->year;

        $assetSo = DB::table('asset_sos')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->where('month', $periodeMonth)
                    ->where('year', $periodeYear)
                    ->first();

        $assetSoPlants = DB::table('asset_so_plants')
                            ->where('asset_so_id', $assetSo->id)
                            ->get();

        foreach ($assetSoPlants as $assetSoPlant) {
            $countListAsset = DB::table('assets')
                                ->where('plant_id', $assetSoPlant->plant_id)
                                ->where('cost_center_code', $assetSoPlant->cost_center_code)
                                ->count();

            $countSoAsset = DB::table('asset_so_details')
                            ->where('asset_so_plant_id', $assetSoPlant->id)
                            ->count();

            echo 'Plant: ' . $assetSoPlant->plant_id . ' List Asset : ' . $countListAsset . ' | So Asset : ' . $countSoAsset . ' | selisih = ' . ($countListAsset - $countSoAsset) .  '<br>';

            if( $countSoAsset < $countListAsset){

                $listAssets = DB::table('assets')
                                    ->where('plant_id', $assetSoPlant->plant_id)
                                    ->where('cost_center_code', $assetSoPlant->cost_center_code)
                                    ->get();
                $i = 1;
                foreach ($listAssets as $listAsset) {

                    $check = DB::table('asset_so_details')
                                ->where('asset_so_plant_id', $assetSoPlant->id)
                                ->where('number', $listAsset->number)
                                ->where('number_sub', $listAsset->number_sub)
                                ->count();

                    if( $check <= 0 ){

                        $assetSoDetail = new AssetSoDetail;
                        $assetSoDetail->asset_so_plant_id = $assetSoPlant->id;
                        $assetSoDetail->number = $listAsset->number;
                        $assetSoDetail->number_sub = $listAsset->number_sub;
                        $assetSoDetail->description = $listAsset->description;
                        $assetSoDetail->spec_user = $listAsset->spec_user;
                        $assetSoDetail->qty_web  = $listAsset->qty_web;
                        $assetSoDetail->uom = $listAsset->uom;
                        $assetSoDetail->remark = $listAsset->remark;
                        $assetSoDetail->qty_so = 0;
                        $assetSoDetail->qty_selisih = 0 - $listAsset->qty_web;
                        $assetSoDetail->remark_so = '';
                        $assetSoDetail->save();

                        $i++;

                    }

                }

            }

        }
    }

    public function fixQtyWeb(Request $request) {
        $userAuth = $request->get('userAuth');

        $periodeMonth = $request->month;
        $periodeYear = $request->year;
        $plantId = $request->plant;

        $assetSo = DB::table('asset_sos')
                    ->where('company_id', $userAuth->company_id_selected)
                    ->where('month', $periodeMonth)
                    ->where('year', $periodeYear)
                    ->first();

        $assetSoPlant = DB::table('asset_so_plants')
                        ->where('asset_so_id', $assetSo->id)
                        ->where('plant_id', $plantId)
                        ->first();

        $assetSoDetails = DB::table('asset_so_details')
                            ->where('asset_so_plant_id', $assetSoPlant->id)
                            ->get();

        foreach ($assetSoDetails as $assetSoDetail) {

            $qtyAsset = DB::table('assets')
                            ->where('plant_id', $assetSoPlant->plant_id)
                            ->where('number', $assetSoDetail->number)
                            ->where('number_sub', $assetSoDetail->number_sub)
                            ->first();

            $iassetSoDetail = AssetSoDetail::find($assetSoDetail->id);
            $iassetSoDetail->qty_web = $qtyAsset->qty_web;
            $iassetSoDetail->qty_selisih = $assetSoDetail->qty_so - $qtyAsset->qty_web;
            $iassetSoDetail->save();

        }

        !dd($iassetSoDetail);

    }


}
