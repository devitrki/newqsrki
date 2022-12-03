<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Auth\Menu;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permission by menu created
        $menus = Menu::all();
        foreach ($menus as $menu) {
            if( $menu->permission_menu != '' ){
                $permissions = explode(",", $menu->permission_menu);
                foreach ($permissions as $permission) {
                    Permission::create(['name' => strtolower($permission) . $menu->id ]);
                }
            }
        }

        // create role super-admin
        $roleSuperAdmin = Role::create(['name' => 'superadmin']);
        $role = Role::create(['name' => 'area manager']);
        $role = Role::create(['name' => 'store manager']);
        $role = Role::create(['name' => 'regional manager']);
        $role = Role::create(['name' => 'store crew']);
        $role = Role::create(['name' => 'finance staff']);
        $role = Role::create(['name' => 'pettycash staff']);
        $role = Role::create(['name' => 'purchasing staff']);
        $role = Role::create(['name' => 'co staff']);
        $role = Role::create(['name' => 'co supervisor']);

        // assign all permission to role super-admin
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // commit transaction
        DB::commit();
    }
}
