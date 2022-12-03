<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, \OwenIt\Auditing\Auditable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function languange()
    {
        return $this->belongsTo(Languange::class);
    }

    public function userPlants()
    {
        return $this->hasMany(UserPlant::class);
    }

    public function Downloads()
    {
        return $this->hasMany(Download::class);
    }

    // utility
    public static function getUsersIdByPlantId($plants)
    {
        if( in_array('0', $plants) ){
            // have all outlets
            $users_crew = DB::table('user_plants')
                            ->leftJoin('users', 'users.id', '=', 'user_plants.user_id')
                            ->leftJoin('profiles', 'profiles.id', '=', 'users.profile_id')
                            ->leftJoin('positions', 'positions.id', '=', 'profiles.position_id')
                            ->where('positions.name', 'crew outlet')
                            ->select('user_plants.user_id')
                            ->distinct()
                            ->get();
        }else{
            $users_crew = DB::table('user_plants')
                            ->leftJoin('users', 'users.id', '=', 'user_plants.user_id')
                            ->leftJoin('profiles', 'profiles.id', '=', 'users.profile_id')
                            ->leftJoin('positions', 'positions.id', '=', 'profiles.position_id')
                            ->where('positions.name', 'crew outlet')
                            ->whereIn('plant_id', $plants)
                            ->select('user_plants.user_id')
                            ->distinct()
                            ->get();
        }
        $users = [];
        foreach ($users_crew as $v) {
            $users[] = $v->user_id;
        }
        return $users;
    }

    public static function getLanguageByUserId($user_id){
        $user = DB::table('users')
                    ->where('id', $user_id)
                    ->select('languange_id')
                    ->first();
        return $user->languange_id;
    }

    public static function getRoleIdById($id){
        $user = DB::table('model_has_roles')
                    ->where('model_id', $id)
                    ->select('role_id')
                    ->first();

        return $user->role_id;
    }

    public static function getNameById($id){
        $name = '';

        $user = DB::table('users')
                    ->leftJoin('profiles', 'profiles.id', 'users.profile_id')
                    ->where('users.id', $id)
                    ->select('profiles.name');

        if($user->count() > 0){
            $data = $user->first();
            $name = $data->name;
        }

        return $name;
    }

    public static function getEmailById($id){
        $email = '';

        $user = DB::table('users')
                    ->where('id', $id)
                    ->select('email');

        if($user->count() > 0){
            $data = $user->first();
            $email = $data->email;
        }

        return $email;
    }

    public static function getPositionById($id){
        $name = '';

        $user = DB::table('users')
                    ->leftJoin('profiles', 'profiles.id', 'users.profile_id')
                    ->leftJoin('positions', 'positions.id', 'profiles.position_id')
                    ->where('users.id', $id)
                    ->select('positions.name');

        if($user->count() > 0){
            $data = $user->first();
            $name = $data->name;
        }

        return $name;
    }

    public static function getDepartmentById($id){
        $name = '';

        $user = DB::table('users')
                    ->leftJoin('profiles', 'profiles.id', 'users.profile_id')
                    ->leftJoin('departments', 'departments.id', 'profiles.department_id')
                    ->where('users.id', $id)
                    ->select('departments.name');

        if($user->count() > 0){
            $data = $user->first();
            $name = $data->name;
        }

        return $name;
    }

    public static function getDepartmentIdById($id){
        $department_id = '0';

        $user = DB::table('users')
                    ->leftJoin('profiles', 'profiles.id', 'users.profile_id')
                    ->where('users.id', $id)
                    ->select('profiles.department_id');

        if($user->count() > 0){
            $data = $user->first();
            $department_id = $data->department_id;
        }

        return $department_id;
    }

    public static function getHodIdByDepartmentId($departmentId){
        $hodId = '0';

        $qHODposition = DB::table('positions')
                            ->where('name', 'Head of Department')
                            ->select('id');

        if( $qHODposition->count() > 0 ){
            $hodPosition = $qHODposition->first();

            $user = DB::table('users')
                        ->leftJoin('profiles', 'profiles.id', 'users.profile_id')
                        ->where('profiles.department_id', $departmentId)
                        ->where('profiles.position_id', $hodPosition->id)
                        ->select('users.id');

            if($user->count() > 0){
                $data = $user->first();
                $hodId = $data->id;
            }
        }

        return $hodId;
    }
}
