<?php

namespace App\Http\Controllers\Interfaces\Aloha;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

use App\Jobs\Interfaces\Aloha\UploadSalesAloha;

use App\Library\Helper;
use App\Models\Plant;
use App\Models\Pos;
use App\Models\Pos\AlohaHistorySendSap;
use App\Models\Pos\AlohaInterface;

use App\Repositories\AlohaRepository;

class SendManualAlohaController extends Controller
{
    public function index(Request $request)
    {
        $dataview = [
            'menu_id' => $request->query('menuid'),
        ];
        return view('interfaces.aloha.send-manual-aloha', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $userAuth = $request->get('userAuth');
        $data = [];

        $posId = Pos::getIdByCode($userAuth->company_id_selected, 'aloha');
        $pos = Pos::find($posId);

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {
            $plantCode = '';
            if( $request->query('plant-id') != '0'){
                $plantCode = Plant::getCustomerCodeById($request->query('plant-id'));
            }

            $stores = $posRepository->getStoreAloha($request->query('from'), $request->query('until'), $plantCode);

            foreach ($stores as $store) {
                $data[] = [
                    'plant' => Plant::getShortNameByCustCode($userAuth->company_id_selected, $store->SecondaryStoreID),
                    'code' => $store->SecondaryStoreID,
                    'status' => AlohaHistorySendSap::getStatusSendSap($store->DateOfBusiness, $store->SecondaryStoreID),
                    'date' => date("Y/m/d", strtotime($store->DateOfBusiness)),
                    'date_desc' => date("d/m/Y", strtotime($store->DateOfBusiness))
                ];
            }
        }

        return Datatables::of($data)->addIndexColumn()->make();
    }

    public function view(Request $request)
    {
        $userAuth = $request->get('userAuth');
        $plant = Plant::getPlantByCustCode($userAuth->company_id_selected, $request->query('customer-code'));
        $pos = Pos::find($plant->pos_id);
        $posData = [];

        $posRepository = Pos::getInstanceRepo($pos);
        $initConnectionAloha = $posRepository->initConnectionDB();
        if ($initConnectionAloha['status']) {
            $posData = $posRepository->getSalesFormatSAP($request->query('customer-code'), $request->query('date'));
        }

        $dataview = [
            'plant_name' => Plant::getShortNameById($plant->id),
            'customer_code' => $request->query('customer-code'),
            'pos' => $posData,
            // 'pos' => AlohaInterface::getSalesFormatSAP($request->query('customer-code'), $request->query('date')),
            'date' => $request->query('date')
        ];

        return view('interfaces.aloha.send-manual-aloha-view', $dataview);
    }
}
