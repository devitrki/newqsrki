<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Repositories\SapRepositoryAppsImpl;

use App\Models\Configuration;
use App\Models\Plant;
use App\Models\Inventory\Opname;
use App\Models\Inventory\OpnameMaterialFormula;

class OpnameServiceAppsImpl implements OpnameService
{
    public function getPreviewData($opnameId)
    {
        $status = true;
        $message = '';
        $data = [];

        $opname = DB::table('opnames')
                    ->where('id', $opnameId)
                    ->first();

        $opname_items  = DB::table('opname_items')
                            ->where('opname_id', $opnameId)
                            ->get();

        $plant = DB::table('plants')
                    ->where('id', $opname->plant_id)
                    ->first();

        $param = [
            'type' => 'all',
            'plant' => $plant->code
        ];

        $sapRepository = new SapRepositoryAppsImpl(true);
        $sapResponse = $sapRepository->getCurrentStockPlant($param);

        $stockSap = [];

        if ($sapResponse['status']) {
            $stockSap = $sapResponse['response'];
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
                    if( $v->material_code == $stock['material_code'] ){
                        $qtySap = $stock['qty'];
                        $uomSap = $stock['uom'];
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

        $dataFormatSAP = $this->getOpnameFormat($opname);

        $dataUpload = [
            'items' => json_encode($dataFormatSAP),
        ];

        !dd($dataUpload);

        $sapRepository = new SapRepositoryAppsImpl();
        $sapResponse = $sapRepository->uploadOpname($dataUpload);

        $document_number = '';

        if ($sapResponse['status']) {
            $res_sap = $sapResponse['response'];

            if( $res_sap['success'] == 'true' ){

                $return = $res_sap['data'];

                if (is_array($return)) {
                    //Error / Warning dan sukses bisa dalam dalam array return
                    //No. DOC selalu ada direturn array terakhir dengan strpos 'posted'
                    $last_error = $return[sizeof($return) - 1];
                    if (substr($last_error['MESSAGE'], 0, 8) == 'Document') {
                        $message          = explode(' ', $last_error['MESSAGE']);
                        $document_number = $message[1];
                    } else {
                        $errors = [];
                        foreach ($return as $error) {
                            $errors[] = $error['MESSAGE'];
                        }

                        if ($errors){
                            $status = false;
                            $message = Lang::get("Feedback SAP") . ' : ' . implode(' <br/> ', $errors);
                        }
                    }
                } elseif (substr( trim($return) , -15, -11) == 'doc.') {
                    //Jika sukses returnnya string
                    $return          = explode(' ', $return);
                    $document_number = $return[10];
                } elseif (substr( trim($return) , 0, 24) == 'Diffs in phys. inv. doc.') {
                    //Jika sukses returnnya string
                    $message          = explode(' ', $return);
                    $document_number = $message[sizeof($message)];
                } else {
                    $status = false;
                    $message = Lang::get("Feedback SAP") . ' : ' . $return;
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
                                ->where('material_code', $stock['material_code'])
                                ->update([
                                    'qty_sap' => $stock['qty'],
                                    'uom_sap' => $stock['uom']
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
                $message = Lang::get("message.submit.failed", ["data" => Lang::get("opname")]);
            }
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    // utility
    private function getOpnameFormat($opname)
    {
        $opnameItems  = DB::table('opname_items')->where('opname_id', $opname->id)->get();
        $plantCode = Plant::getCodeById($opname->plant_id);
        $plantSlocIdOpname = Plant::getSlocIdOpname($opname->plant_id);
        $opnameMaterialFormulas = $this->getOpnameMaterialFormulas($opname->company_id);

        $data = [];

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

        foreach ($opnameItems as $item) {
            if (in_array($item->material_code, $skipMaterialSap)) {
                continue;
            }

            foreach ($opnameMaterialFormulas as $opnameFormulaMaterialCode => $opnameFormulaMaterialData) {
                if ($item->material_code == $opnameFormulaMaterialCode) {
                    $item->qty_final = $opnameFormulaMaterialData['qty'];
                }
            }

            $data[] = [
                'col01' => date('d.m.Y', strtotime($opname->date)),
                'col02' => $plantCode,
                'col03' => $plantSlocIdOpname,
                'col04' => $item->material_code,
                'col05' => '',
                'col06' => str_replace('.', ',', $item->qty_final),
                'col07' => $item->uom_final,
                'col08' => ( $item->qty_final + 0 ) <> 0 ? '' : 'X'
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
