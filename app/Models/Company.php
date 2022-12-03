<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use OwenIt\Auditing\Contracts\Auditable;

class Company extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function area_plants()
    {
        return $this->hasMany(AreaPlant::class);
    }

    public function regional_plants()
    {
        return $this->hasMany(RegionalPlant::class);
    }

    public static function getCompaniesUser($user)
    {
        $companiesUser = Cache::rememberForever('company_by_user_id_' . $user->id, function () use ($user) {
            $userCompanySelected = DB::table('companies')
                                    ->where('id', $user->company_id_selected)
                                    ->select('id', 'name')
                                    ->first();

            if ($user->company_id != 0) {
                $userCompanyOptions = DB::table('companies')
                                        ->where('id', $user->company_id)
                                        ->select('id', 'name')
                                        ->get();
            } else {
                $userCompanyOptions = DB::table('companies')
                                        ->select('id', 'name')
                                        ->get();
            }

            $userCompanies = [
                'selected' => $userCompanySelected,
                'options' => $userCompanyOptions
            ];

            return $userCompanies;
        });

        return $companiesUser;
    }

    public static function getSapById($id)
    {
        return Company::where('id', $id)->select('sap')->first();
    }

    public static function getFirstCompanyIdSelect()
    {
        $company = DB::table('companies')->select('id')->first();
        return $company->id;
    }

    public static function getNameById($id)
    {
        $name = '';
        $query = DB::table('companies')
                    ->where('id', $id)
                    ->select('name');

        if($query->count()){
            $data = $query->first();
            $name = $data->name;
        }

        return $name;
    }

    public static function getConfigByKey($companyId, $key)
    {
        $value = '';
        $configurations = self::getConfigs($companyId);
        $configurations = json_decode($configurations);
        foreach ($configurations as $k => $v) {
            if ($key == $k) {
                $value = $v;
            }
        }

        return $value;
    }

    public static function getConfigs($companyId)
    {
        $configurations = Cache::rememberForever('company_configuration_id_' . $companyId, function () use ($companyId) {
            $company = DB::table('companies')
                        ->where('id', $companyId)
                        ->select('configurations')
                        ->first();

            return $company->configurations;
        });

        return $configurations;
    }

}
