<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Repositories\SapRepositorySapImpl;

use App\Entities\SapMiddleware;

use App\Models\Configuration;
use App\Models\Plant;
use App\Models\Company;
use App\Models\Stock;
use App\Models\Inventory\Opname;
use App\Models\Inventory\OpnameMaterialFormula;

class OpnameServiceSapImpl implements OpnameService
{
    public function getPreviewData($opnameId)
    {
        $status = true;
        $message = '';
        $data = [];

        $opname = DB::table('opnames')
                    ->where('id', $opnameId)
                    ->first();

        $sapCodeComp = Company::getConfigByKey($opname->company_id, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
                'data' => $data
            ];
        }

        $opname_items  = DB::table('opname_items')
                            ->where('opname_id', $opnameId)
                            ->get();

        $plant = DB::table('plants')
                    ->where('id', $opname->plant_id)
                    ->first();

        $payload = [
            'company_id' => $sapCodeComp,
            'plant_id' => $plant->code,
            'sloc_id' => [Plant::getSlocIdCurStock($opname->plant_id)],
            'material_type_id' => Stock::getMaterialTypeAll()
        ];

        $sapRepository = new SapRepositorySapImpl($opname->company_id);
        $sapResponse = $sapRepository->getCurrentStockPlant($payload);

        $stockSap = [];
        if ($sapResponse['status']) {
            $stockSap = $sapResponse['response'];
        } else {
            return [
                'status' => false,
                'message' => Lang::get("Sorry, an error occurred, please try again later"),
                'data' => $data
            ];
        }

        $sHideMaterialSap = Configuration::getValueByKeyFor('inventory', 'mat_code_hide_opname');
        $hideMaterialSap = explode( ',', trim($sHideMaterialSap) );

        foreach ($opname_items as $v) {
            $qtySap = 0;
            $uomSap = '-';
            $selisih = '-';

            if( in_array($v->material_code, $hideMaterialSap) ){
                $qtySap = '-Hide By CO-';
            } else {
                foreach ($stockSap as $stock) {
                    if( $v->material_code == $stock['material_id'] ){
                        $qtySap = $stock['qty'];
                        $uomSap = $stock['uom_id'];
                    }
                }

                $selisih = Helper::convertNumberToInd($v->qty_final - $qtySap, '', 3);
            }

            $v->qty_sap = $qtySap;
            if($qtySap != '-Hide By CO-'){
                $v->qty_sap = Helper::convertNumberToInd($qtySap, '', 3);
            }

            $v->uom_sap = $uomSap;
            $v->selisih = $selisih;
        }

