<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class Plant extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function userPlants()
    {
        return $this->hasMany(UserPlant::class);
    }

    // utlity

    public static function getPlantsIdByUserId($user_id){

        $userRole = DB::table('model_has_roles')
                        ->where('model_type', 'App\Models\User')
                        ->where('model_id', $user_id)
                        ->select('role_id')
                        ->first();

        $role_am = Configuration::getValueByKeyFor('general_master', 'role_am');
        $role_rm = Configuration::getValueByKeyFor('general_master', 'role_rm');

        $plants = '';

        if ($userRole->role_id == $role_am) {
            // role am
            $userAreas = DB::table('user_areas')
                            ->select('area_plant_id')
                            ->where('user_id', $user_id)
                            ->first();

            $areaPlants = DB::table('mapping_area_plants')
                            ->where('area_plant_id', $userAreas->area_plant_id)
                            ->pluck('plant_id')
                            ->toArray();

            $plants = implode(",", $areaPlants);


        } else if ($userRole->role_id == $role_rm) {
            // role rm
            $userRegionals = DB::table('user_regionals')
                                ->select('regional_plant_id')
                                ->where('user_id', $user_id)
                                ->first();

            $areaRegionals = DB::table('mapping_regional_areas')
                                ->where('regional_plant_id', $userRegionals->regional_plant_id)
                                ->pluck('area_plant_id')
                                ->toArray();

            $areaPlants = DB::table('mapping_area_plants')
                            ->whereIn('area_plant_id', $areaRegionals)
                            ->pluck('plant_id')
                            ->toArray();

            $plants = implode(",", $areaPlants);

        } else {

            $userPlants = DB::table('user_plants')
                            ->leftJoin('plants', 'plants.id', 'user_plants.plant_id')
                            ->where('user_id', $user_id)
                            ->pluck('plant_id')
                            ->toArray();

            $plants = implode(",", $userPlants);
        }

        return $plants;
    }

    public static function getPlantIdByUserId($user_id){
        $userPlants = DB::table('user_plants')->where('user_id', $user_id);
        $id = 0;
        if( $userPlants->count() > 0 ){
            $userPlant = $userPlants->first();
            $id = $userPlant->plant_id;
        }
        return $id;
    }

    public static function getPlantsNameByUserId($user_id){
        $userPlants = DB::table('user_plants')->where('user_id', $user_id)->get();
        $plants = '';
        $i = 1;
        foreach ($userPlants as $key => $value) {
            $name = '';
            if($value->plant_id != 0){
                $plant = DB::table('plants')->where('id', $value->plant_id)->first();
                $name = $plant->initital . ' ' .$plant->short_name;
            }else{
                $name = Lang::get('All');
            }

            if($i > 1){
                $plants .= ',' . $name;
            }else{
                $plants .= $name;
            }
            $i++;
        }
        return $plants;
    }

    public static function getShortNameByCode($code){
        $plant = DB::table('plants')->where('code', $code)->select('initital', 'short_name')->first();
        $short_name = '';
        if (isset($plant->short_name)) {
            $short_name = $plant->initital . ' ' . $plant->short_name;
        }
        return $short_name;
    }

    public static function getShortNameById($id, $initital = true){
        $plant = DB::table('plants')->where('id', $id)->select('initital', 'short_name')->first();
        $short_name = '';
        if (isset($plant->short_name)) {
            if( $initital ){
                $short_name = $plant->initital . ' ' . $plant->short_name;
            } else {
                $short_name = $plant->short_name;
            }
        }
        return $short_name;
    }

    public static function getShortNameByCustCode($custCode, $initital = true){
        $plant = DB::table('plants')->where('customer_code', $custCode)->select('initital', 'short_name')->first();
        $short_name = '';
        if (isset($plant->short_name)) {
            if( $initital ){
                $short_name = $plant->initital . ' ' . $plant->short_name;
            } else {
                $short_name = $plant->short_name;
            }
        }
        return $short_name;
    }

    public static function getDataPlantById($id){
        $plant = DB::table('plants')->where('id', $id)->select('initital', 'short_name', 'email');
        $data = [];
        if ($plant->count() > 0) {
            $data = $plant->first();
        }
        return $data;
    }

    public static function getIdByCode($code){
        $plant = DB::table('plants')->where('code', $code)->select('id')->first();
        $id = 0;
        if (isset($plant->id)) {
            $id = $plant->id;
        }
        return $id;
    }

    public static function getIdByCustomerCode($customerCode){
        $plant = DB::table('plants')->where('customer_code', $customerCode)->select('id')->first();
        $id = 0;
        if (isset($plant->id)) {
            $id = $plant->id;
        }
        return $id;
    }

    public static function getCodeById($id){
        $plant = DB::table('plants')->where('id', $id)->select('code')->first();
        $code = '';
        if (isset($plant->code)) {
            $code = $plant->code;
        }
        return $code;
    }

    public static function getAddressById($id){
        $plant = DB::table('plants')->where('id', $id)->select('address')->first();
        $address = '';
        if (isset($plant->address)) {
            $address = $plant->address;
        }
        return $address;
    }

    public static function getPlantAuthUser($companyId, $id, $get = 'all'){
        $plants = DB::table('plants')
                        ->where('company_id', $companyId)
                        ->select('id');

        $plants_auth = Plant::getPlantsIdByUserId($companyId, $id);
        if (!in_array('0', explode(',', $plants_auth))) {
            $plants = $plants->whereIn('id', $plants);
        }
        if($get != 'all'){
            $plants = $plants->first();
        }else{
            $plants = $plants->get();
        }

        return $plants;
    }

    public static function getStoragePlantById($id)
    {
        $plant = DB::table('plants')->where('id', $id)->select('type')->first();
        $storage = 'S001';
        if (isset($plant->type)) {
            if($plant->type != '1'){
                $storage = 'DR01';
            }
        }
        return $storage;
    }

    public static function getFirstPlantIdSelect($companyId, $type, $auth = null)
    {
        if ($auth == null) {
            $auth = false;
        }

        $plant = DB::table('plants')
                    ->where('company_id', $companyId)
                    ->select('id');

        if($auth){
            $plants_auth = Plant::getPlantsIdByUserId(Auth::id());
            $plants = explode(',', $plants_auth);

            if (!in_array('0', $plants)) {
                $plant = $plant->whereIn('id', $plants);
            }
        }
        if ($type == 'dc' ) {
            $plant = $plant->where('type', 2);
        }
        if ($type == 'outlet' ) {
            $plant = $plant->where('type', 1);
        }

        $plant = $plant->orderBy('code')->first();
        return $plant->id;
    }

    public static function getCustomerCodeById($id)
    {
        $cust_code = '';
        $query = DB::table('plants')
                    ->where('id', $id)
                    ->select('plants.customer_code');

        if($query->count()){
            $data = $query->first();
            $cust_code = $data->customer_code;
        }

        return $cust_code;
    }

    public static function getCostCenterById($id)
    {
        $cc = '';
        $query = DB::table('plants')
                    ->where('id', $id)
                    ->select('cost_center');

        if($query->count()){
            $data = $query->first();
            $cc = $data->cost_center;
        }

        return $cc;
    }

    public static function getPosById($id)
    {
        $pos = '';
        $query = DB::table('plants')
                    ->where('id', $id)
                    ->select('pos_id');

        if($query->count()){
            $data = $query->first();
            $pos = $data->pos_id;
        }

        return $pos;
    }

    public static function getPosNameById($id)
    {
        $pos = '';
        $query = DB::table('plants')
                    ->where('id', $id)
                    ->select('pos');

        $poss = [
            "1" => "Aloha",
            "2" => "Vtec",
            "3" => "Quorion",
            "" => "",
        ];

        if($query->count()){
            $data = $query->first();
            $pos = $poss[$data->pos];

        }

        return $pos;
    }

    public static function getAMIdPlantById($id)
    {
        $am_id = 0;
        $queryMappingAreaPlant = DB::table('mapping_area_plants')
                                    ->where('plant_id', $id);

        if( $queryMappingAreaPlant->count() > 0 ) {
            $mappingAreaPlant = $queryMappingAreaPlant->first();

            $queryUserArea = DB::table('user_areas')
                                ->where('area_plant_id', $mappingAreaPlant->area_plant_id);

            if($queryUserArea->count() > 0){
                $userArea = $queryUserArea->first();
                $am_id = $userArea->user_id;
            }
        }

        return $am_id;
    }

    public static function getAMNamePlantById($id)
    {
        $am_name = '-';
        $queryMappingAreaPlant = DB::table('mapping_area_plants')
                                    ->where('plant_id', $id);

        if( $queryMappingAreaPlant->count() > 0 ) {
            $mappingAreaPlant = $queryMappingAreaPlant->first();

            $queryUserArea = DB::table('user_areas')
                                ->where('area_plant_id', $mappingAreaPlant->area_plant_id);

            if($queryUserArea->count() > 0){
                $userArea = $queryUserArea->first();
                $quser = DB::table('users')
                            ->leftJoin('profiles', 'profiles.id', 'users.profile_id')
                            ->select('profiles.name')
                            ->where('users.id', $userArea->user_id);
                $am_name = '';
                if($quser->count() > 0){
                    $user = $quser->first();
                    $am_name = $user->name;
                }
            }
        }

        return $am_name;
    }

    public static function getDataAMPlantById($id)
    {
        $am = '-';
        $queryMappingAreaPlant = DB::table('mapping_area_plants')
                                    ->where('plant_id', $id);

        if( $queryMappingAreaPlant->count() > 0 ) {
            $mappingAreaPlant = $queryMappingAreaPlant->first();

            $queryUserArea = DB::table('user_areas')
                                ->where('area_plant_id', $mappingAreaPlant->area_plant_id);

            if($queryUserArea->count() > 0){
                $userArea = $queryUserArea->first();
                $quser = DB::table('users')
                            ->leftJoin('profiles', 'profiles.id', 'users.profile_id')
                            ->select('users.email', 'profiles.name')
                            ->where('users.id', $userArea->user_id);
                $am = '';
                if($quser->count() > 0){
                    $am = $quser->first();
                }
            }
        }

        return $am;
    }

    public static function getRMIdByAm($userID)
    {
        $rm_id = 0;
        $qUserArea = DB::table('user_areas')
                        ->where('user_id', $userID);

        if( $qUserArea->count() > 0 ) {
            $userArea = $qUserArea->first();

            $queryMappingRegionalArea = DB::table('mapping_regional_areas')
                                            ->where('area_plant_id', $userArea->area_plant_id);

            if($queryMappingRegionalArea->count() > 0 ) {
                $mappingRegionalArea = $queryMappingRegionalArea->first();

                $queryUserRegional = DB::table('user_regionals')
                                    ->where('regional_plant_id', $mappingRegionalArea->regional_plant_id);

                if ($queryUserRegional->count() > 0) {
                    $userRegional = $queryUserRegional->first();
                    $rm_id = $userRegional->user_id;
                }

            }

        }

        return $rm_id;
    }

    public static function getRMIdPlantById($id)
    {
        $rm_id = 0;
        $queryMappingAreaPlant = DB::table('mapping_area_plants')
                                    ->where('plant_id', $id);

        if( $queryMappingAreaPlant->count() > 0 ) {
            $mappingAreaPlant = $queryMappingAreaPlant->first();

            $queryMappingRegionalArea = DB::table('mapping_regional_areas')
                                            ->where('area_plant_id', $mappingAreaPlant->area_plant_id);

            if($queryMappingRegionalArea->count() > 0 ) {
                $mappingRegionalArea = $queryMappingRegionalArea->first();

                $queryUserRegional = DB::table('user_regionals')
                                    ->where('regional_plant_id', $mappingRegionalArea->regional_plant_id);

                if ($queryUserRegional->count() > 0) {
                    $userRegional = $queryUserRegional->first();
                    $rm_id = $userRegional->user_id;
                }

            }

        }

        return $rm_id;
    }

    public static function getRMNamePlantById($id)
    {
        $rm_name = '-';
        $queryMappingAreaPlant = DB::table('mapping_area_plants')
                                    ->where('plant_id', $id);

        if( $queryMappingAreaPlant->count() > 0 ) {
            $mappingAreaPlant = $queryMappingAreaPlant->first();

            $queryMappingRegionalArea = DB::table('mapping_regional_areas')
                                            ->where('area_plant_id', $mappingAreaPlant->area_plant_id);

            if($queryMappingRegionalArea->count() > 0 ) {
                $mappingRegionalArea = $queryMappingRegionalArea->first();

                $queryUserRegional = DB::table('user_regionals')
                                    ->where('regional_plant_id', $mappingRegionalArea->regional_plant_id);

                if ($queryUserRegional->count() > 0) {
                    $userRegional = $queryUserRegional->first();
                    $quser = DB::table('users')
                                ->leftJoin('profiles', 'profiles.id', 'users.profile_id')
                                ->select('profiles.name')
                                ->where('users.id', $userRegional->user_id);

                    $rm_name = '';
                    if ($quser->count() > 0) {
                        $user = $quser->first();
                        $rm_name = $user->name;
                    }

                }

            }

        }

        return $rm_name;
    }

    public static function getMODIdPlantById($id)
    {
        $mod_id = 0;

        $queryUserPlant = DB::table('user_plants')
                            ->join('model_has_roles', 'model_has_roles.model_id', 'user_plants.user_id')
                            ->join('roles', 'roles.id', 'model_has_roles.role_id')
                            ->join('users', 'users.id', 'user_plants.user_id')
                            ->where('user_plants.plant_id', $id)
                            ->where('roles.name', 'store manager')
                            ->where('users.status', 2)
                            ->select('user_plants.user_id');

        if( $queryUserPlant->count() > 0 ) {
            $userPlant = $queryUserPlant->first();

            $mod_id = $userPlant->user_id;
        }

        return $mod_id;
    }

    public static function getMODNamePlantById($id)
    {
        $mod_name = '-';

        $queryUserPlant = DB::table('user_plants')
                            ->join('model_has_roles', 'model_has_roles.model_id', 'user_plants.user_id')
                            ->join('roles', 'roles.id', 'model_has_roles.role_id')
                            ->join('users', 'users.id', 'user_plants.user_id')
                            ->where('user_plants.plant_id', $id)
                            ->where('roles.name', 'store manager')
                            ->where('users.status', 2)
                            ->select('user_plants.user_id');

        if( $queryUserPlant->count() > 0 ) {
            $userPlant = $queryUserPlant->first();

            $quser = DB::table('users')
                        ->leftJoin('profiles', 'profiles.id', 'users.profile_id')
                        ->select('profiles.name')
                        ->where('users.id', $userPlant->user_id);

            $mod_name = '';
            if ($quser->count() > 0) {
                $user = $quser->first();
                $mod_name = $user->name;
            }

        }

        return $mod_name;
    }

    public static function getListStoreVtec()
    {
        $list = DB::table('plants')
                    ->where('pos', 2)
                    ->pluck('id');

        return $list;
    }

    public static function getListStore($companyId, $pos = '0')
    {
        $list = DB::table('plants')
                    ->whereNotNull('pos_id')
                    ->where('type', 1);

        if($pos != '0'){
            $list = $list->where('pos_id', $pos);
        }

        $list = $list->pluck('id');

        return $list;
    }

    public static function getTypeByPlantId($id)
    {
        $query = DB::table('plants')
                    ->select('type')
                    ->where('id', $id);

        $type = '';

        if($query->count() > 0){
            $plant = $query->first();
            if($plant->type != '1'){
                $type = 'DC';
            } else {
                $type = 'Outlet';
            }

        }

        return $type;
    }

    public static function getTypeIdByPlantId($id)
    {
        $query = DB::table('plants')
                    ->select('type')
                    ->where('id', $id);

        $typeId = 0;

        if($query->count() > 0){
            $plant = $query->first();
            $typeId = $plant->type;
        }

        return $typeId;
    }

    public static function getCompanyIdByPlantId($id)
    {
        $query = DB::table('plants')
                    ->select('company_id')
                    ->where('id', $id);

        $companyId = '';

        if($query->count() > 0){
            $plant = $query->first();
            $companyId = $plant->company_id;
        }

        return $companyId;
    }

    public static function getSlocIdWaste($id)
    {
        $query = DB::table('plants')
                    ->select('sloc_id_waste')
                    ->where('id', $id);

        $slocIdWaste = '';

        if($query->count() > 0){
            $plant = $query->first();
            $slocIdWaste = $plant->sloc_id_waste;
        }

        return $slocIdWaste;
    }

    public static function getSlocIdCurStock($id)
    {
        $query = DB::table('plants')
                    ->select('sloc_id_current_stock')
                    ->where('id', $id);

        $slocIdCurStock = '';

        if($query->count() > 0){
            $plant = $query->first();
            $slocIdCurStock = $plant->sloc_id_current_stock;
        }

        return $slocIdCurStock;
    }

    public static function getSlocIdOpname($id)
    {
        $query = DB::table('plants')
                    ->select('sloc_id_opname')
                    ->where('id', $id);

        $slocIdOpname = '';

        if($query->count() > 0){
            $plant = $query->first();
            $slocIdOpname = $plant->sloc_id_opname;
        }

        return $slocIdOpname;
    }

    public static function getSlocIdGrVendor($id)
    {
        $query = DB::table('plants')
                    ->select('sloc_id_gr_vendor')
                    ->where('id', $id);

        $slocIdGrVendor = '';

        if($query->count() > 0){
            $plant = $query->first();
            $slocIdGrVendor = $plant->sloc_id_gr_vendor;
        }

        return $slocIdGrVendor;
    }

    public static function getSlocIdGr($id)
    {
        $query = DB::table('plants')
                    ->select('sloc_id_gr')
                    ->where('id', $id);

        $slocIdGr = '';

        if($query->count() > 0){
            $plant = $query->first();
            $slocIdGr = $plant->sloc_id_gr;
        }

        return $slocIdGr;
    }

    public static function getSlocIdAssetMutation($id)
    {
        $query = DB::table('plants')
                    ->select('sloc_id_asset_mutation')
                    ->where('id', $id);

        $slocIdAssetMutation = '';

        if($query->count() > 0){
            $plant = $query->first();
            $slocIdAssetMutation = $plant->sloc_id_asset_mutation;
        }

        return $slocIdAssetMutation;
    }

    public static function getSlocIdGiPlant($id)
    {
        $query = DB::table('plants')
                    ->select('sloc_id_gi_plant')
                    ->where('id', $id);

        $slocIdGiPlant = '';

        if($query->count() > 0){
            $plant = $query->first();
            $slocIdGiPlant = $plant->sloc_id_gi_plant;
        }

        return $slocIdGiPlant;
    }

    public static function getTypePlant($plant){
        return ($plant[0] != 'R') ? 'Outlet' : 'DC';
    }

    public static function getInitialPlant($plant){
        return ($plant[0] != 'R') ? 'RF' : 'DC';
    }

    public static function cleanInisialPlant($plant){
        return Str::of($plant)->replace('Richeese Factory ', '')->replace('Plant ', '')->replace('DC ', '')->replace('Richeese Factory', '');
    }
}
