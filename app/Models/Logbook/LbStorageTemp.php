<?php

namespace App\Models\Logbook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class LbStorageTemp extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public static function getFirstId($companyId)
    {
        $storage = DB::table('lb_storage_temps')
                        ->where('company_id', $companyId)
                        ->select('id')
                        ->first();
        return $storage->id;
    }

    public static function getNameById($id)
    {
        $name = '';

        $query = DB::table('lb_storage_temps')->where('id', $id)->select('name');
        if($query->count() > 0){
            $data = $query->first();
            $name = $data->name;
        }

        return $name;
    }
}
