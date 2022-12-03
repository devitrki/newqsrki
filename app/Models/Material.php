<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Material extends Model
{
    use HasFactory;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function material_convertions()
    {
        return $this->hasMany(MaterialConvertion::class);
    }

    public static function getIdByCode($code)
    {
        $material = DB::table('materials')->where('code', $code)->select('id')->first();
        $id = '';
        if (isset($material->id)) {
            $id = $material->id;
        }
        return $id;
    }

    public static function getDescByCode($code)
    {
        $material = DB::table('materials')->where('code', $code)->select('description')->first();
        $description = '';
        if (isset($material->description)) {
            $description = $material->description;
        }
        return $description;
    }
}
