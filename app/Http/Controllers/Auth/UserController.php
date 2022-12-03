<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Library\Helper;
use Yajra\DataTables\DataTables;

use App\Models\User;
use App\Models\Profile;
use App\Models\UserPlant;
use App\Models\UserArea;
use App\Models\UserRegional;
use App\Models\Configuration;
use App\Models\Company;

use App\Rules\CheckHashPass;

class UserController extends Controller
{
    public function index(Request $request){
        $dataview = [
            'menu_id' => $request->query('menuid'),
            'role_am' => Configuration::getValueByKeyFor('general_master', 'role_am'),
            'role_rm' => Configuration::getValueByKeyFor('general_master', 'role_rm'),
            'role_sm' => Configuration::getValueByKeyFor('general_master', 'role_sm'),
            'role_sc' => Configuration::getValueByKeyFor('general_master', 'role_sc'),
        ];

        return view('application.authentication.user', $dataview)->render();
    }

    public function dtble(Request $request)
    {
        $query = DB::table('users')
                    ->join('profiles', 'profiles.id', '=', 'users.profile_id')
                    ->join('languanges', 'languanges.id', '=', 'users.languange_id')
                    ->join('countries', 'countries.id', '=', 'profiles.country_id')
                    ->join('departments', 'departments.id', '=', 'profiles.department_id')
                    ->join('positions', 'positions.id', '=', 'profiles.position_id')
                    ->select(['users.id', 'profiles.name as profile_name', 'users.email', 'profiles.phone', 'users.status', 'users.created_by', 'users.last_login', 'languanges.lang', 'profiles.company_id', 'countries.name as country_name', 'users.created_at', 'profiles.country_id', 'profiles.company_id', 'profiles.department_id', 'departments.name as department_name', 'profiles.position_id', 'positions.name as position_name', 'users.languange_id', 'users.status as status_id', 'users.password', 'profiles.work_at' ]);

        if($request->has('company') ) {
            if( !in_array($request->query('company'), ['0', null]) ){
                $query = $query->whereIn('profiles.company_id', [0, $request->query('company')]);
            }
        }

        return Datatables::of($query)
                        ->addIndexColumn()
                        ->filterColumn('profile_name', function($query, $keyword) {
                                $sql = "LOWER(profiles).name like ?";
                                $query->whereRaw($sql, ["%{$keyword}%"]);
                            })
                        ->filterColumn('country_name', function($query, $keyword) {
                                $sql = "LOWER(countries.name) like ?";
                                $query->whereRaw($sql, ["%{$keyword}%"]);
                            })
                        ->filterColumn('department_name', function($query, $keyword) {
                                $sql = "LOWER(departments.name) like ?";
                                $query->whereRaw($sql, ["%{$keyword}%"]);
                            })
                        ->filterColumn('position_name', function($query, $keyword) {
                                $sql = "LOWER(positions.name) like ?";
                                $query->whereRaw($sql, ["%{$keyword}%"]);
                            })
                        ->addColumn('role', '{{ \App\Http\Controllers\Application\Authentication\RoleController::getRole($id) }}')
                        ->addColumn('role_id', '{{ \App\Http\Controllers\Application\Authentication\RoleController::getRoleId($id) }}')
                        ->addColumn('authorize_role', '{{ \App\Http\Controllers\Application\Authentication\RoleController::getAuthorizeRole($id) }}')
                        ->addColumn('plant_id', '{{ \App\Models\Plant::getPlantIdByUserId($id) }}')
                        ->addColumn('plant_name', '{{ \App\Models\Plant::getPlantsNameByUserId($id) }}')
                        ->addColumn('area_plant_id', '{{ \App\Models\AreaPlant::getAreaPlantIdByUserId($id) }}')
                        ->addColumn('area_plant_name', '{{ \App\Models\AreaPlant::getAreaPlantNameByUserId($id) }}')
                        ->addColumn('regional_plant_id', '{{ \App\Models\RegionalPlant::getRegionalPlantIdByUserId($id) }}')
                        ->addColumn('regional_plant_name', '{{ \App\Models\RegionalPlant::getRegionalPlantNameByUserId($id) }}')
                        ->addColumn('work_at_desc', function ($data) {
                            $work_at_desc = "";
                            switch ($data->work_at) {
                                case '0':
                                    $work_at_desc = "Back Office";
                                    break;
                                case '1':
                                    $work_at_desc = "Outlet";
                                    break;
                                case '2':
                                    $work_at_desc = "DC";
                                    break;
                            }
                            return $work_at_desc;
                        })
                        ->addColumn('company_name', function ($data) {
                            $company_name = "";
                            switch ($data->company_id) {
                                case '0':
                                    $company_name = Lang::get('All');
                                    break;
                                default:
                                    $company_name = Company::getNameById($data->company_id);
                            }
                            return $company_name;
                        })
                        ->editColumn('status', function ($data) {
                            $status = $data->status;
                            switch ($data->status) {
                                case '0':
                                    $status = "Blocked";
                                    break;
                                case '1':
                                    $status = "Unactive";
                                    break;
                                case '2':
                                    $status = "Active";
                                    break;
                            }
                            return $status;
                        })
                        ->make();
    }

