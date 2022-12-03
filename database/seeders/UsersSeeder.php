<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Country;
use App\Models\Company;
use App\Models\Profile;
use App\Models\UserPlant;
use App\Models\Auth\Languange;
use App\Models\Auth\Menu;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        // create company RKI
        $company_rki = new Company;
        $company_rki->name = "RKI Indonesia";
        $company_rki->code = "rki";
        $company_rki->configurations = '{"sap_code":"RKI"}';
        $company_rki->save();

        $company = new Company;
        $company->name = "RKI Malaysia";
        $company->code = "rsh";
        $company->configurations = '{"sap_code":""}';
        $company->save();

        // create department IT
        $department = new Department;
        $department->name = "IT";
        $department->save();

        // create position IT
        $position = new Position;
        $position->name = "Data Application";
        $position->save();

        // create country
        $countryIndonesia = new Country;
        $countryIndonesia->name = "Indonesia";
        $countryIndonesia->save();

        $country = new Country;
        $country->name = "Malaysia";
        $country->save();

        // create languange
        $languange = new Languange;
        $languange->lang = "English";
        $languange->short_lang = "us";
        $languange->save();

        // create profile super-admin
        $profile = new Profile;
        $profile->name = "Super Admin";
        $profile->company_id = 0;
        $profile->country_id = $countryIndonesia->id;
        $profile->position_id = $position->id;
        $profile->department_id = $department->id;
        $profile->save();

        // create user for super-admin
        $user = new User;
        $user->email = 'superadmin@richeesefactory.com';
        $user->password = Hash::make("1");
        $user->profile_id = $profile->id;
        $user->languange_id = $languange->id;
        $user->company_id = $company_rki->id;
        $user->status = 2;
        $user->created_by = 'system';
        $user->save();


        // assign role to user
        $user->assignRole('superadmin');

        // assign all menu to role admin
        $menu = Menu::all();
        foreach ($menu as $v) {
            if( $v->type != 1 ){
                continue;
            }

            DB::table('menu_roles')->insert([
                'menu_id' => $v->id,
                'role_id' => 1
            ]);
        }

        // adding user id plant to user plant
        $userPlant = new UserPlant;
        $userPlant->user_id = $user->id;
        $userPlant->plant_id = 0;
        $userPlant->save();

        // commit transaction
        DB::commit();
    }
}