        $data = [
            'opname' => $opname,
            'opname_items' => $opname_items,
            'plant' => Plant::getShortNameById($opname->plant_id)
        ];

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    public function uploadOpname($opnameId)
    {
        $status = true;
        $message = '';
        $data = [];

        $opname = DB::table('opnames')
                    ->where('id', $opnameId)
                    ->first();

        $plant = DB::table('plants')
                    ->where('id', $opname->plant_id)
                    ->first();

        $sapCodeComp = Company::getConfigByKey($opname->company_id, 'SAP_CODE');
        if (!$sapCodeComp || $sapCodeComp == '') {
            return [
                'status' => false,
                'message' => Lang::get('Please set SAP_CODE in company configuration'),
                'data' => $data
            ];
        }

        $dataUpload = $this->getOpnameFormat($opname, $sapCodeComp);

        $sapRepository = new SapRepositorySapImpl($opname->company_id);
        $sapResponse = $sapRepository->uploadOpname($dataUpload);
        if ($sapResponse['status']) {
            $resSap = $sapResponse['response'];

            $document_number = '';
            $status = true;

            if ((bool)$resSap['success']) {
                $message = $resSap['message'];
                if (is_array($message)) {
                    //Error / Warning dan sukses bisa dalam dalam array return
                    //No. DOC selalu ada direturn array terakhir dengan strpos 'posted'
                    $last_error = $message[sizeof($message) - 1];
                    if (substr($last_error['MESSAGE'], 0, 8) == 'Document') {
                        $message          = explode(' ', $last_error['MESSAGE']);
                        $document_number = $message[1];
                    } else {
                        $errors = [];
                        foreach ($message as $error) {
                            $errors[] = $error['MESSAGE'];
                        }

                        if ($errors){
                            $status = false;
                            $message = Lang::get("Feedback SAP") . ' : ' . implode(' <br/> ', $errors);
                        }
                    }
                } elseif (substr( trim($message) , -15, -11) == 'doc.') {
                    //Jika sukses returnnya string
                    $message          = explode(' ', $message);
                    $document_number = $message[10];
                } elseif (substr( trim($message) , 0, 24) == 'Diffs in phys. inv. doc.') {
                    //Jika sukses returnnya string
                    $message          = explode(' ', $message);
                    $document_number = $message[sizeof($message)];
                } else {
                    $status = false;
                    $message = Lang::get("Feedback SAP") . ' : ' . $message;
                }

                if( $document_number != '' ){
                    DB::BeginTransaction();

                    // update stock
                    $param = [
                        'type' => 'all',
                        'plant' => $plant->code
                    ];
                    $sapResponse = $sapRepository->getCurrentStockPlant($param);
                    $stockSap = [];
                    if ($sapResponse['status']) {
                        $stockSap = $sapResponse['response'];
                    }

                    foreach ($stockSap as $stock) {
                        DB::table('opname_items')
                                ->where('opname_id', $opname->id)
                                ->where('material_code', $stock['material_id'])
                                ->update([
                                    'qty_sap' => $stock['qty'],
                                    'uom_sap' => $stock['uom_id']
                        ]);
                    }

                    $opname = Opname::find($opnameId);
                    $opname->document_number = $document_number;
                    $opname->submit = 1;
                    $opname->posting_date = date('Y-m-d H:i:s');
                    $opname->save();

                    DB::commit();

                    $status = true;
                    $message = Lang::get("message.submit.success", ["data" => Lang::get("opname")]);
                } else {
                    $status = false;
                }
            } else {
                $status = false;
                $message = SapMiddleware::getLastErrorMessage($resSap['logs']);
            }

        } else {
            $status = false;
            $message = 'Error middleware: ' . $sapResponse['response'];
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    // utility
    private function getOpnameFormat($opname, $sapCodeComp)
    {
        $opnameItems  = DB::table('opname_items')->where('opname_id', $opname->id)->get();
        $plantCode = Plant::getCodeById($opname->plant_id);
        $plantSlocIdOpname = Plant::getSlocIdOpname($opname->plant_id);
        $opnameMaterialFormulas = $this->getOpnameMaterialFormulas($opname->company_id);

        foreach ($opnameItems as $item) {
            foreach ($opnameMaterialFormulas as $opnameFormulaMaterialCode => $opnameFormulaMaterialData) {
                foreach ($opnameFormulaMaterialData['items'] as $opnameMaterialFormulaItem) {
                    if ($item->material_code == $opnameMaterialFormulaItem['material_code']) {
                        $opnameMaterialFormulas[$opnameFormulaMaterialCode]['qty'] += ($item->qty_final * (float)$opnameMaterialFormulaItem['multiplication']);
                    }
                }
            }
        }

        $sSkipMaterialSap = Configuration::getValueByKeyFor('inventory', 'mat_code_skip_opname');
        $skipMaterialSap = explode( ',', trim($sSkipMaterialSap) );

        $data = [
            'company_id' => $sapCodeComp,
            'items' => []
        ];

        foreach ($opnameItems as $item) {
            if (in_array($item->material_code, $skipMaterialSap)) {
                continue;
            }

            foreach ($opnameMaterialFormulas as $opnameFormulaMaterialCode => $opnameFormulaMaterialData) {
                if ($item->material_code == $opnameFormulaMaterialCode) {
                    $item->qty_final = $opnameFormulaMaterialData['qty'];
                }
            }

            $data['items'][] = [
                'plant_id' => $plantCode,
                'date_last_count' => date('Y-m-d', strtotime($opname->date)),
                'document_date' => date('Y-m-d', strtotime($opname->date)),
                'sloc_id' => $plantSlocIdOpname,
                'material_id' => $item->material_code,
                'entry_qty' => (float)$item->qty_final,
                'entry_uom_id' => $item->uom_final,
                'batch_number' => '',
                'is_zero_count' => ( $item->qty_final + 0 ) <> 0 ? false : true
            ];
        }

        return $data;
    }

    private function getOpnameMaterialFormulas($companyId)
    {
        $data = [];

        $opnameMaterialFormulas = OpnameMaterialFormula::where('company_id', $companyId)
                                    ->select('id', 'material_code')
                                    ->get();
        foreach ($opnameMaterialFormulas as $opnameMaterialFormula) {
            $opnameMaterialFormulaItems = $opnameMaterialFormula->opnameMaterialFormulaItems;
            $data[$opnameMaterialFormula->material_code] = [
                'qty' => 0,
                'items' => $opnameMaterialFormulaItems->toArray()
            ];
        }

        return $data;
    }
}
