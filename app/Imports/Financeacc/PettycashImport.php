<?php

namespace App\Imports\Financeacc;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use App\Library\Helper;

use App\Models\Financeacc\Pettycash;
use App\Models\Plant;

class PettycashImport implements ToCollection, WithStartRow
{
    public $companyId;

    function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $status = 'success';
        $msg = '';

        $insert = [];

        foreach ($rows as $index => $row) {
            // check nominal must numeric
            if ( !is_numeric($row[0]) ) {
                $status = 'failed';
                $msg = "Row " . ((int)$index + 2) . " : " . Lang::get("Please check, nominal debit must numeric / number");
                break;
            }

            // check descriprion required
            if( $row[1] == '' ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 2) . " : " . Lang::get("Please check, description cannot be empty");
                break;
            }

            // check pic required
            if( $row[3] == '' ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 2) . " : " . Lang::get("Please check, pic cannot be empty");
                break;
            }

            // check plant code must exists
            $plant = DB::table('plants')
                        ->where('code', $row[2])
                        ->first();

            if( !isset($plant->id) ){
                $status = 'failed';
                $msg = "Row " . ((int)$index + 2) . " : " . Lang::get("Please check, plant code not exists");
                break;
            } else {

                // adding data to array insert
                $insert[$plant->id][] = [
                    'nominal' => $row[0],
                    'description' => $row[1],
                    'plant' => $row[2],
                    'pic' => $row[3],
                ];

            }
        }

        if( sizeof($insert) > 0 && $status != 'failed'){

            DB::beginTransaction();

            // have data to insert
            foreach ($insert as $plantId => $datas) {

                // get saldo last plant
                $lastTransaction = DB::table('pettycashes')
                    ->where('plant_id', $plantId)
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
                                                ->where('plant_id', $plantId)
                                                ->where('type', 1)
                                                ->distinct('transaction_id')
                                                ->count('transaction_id');

                $countTransactionPlantType = $countTransactionPlantType + 1;

                // insert item add
                $plantShortName = Plant::getShortNameById($plantId);
                $tglInd = date('d-m-Y');

                $orderNumber = 1;
                $totalDebit = 0;

                foreach ($datas as $data) {

                    $totalDebit  += floatVal($data['nominal']);

                    $pettycash = new Pettycash;
                    $pettycash->company_id = $this->companyId;
                    $pettycash->transaction_id = $transactionIdNew;
                    $pettycash->order_number = $orderNumber;
                    $pettycash->type_id = 'KD-' . $countTransactionPlantType;
                    $pettycash->type = 1;
                    $pettycash->transaction_date = date('Y-m-d');
                    $pettycash->pic = $data['pic'];
                    $pettycash->voucher_number = '';
                    $pettycash->plant_id = $plantId;
                    $pettycash->debit = $data['nominal'];
                    $pettycash->kredit = 0;
                    $pettycash->saldo = $lastSaldo + $totalDebit;
                    $pettycash->remark = $plantShortName . ' (' . $tglInd . ')';
                    $pettycash->description = $data['description'];
                    $pettycash->gl_code = 0;
                    $pettycash->gl_desc = 'Debit';
                    $pettycash->approve = 1;
                    $pettycash->approved_at = date('Y-m-d H:i:s');
                    $pettycash->submit = 1;
                    $pettycash->submited_at = date('Y-m-d H:i:s');
                    $pettycash->save();

                    $orderNumber += 1;

                }

            }

            DB::commit();

        }

        $return = [
            'status' => $status,
            'message' => $msg
        ];

        $this->return = $return;
    }

}