    public function select(Request $request){
        $userAuth = $request->get('userAuth');

        $query = DB::table('users')
                    ->leftJoin('profiles', 'profiles.id', '=', 'users.profile_id')
                    ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                    ->whereIn('company_id', [0, $userAuth->company_id_selected])
                    ->select(['users.id', 'profiles.name as text']);

        $roleSc = Configuration::getValueByKeyFor('general_master', 'role_sc');

        if ($request->has('crew')) {
            $query->where('model_has_roles.role_id', $roleSc);
        }

        if ($request->has('search')) {
            $query->whereRaw("LOWER(profiles.name) like '%" . strtolower($request->search) . "%'");
        }

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        if ( ($request->query('init') == 'false' && !$request->has('search')) || empty($request->query('search'))) {
            $data = [];
        } else {
            $data = $query->get();
        }

        if ($request->has('ext')) {
            if ($request->query('ext') == 'all') {
                if (!is_array($data)) {
                    $data->prepend(['id' => 0, 'text' => Lang::get('All')]);
                }
            }
        }

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
                        'name' => 'required',
                        'country'=> 'required',
                        'company' => 'required',
                        'department' => 'required',
                        'position' => 'required',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required',
                        'role' => 'required',
                        'language' => 'required',
                        'status' => 'required',
                        'work_at' => 'required',
                    ]);

        // validation for area, regional, plant input
        $role_am = Configuration::getValueByKeyFor('general_master', 'role_am');
        $role_rm = Configuration::getValueByKeyFor('general_master', 'role_rm');
        $role_sm = Configuration::getValueByKeyFor('general_master', 'role_sm');
        $role_sc = Configuration::getValueByKeyFor('general_master', 'role_sc');

        $stat = 'warning';
        $msg = '';
        if( $request->role == $role_am && (is_null($request->area) || $request->area == '') ){
            $msg = Lang::get("validation.required", ['attribute' => 'area']);
        }

        if( $request->role == $role_rm && (is_null($request->regional) || $request->regional == '') ){
            $msg = Lang::get("validation.required", ['attribute' => 'regional']);
        }

        if( ($request->role == $role_sm || $request->role == $role_sc) && (is_null($request->plant) || $request->plant == '') ){
            $msg = Lang::get("validation.required", ['attribute' => 'plant']);
        }

        if($msg != ''){
            return response()->json(Helper::resJSON($stat, $msg));
        }

        DB::BeginTransaction();

        $userf = DB::table('users')->join('profiles', 'profiles.id', '=', 'users.profile_id')->where('users.id', Auth::id())->select('profiles.name')->first();

        $profile = new Profile;
        $profile->name = $request->name;
        $profile->phone = $request->phone;
        $profile->work_at = $request->work_at;
        $profile->company_id = $request->company;
        $profile->country_id = $request->country;
        $profile->department_id = $request->department;
        $profile->position_id = $request->position;
        if ($profile->save()) {
            $company_id_selected = $request->company;
            if ($request->company == 0) {
                $company_id_selected = Company::getFirstCompanyIdSelect();
            }

            $user = new User;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->profile_id = $profile->id;
            $user->company_id = $company_id_selected;
            $user->languange_id = $request->language;
            $user->status = $request->status;
            $user->created_by = $userf->name;
            if ($user->save()) {

                // assign role
                $role = DB::table('roles')->where('id', $request->role)->first();
                $user->assignRole($role->name);

                $suc = true;

                if ($request->role == $role_am) {
                    // role am
                    $userArea = new UserArea;
                    $userArea->user_id = $user->id;
                    $userArea->area_plant_id = $request->area;
                    if (!$userArea->save()) {
                        $suc = false;
                    }
                } else if ($request->role == $role_rm) {
                    // role rm
                    $userRegional = new UserRegional;
                    $userRegional->user_id = $user->id;
                    $userRegional->regional_plant_id = $request->regional;
                    if (!$userRegional->save()) {
                        $suc = false;
                    }
                } else if ( $request->role == $role_sm || $request->role == $role_sc ) {
                    // role store manager / crew
                    $userPlant = new UserPlant;
                    $userPlant->user_id = $user->id;
                    $userPlant->plant_id = $request->plant;
                    if (!$userPlant->save()) {
                        $suc = false;
                    }
                } else {
                    $userPlant = new UserPlant;
                    $userPlant->user_id = $user->id;
                    $userPlant->plant_id = 0;
                    if (!$userPlant->save()) {
                        $suc = false;
                    }
                }

                if($suc){
                    DB::commit();
                    $stat = 'success';
                    $msg = Lang::get("message.save.success", ["data" => Lang::get("user")]);
                }else{
                    DB::rollBack();
                    $stat = 'failed';
                    $msg = Lang::get("message.save.failed", ["data" => Lang::get("user")]);
                }
            }else{
                DB::rollBack();
                $stat = 'failed';
                $msg = Lang::get("message.save.failed", ["data" => Lang::get("user")]);
            }
        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.save.failed", ["data" => Lang::get("user")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                        'name' => 'required',
                        'country'=> 'required',
                        'company' => 'required',
                        'department' => 'required',
                        'position' => 'required',
                        'email' => 'required|email',
                        'password' => 'required',
                        'role' => 'required',
                        'language' => 'required',
                        'status' => 'required',
                        'work_at' => 'required',
                    ]);

        // validation for area, regional, plant input
        $role_am = Configuration::getValueByKeyFor('general_master', 'role_am');
        $role_rm = Configuration::getValueByKeyFor('general_master', 'role_rm');
        $role_sm = Configuration::getValueByKeyFor('general_master', 'role_sm');
        $role_sc = Configuration::getValueByKeyFor('general_master', 'role_sc');

        $stat = 'warning';
        $msg = '';

        if ($request->role == $role_am && (is_null($request->area) || $request->area == '')) {
            $msg = Lang::get("validation.required", ['attribute' => 'area']);
        }

        if ($request->role == $role_rm && (is_null($request->regional) || $request->regional == '')) {
            $msg = Lang::get("validation.required", ['attribute' => 'regional']);
        }

        if (($request->role == $role_sm || $request->role == $role_sc) && (is_null($request->plant) || $request->plant == '')) {
            $msg = Lang::get("validation.required", ['attribute' => 'plant']);
        }

        if ($msg != '') {
            return response()->json(Helper::resJSON($stat, $msg));
        }

        DB::BeginTransaction();

        $user = User::find($request->id);

        $profile = Profile::find($user->profile_id);
        $profile->name = $request->name;
        $profile->phone = $request->phone;
        $profile->work_at = $request->work_at;
        $profile->company_id = $request->company;
        $profile->country_id = $request->country;
        $profile->department_id = $request->department;
        $profile->position_id = $request->position;
        if ($profile->save()) {

            if($user->password != $request->password ){
                $password = Hash::make($request->password);
            }else{
                $password = $request->password;
            }

            $user->email = $request->email;
            $user->password = $password;
            $user->profile_id = $profile->id;
            $user->languange_id = $request->language;
            $user->status = $request->status;
            if ($user->save()) {

                // remove role
                $roles = $user->getRoleNames();
                foreach ($roles as $role) {
                    $user->removeRole($role);
                }

                // assign role
                $role = DB::table('roles')->where('id', $request->role)->first();
                $user->assignRole($role->name);

                // remove all mapping user, area, regional
                DB::table('user_plants')->where('user_id', $request->id)->delete();
                DB::table('user_areas')->where('user_id', $request->id)->delete();
                DB::table('user_regionals')->where('user_id', $request->id)->delete();

                // insert user plant
                $suc = true;

                if ($request->role == $role_am) {
                    // role am
                    $userArea = new UserArea;
                    $userArea->user_id = $user->id;
                    $userArea->area_plant_id = $request->area;
                    if (!$userArea->save()) {
                        $suc = false;
                    }
                } else if ($request->role == $role_rm) {
                    // role rm
                    $userRegional = new UserRegional;
                    $userRegional->user_id = $user->id;
                    $userRegional->regional_plant_id = $request->regional;
                    if (!$userRegional->save()) {
                        $suc = false;
                    }
                } else if ($request->role == $role_sm || $request->role == $role_sc) {
                    // role store manager / crew
                    $userPlant = new UserPlant;
                    $userPlant->user_id = $user->id;
                    $userPlant->plant_id = $request->plant;
                    if (!$userPlant->save()) {
                        $suc = false;
                    }
                } else {
                    $userPlant = new UserPlant;
                    $userPlant->user_id = $user->id;
                    $userPlant->plant_id = 0;
                    if (!$userPlant->save()) {
                        $suc = false;
                    }
                }

                if($suc){
                    DB::commit();
                    $stat = 'success';
                    $msg = Lang::get("message.save.success", ["data" => Lang::get("user")]);
                }else{
                    DB::rollBack();
                    $stat = 'failed';
                    $msg = Lang::get("message.save.failed", ["data" => Lang::get("user")]);
                }
            }else{
                DB::rollBack();
                $stat = 'failed';
                $msg = Lang::get("message.save.failed", ["data" => Lang::get("user")]);
            }

        } else {
            DB::rollBack();
            $stat = 'failed';
            $msg = Lang::get("message.update.failed", ["data" => Lang::get("user")]);
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function destroy($id)
    {
        //
    }

    // utility

    public function changePassword(Request $request)
    {
        $request->validate([
                            'old_password' => ['required', new CheckHashPass],
                            'new_password' => 'required|confirmed',
                        ]);

        $user = User::find(Auth::id());
        $user->password = Hash::make($request->new_password);
        if ($user->save()) {
            $stat = 'success';
            $msg = Lang::get("message.change_password.success");
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.change_password.failed");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function changePasswordMonthly(Request $request)
    {
        $request->validate([
                            'old_password' => ['required', new CheckHashPass],
                            'new_password' => 'required|confirmed',
                        ]);

        $user = User::find(Auth::id());
        $user->password = Hash::make($request->new_password);
        $user->flag_change_pass = 0;
        if ($user->save()) {
            $stat = 'success';
            $msg = Lang::get("message.change_password_monthly.success");
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.change_password_monthly.failed");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }

    public function changeProfile(Request $request)
    {
        $request->validate([
                            'name' => 'required',
                            'phone' => 'required',
                        ]);

        $user = User::find(Auth::id());

        $profile = Profile::find($user->profile_id);
        $profile->name = $request->name;
        $profile->phone = $request->phone;
        if ($profile->save()) {
            Cache::forget('profile_by_user_id_'. $user->id);

            $stat = 'success';
            $msg = Lang::get("message.change_profile.success");
        } else {
            $stat = 'failed';
            $msg = Lang::get("message.change_profile.failed");
        }

        return response()->json( Helper::resJSON( $stat, $msg ) );
    }
}
