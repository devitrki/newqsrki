<?php

namespace App\Imports\Financeacc;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Financeacc\AssetSoPlant;
use App\Models\Financeacc\AssetSoDetail;
use App\Models\Plant;

class AssetSoImport implements ToCollection, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 4;
    }

    public function collection(Collection $rows)
    {
        $status = 'success';
        $msg = '';

        // get plant user
        $plantId = Plant::getPlantIdByUserId(Auth::id());

        // check upload code is correct or not
        $uploadCode = trim($rows[0][1]);

        $qAssetSoPlant = DB::table('asset_so_plants')
                            ->join('asset_sos', 'asset_sos.id', 'asset_so_plants.asset_so_id')
                            ->where('asset_so_plants.upload_code', $uploadCode)
                            ->where('asset_so_plants.plant_id', $plantId)
                            ->select('asset_so_plants.*', 'asset_sos.month', 'asset_sos.year');

        if($qAssetSoPlant->count() > 0){

            $assetSoPlant = $qAssetSoPlant->first();

            // get note
            $note = trim($rows[2][1]);

            // update note
            $uAssetSoPlant = AssetSoPlant::find($assetSoPlant->id);
            $uAssetSoPlant->note = $note;
            $uAssetSoPlant->save();

            foreach ($rows as $i => $row) {

                if($i < 7){
                    continue;
                }

                $assetNumber = trim($row[0]);
                $assetNumberSub = trim($row[1]);
                $qtySO = trim($row[4]);
                $remarkSO = trim($row[7]);

                if( !is_numeric($qtySO) ){
                    $qtySO = 0;
                }

                $assetSoDetail = AssetSoDetail::where('asset_so_plant_id', $assetSoPlant->id)
                                    ->where('number', $assetNumber)
                                    ->where('number_sub', $assetNumberSub)
                                    ->first();

                $qtySelisih = $qtySO - $assetSoDetail->qty_web;

                $assetSoDetail->qty_so = $qtySO;
                $assetSoDetail->qty_selisih = $qtySelisih;
                $assetSoDetail->remark_so = $remarkSO;
                $assetSoDetail->save();

            }

        } else {
            $status = 'failed';
            $msg = \Lang::get("File excel not valid. Please download the valid file.");
        }

        $return = [
            'status' => $status,
            'message' => $msg
        ];

        $this->return = $return;
    }
}
