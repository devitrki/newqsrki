<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Auth\PermissionList;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionList = new PermissionList;
        $permissionList->name = 'Read';
        $permissionList->short_name = 'r';
        $permissionList->save();

        $permissionList = new PermissionList;
        $permissionList->name = 'Create';
        $permissionList->short_name = 'c';
        $permissionList->save();

        $permissionList = new PermissionList;
        $permissionList->name = 'Update';
        $permissionList->short_name = 'u';
        $permissionList->save();

        $permissionList = new PermissionList;
        $permissionList->name = 'Delete';
        $permissionList->short_name = 'd';
        $permissionList->save();

        $permissionList = new PermissionList;
        $permissionList->name = 'Approve';
        $permissionList->short_name = 'a';
        $permissionList->save();

        $permissionList = new PermissionList;
        $permissionList->name = 'Unapprove';
        $permissionList->short_name = 'ua';
        $permissionList->save();
    }
}
