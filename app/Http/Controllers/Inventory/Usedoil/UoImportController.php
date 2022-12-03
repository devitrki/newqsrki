<?php

namespace App\Http\Controllers\Inventory\Usedoil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Usedoil\UoCategoryPrice;
use App\Models\Inventory\Usedoil\UoVendor;
use App\Models\Inventory\Usedoil\UoSaldoVendor;
use App\Models\Plant;

class UoImportController extends Controller
{
    public function import(Request $request){
        $userAuth = $request->get('userAuth');

        switch ($request->type) {
            case 'category_price':
                $this->importCategoryPrice($userAuth->company_id_selected);
                break;
            case 'vendor':
                $this->importvendor($userAuth->company_id_selected);
                break;
            case 'saldo_vendor':
                $this->importSaldovendor();
                break;

            default:
                echo "Nothing";
                break;
        }
    }

    public function importCategoryPrice($companyId){

        $categoryPrices =  DB::connection('apps')
                            ->table('uo_kategori_price')
                            ->get();

        DB::beginTransaction();

        foreach ($categoryPrices as $categoryPrice) {

            $uoCategoryPrice = new UoCategoryPrice;
            $uoCategoryPrice->id = $categoryPrice->kategori_price_id;
            $uoCategoryPrice->company_id = $companyId;
            $uoCategoryPrice->name = $categoryPrice->name;
            if ($uoCategoryPrice->save()) {

                $categoryPriceDetails =  DB::connection('apps')
                                ->table('uo_kategori_price_detail')
                                ->where('kategori_price_id', $categoryPrice->kategori_price_id)
                                ->get();

                $insertDetail = [];
                foreach ($categoryPriceDetails as $categoryPriceDetail) {

                    $material = DB::table('uo_materials')->select('id')->where('code', $categoryPriceDetail->material_code)->first();

                    $insertDetail[] = [
                        'uo_category_price_id' => $categoryPriceDetail->kategori_price_id,
                        'uo_material_id' => $material->id,
                        'price' => $categoryPriceDetail->price
                    ];
                }

                DB::table('uo_category_price_details')->insert($insertDetail);

            }

        }

        DB::commit();

        !dd('import success');

    }

    public function importVendor($companyId){

        $vendors =  DB::connection('apps')
                            ->table('uo_vendor')
                            ->get();

        DB::beginTransaction();

        foreach ($vendors as $vendor) {

            if ($vendor->kategori_price_id <= 0) {
                continue;
            }

            $uoVendor = new UoVendor;
            $uoVendor->id = $vendor->vendor_id;
            $uoVendor->company_id = $companyId;
            $uoVendor->uo_category_price_id = $vendor->kategori_price_id;
            $uoVendor->name = $vendor->name;
            $uoVendor->address = $vendor->address;
            $uoVendor->city = $vendor->city;
            $uoVendor->province = $vendor->province;
            $uoVendor->contact_person = $vendor->contact_person;
            $uoVendor->phone = $vendor->phone;
            if( $uoVendor->save() ){

                $vendorPlants =  DB::connection('apps')
                                ->table('uo_vendor_plant')
                                ->where('vendor_id', $vendor->vendor_id)
                                ->get();

                $inserts = [];
                foreach ($vendorPlants as $vendorPlant) {
                    $inserts[] = [
                        'uo_vendor_id' => $vendorPlant->vendor_id,
                        'plant_id' => Plant::getIdByCode($vendorPlant->plant)
                    ];
                }

                DB::table('uo_vendor_plants')->insert($inserts);

            }

        }

        DB::commit();

        !dd('import success');


    }

    public function importSaldoVendor(){

        $saldoVendors =  DB::connection('apps')
                            ->table('uo_saldo_vendor')
                            ->get();

        DB::beginTransaction();

        foreach ($saldoVendors as $saldoVendor) {

            $uoSaldoVendor = new UoSaldoVendor;
            $uoSaldoVendor->uo_vendor_id = $saldoVendor->vendor_id;
            $uoSaldoVendor->saldo = $saldoVendor->saldo;
            $uoSaldoVendor->save();
        }

        DB::commit();

        !dd('import success');
    }

}
