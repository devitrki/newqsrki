<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;

class Languange extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function getLanguageByUserId($user_id){
        return DB::table('users')
                    ->leftJoin('languanges', 'users.languange_id', '=', 'languanges.id')
                    ->where('users.id', $user_id)
                    ->select('languanges.*')
                    ->first();
    }
}
