<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Profile extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // utility
    public static function getProfileByUserId($user_id)
    {
        $profile_by_user_id = Cache::rememberForever('profile_by_user_id_' . $user_id, function () use ($user_id) {
            return DB::table('users')
                    ->leftJoin('profiles', 'profiles.id', '=', 'users.profile_id')
                    ->select('profiles.*', 'users.email', 'users.flag_change_pass', DB::raw('users.company_id as company_id_selected'))
                    ->where('users.id', $user_id)
                    ->first();
        });

        return $profile_by_user_id;
    }
}
