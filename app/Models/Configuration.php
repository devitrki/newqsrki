<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Configuration extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function configurationGroup()
    {
        return $this->belongsTo(ConfigurationGroup::class);
    }

    public static function getConfigurationByFor($for)
    {
        $configurations = Configuration::select('key', 'value')->where('for', $for)->get();
        $app = [];
        foreach ($configurations as $apps) {
            $app[ $apps->key ] = $apps->value;
        }
        return $app;
    }

    public static function getValueByKeyFor($for, $key)
    {
        $configurations = Configuration::select('value')->where('for', $for)->where('key', $key);
        $value = '';
        if($configurations->count() > 0){
            $configurations = $configurations->first();
            $value = $configurations->value;
        }
        return $value;
    }

    public static function setValueByKeyFor($for, $key, $value)
    {
        $configurations = Configuration::where('for', $for)->where('key', $key)->first();
        $configurations->value = $value;
        $configurations->save();
    }

    // configuration level company
    public static function getConfigurationCompByFor($companyId, $for)
    {
        $configurations = Cache::rememberForever('configuration_comp_' . $for . $companyId , function () use ($companyId, $for) {
            $configurations = Configuration::select('key', 'value')
                                ->where('company_id', $companyId)
                                ->where('for', $for)
                                ->get();
            $conf = [];
            foreach ($configurations as $configuration) {
                $conf[ $configuration->key ] = $configuration->value;
            }

            return $conf;
        });

        return $configurations;
    }

    public static function getValueCompByKeyFor($companyId, $for, $key)
    {
        $configurations = Configuration::getConfigurationCompByFor($companyId, $for);
        $value = '';

        foreach ($configurations as $k => $v) {
            if ($k == $key) {
                $value = $v;
                break;
            }
        }

        return $value;
    }

    public static function setValueCompByKeyFor($companyId, $for, $key, $value)
    {
        $configurations = Configuration::where('for', $for)
                            ->where('company_id', $companyId)
                            ->where('for', $for)
                            ->where('key', $key)
                            ->first();

        $configurations->value = $value;
        $configurations->save();
    }
}
