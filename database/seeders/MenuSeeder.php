<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Auth\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        // create menu folder 'Application' 1
        $application_module = new Menu;
        $application_module->parent_id = 0; //for root menu
        $application_module->type = 3; // 3 = module
        $application_module->path = "3"; // path menu id for easy get structur
        $application_module->name = "Application";
        $application_module->description = "Menu module application";
        $application_module->sort_order = 1;
        $application_module->flag_end = 0;
        $application_module->save();

        // create menu folder 'Authentication' 2
        $authentication_folder = new Menu;
        $authentication_folder->parent_id = $application_module->id; //for root menu
        $authentication_folder->type = 2; // 2 = folder
        $authentication_folder->path = "1,2"; // path menu id for easy get structur
        $authentication_folder->name = "Authentication";
        $authentication_folder->description = "Menu folder authentication";
        $authentication_folder->sort_order = 1;
        $authentication_folder->flag_end = 0;
        $authentication_folder->save();

        // create menu file 'User' 3
        $user_menu = new Menu;
        $user_menu->parent_id = $authentication_folder->id; //for parent menu
        $user_menu->type = 1; // 1 = file
        $user_menu->path = "1,2,3"; // path menu id for easy get structur
        $user_menu->name = "User";
        $user_menu->description = "Menu for manage user web";
        $user_menu->url = "application/authentication/user";
        $user_menu->permission_menu = "C,R,U,D";
        $user_menu->sort_order = 1;
        $user_menu->flag_end = 1;
        $user_menu->save();

        // create menu file 'Role' 4
        $role_menu = new Menu;
        $role_menu->parent_id = $authentication_folder->id; //for parent menu
        $role_menu->type = 1; // 1 = file
        $role_menu->path = "1,2,4"; // path menu id for easy get structur
        $role_menu->name = "Role";
        $role_menu->description = "Menu for manage role web";
        $role_menu->url = "application/authentication/role";
        $role_menu->permission_menu = "C,R,U,D";
        $role_menu->sort_order = 2;
        $role_menu->flag_end = 1;
        $role_menu->save();

        // create menu file 'Menu' 5
        $menu_menu = new Menu;
        $menu_menu->parent_id = $authentication_folder->id; //for parent menu
        $menu_menu->type = 1; // 1 = file
        $menu_menu->path = "1,2,5"; // path menu id for easy get structur
        $menu_menu->name = "Menu";
        $menu_menu->description = "Menu for manage menu web";
        $menu_menu->url = "application/authentication/menu";
        $menu_menu->permission_menu = "C,R,U,D";
        $menu_menu->sort_order = 3;
        $menu_menu->flag_end = 1;
        $menu_menu->save();

        // create menu file 'Permission' 6
        $permisssion_menu = new Menu;
        $permisssion_menu->parent_id = $authentication_folder->id; //for parent menu
        $permisssion_menu->type = 1; // 1 = file
        $permisssion_menu->path = "1,2,6"; // path menu id for easy get structur
        $permisssion_menu->name = "Permission";
        $permisssion_menu->description = "Menu for manage permission menu web";
        $permisssion_menu->url = "application/authentication/permission";
        $permisssion_menu->permission_menu = "C,R,U,D";
        $permisssion_menu->sort_order = 4;
        $permisssion_menu->flag_end = 1;
        $permisssion_menu->save();

        // create menu file 'Permission Menu' 7
        $permisssion_menu_menu = new Menu;
        $permisssion_menu_menu->parent_id = $authentication_folder->id; //for parent menu
        $permisssion_menu_menu->type = 1; // 1 = file
        $permisssion_menu_menu->path = "1,2,7"; // path menu id for easy get structur
        $permisssion_menu_menu->name = "Permission Menu";
        $permisssion_menu_menu->description = "Menu for manage permission menu";
        $permisssion_menu_menu->url = "application/authentication/permissionmenu";
        $permisssion_menu_menu->permission_menu = "C,R,U,D";
        $permisssion_menu_menu->sort_order = 5;
        $permisssion_menu_menu->flag_end = 1;
        $permisssion_menu_menu->save();

        // create menu file 'Web Configuration' 8
        $web_configuration_menu = new Menu;
        $web_configuration_menu->parent_id = $application_module->id; //for root menu
        $web_configuration_menu->type = 1; // 1 = file
        $web_configuration_menu->path = "1,8"; // path menu id for easy get structur
        $web_configuration_menu->name = "Web Configuration";
        $web_configuration_menu->description = "Menu for manage fiture web";
        $web_configuration_menu->url = "application/web-configuration";
        $web_configuration_menu->permission_menu = "C,R,U,D";
        $web_configuration_menu->sort_order = 3;
        $web_configuration_menu->flag_end = 1;
        $web_configuration_menu->save();

        // create menu file 'Notification System' 9
        $notification_system_menu = new Menu;
        $notification_system_menu->parent_id = $application_module->id; //for root menu
        $notification_system_menu->type = 1; // 1 = file
        $notification_system_menu->path = "1,9"; // path menu id for easy get structur
        $notification_system_menu->name = "Notification System";
        $notification_system_menu->description = "Menu for manage notification system";
        $notification_system_menu->url = "application/notification-system";
        $notification_system_menu->permission_menu = "C,R,U,D";
        $notification_system_menu->sort_order = 4;
        $notification_system_menu->flag_end = 1;
        $notification_system_menu->save();

        // create menu file 'Download' 10
        $download_menu = new Menu;
        $download_menu->parent_id = $application_module->id; //for root menu
        $download_menu->type = 1; // 1 = file
        $download_menu->path = "1,10"; // path menu id for easy get structur
        $download_menu->name = "Download";
        $download_menu->description = "Menu for get result download from apps";
        $download_menu->url = "application/download";
        $download_menu->permission_menu = "C,R,U,D";
        $download_menu->sort_order = 5;
        $download_menu->flag_end = 1;
        $download_menu->save();

        // create menu folder 'General Master' 11
        $general_master_module = new Menu;
        $general_master_module->parent_id = 0; //for root menu
        $general_master_module->type = 3; // 3 = module
        $general_master_module->path = "11"; // path menu id for easy get structur
        $general_master_module->name = "General Master";
        $general_master_module->description = "Menu module general master";
        $general_master_module->sort_order = 2;
        $general_master_module->flag_end = 0;
        $general_master_module->save();

        // create menu file 'Company' 12
        $company_menu = new Menu;
        $company_menu->parent_id = $general_master_module->id; //for root menu
        $company_menu->type = 1; // 1 = file
        $company_menu->path = "11,12"; // path menu id for easy get structur
        $company_menu->name = "Company";
        $company_menu->description = "Menu for manage data company";
        $company_menu->url = "master/company";
        $company_menu->permission_menu = "C,R,U,D";
        $company_menu->sort_order = 1;
        $company_menu->flag_end = 1;
        $company_menu->save();

        // create menu file 'Country' 13
        $country_menu = new Menu;
        $country_menu->parent_id = $general_master_module->id; //for root menu
        $country_menu->type = 1; // 1 = file
        $country_menu->path = "11,13"; // path menu id for easy get structur
        $country_menu->name = "Country";
        $country_menu->description = "Menu for manage data country";
        $country_menu->url = "master/country";
        $country_menu->permission_menu = "C,R,U,D";
        $country_menu->sort_order = 2;
        $country_menu->flag_end = 1;
        $country_menu->save();

        // create menu file 'Department' 14
        $department_menu = new Menu;
        $department_menu->parent_id = $general_master_module->id; //for root menu
        $department_menu->type = 1; // 1 = file
        $department_menu->path = "11,14"; // path menu id for easy get structur
        $department_menu->name = "Department";
        $department_menu->description = "Menu for manage data department";
        $department_menu->url = "master/department";
        $department_menu->permission_menu = "C,R,U,D";
        $department_menu->sort_order = 3;
        $department_menu->flag_end = 1;
        $department_menu->save();

        // create menu file 'Position' 15
        $position_menu = new Menu;
        $position_menu->parent_id = $general_master_module->id; //for root menu
        $position_menu->type = 1; // 1 = file
        $position_menu->path = "11,15"; // path menu id for easy get structur
        $position_menu->name = "Position";
        $position_menu->description = "Menu for manage data position";
        $position_menu->url = "master/position";
        $position_menu->permission_menu = "C,R,U,D";
        $position_menu->sort_order = 4;
        $position_menu->flag_end = 1;
        $position_menu->save();

        // create menu file 'Master Configuration' 16
        $master_configuration_menu = new Menu;
        $master_configuration_menu->parent_id = $general_master_module->id; //for root menu
        $master_configuration_menu->type = 1; // 1 = file
        $master_configuration_menu->path = "11,16"; // path menu id for easy get structur
        $master_configuration_menu->name = "General Master Configuration";
        $master_configuration_menu->description = "Menu for manage configuration general master";
        $master_configuration_menu->url = "master/master-configuration";
        $master_configuration_menu->permission_menu = "C,R,U,D";
        $master_configuration_menu->sort_order = 5;
        $master_configuration_menu->flag_end = 1;
        $master_configuration_menu->save();

        // create menu folder 'Master' 17
        $master_module = new Menu;
        $master_module->parent_id = 0; //for root menu
        $master_module->type = 3; // 3 = module
        $master_module->path = "17"; // path menu id for easy get structur
        $master_module->name = "Master";
        $master_module->description = "Menu module master";
        $master_module->sort_order = 3;
        $master_module->flag_end = 0;
        $master_module->save();

        // create menu file 'Area Order' 18
        $area_order_menu = new Menu;
        $area_order_menu->parent_id = $master_module->id; //for root menu
        $area_order_menu->type = 1; // 1 = file
        $area_order_menu->path = "17,18"; // path menu id for easy get structur
        $area_order_menu->name = "Area Order";
        $area_order_menu->description = "Menu for manage data area order";
        $area_order_menu->url = "master/area";
        $area_order_menu->permission_menu = "C,R,U,D";
        $area_order_menu->sort_order = 1;
        $area_order_menu->flag_end = 1;
        $area_order_menu->save();

        // create menu file 'Plant' 19
        $plant_menu = new Menu;
        $plant_menu->parent_id = $master_module->id; //for root menu
        $plant_menu->type = 1; // 1 = file
        $plant_menu->path = "17,19"; // path menu id for easy get structur
        $plant_menu->name = "Plant";
        $plant_menu->description = "Menu for manage data plant";
        $plant_menu->url = "master/plant";
        $plant_menu->permission_menu = "C,R,U,D";
        $plant_menu->sort_order = 3;
        $plant_menu->flag_end = 1;
        $plant_menu->save();

        // create menu file 'Material SAP' 20
        $material_sap_menu = new Menu;
        $material_sap_menu->parent_id = $master_module->id; //for root menu
        $material_sap_menu->type = 1; // 1 = file
        $material_sap_menu->path = "17,20"; // path menu id for easy get structur
        $material_sap_menu->name = "Material SAP";
        $material_sap_menu->description = "Menu for manage data material";
        $material_sap_menu->url = "master/material";
        $material_sap_menu->permission_menu = "C,R,U,D";
        $material_sap_menu->sort_order = 4;
        $material_sap_menu->flag_end = 1;
        $material_sap_menu->save();

        // create menu file 'Material Outlet' 21
        $material_outlet_menu = new Menu;
        $material_outlet_menu->parent_id = $master_module->id; //for root menu
        $material_outlet_menu->type = 1; // 1 = file
        $material_outlet_menu->path = "17,21"; // path menu id for easy get structur
        $material_outlet_menu->name = "Material Outlet";
        $material_outlet_menu->description = "Menu for manage master material outlet";
        $material_outlet_menu->url = "master/material-outlet";
        $material_outlet_menu->permission_menu = "C,R,U,D";
        $material_outlet_menu->sort_order = 5;
        $material_outlet_menu->flag_end = 1;
        $material_outlet_menu->save();

        // create menu file 'Area Plant' 22
        $area_plant_menu = new Menu;
        $area_plant_menu->parent_id = $master_module->id; //for root menu
        $area_plant_menu->type = 1; // 1 = file
        $area_plant_menu->path = "17,22"; // path menu id for easy get structur
        $area_plant_menu->name = "Area Plant";
        $area_plant_menu->description = "Menu for manage data area plant";
        $area_plant_menu->url = "master/area-plant";
        $area_plant_menu->permission_menu = "C,R,U,D";
        $area_plant_menu->sort_order = 6;
        $area_plant_menu->flag_end = 1;
        $area_plant_menu->save();

        // create menu file 'Regional Plant' 23
        $regional_plant_menu = new Menu;
        $regional_plant_menu->parent_id = $master_module->id; //for root menu
        $regional_plant_menu->type = 1; // 1 = file
        $regional_plant_menu->path = "17,23"; // path menu id for easy get structur
        $regional_plant_menu->name = "Regional Plant";
        $regional_plant_menu->description = "Menu for manage regional plant";
        $regional_plant_menu->url = "master/regional-plant";
        $regional_plant_menu->permission_menu = "C,R,U,D";
        $regional_plant_menu->sort_order = 7;
        $regional_plant_menu->flag_end = 1;
        $regional_plant_menu->save();

        // create menu file 'Master GL' 24
        $master_gl_menu = new Menu;
        $master_gl_menu->parent_id = $master_module->id; //for root menu
        $master_gl_menu->type = 1; // 1 = file
        $master_gl_menu->path = "17,24"; // path menu id for easy get structur
        $master_gl_menu->name = "Master GL";
        $master_gl_menu->description = "Menu for manage master GL";
        $master_gl_menu->url = "master/gl";
        $master_gl_menu->permission_menu = "C,R,U,D";
        $master_gl_menu->sort_order = 8;
        $master_gl_menu->flag_end = 1;
        $master_gl_menu->save();

        // create menu file 'Bank GL' 25
        $bank_gl_menu = new Menu;
        $bank_gl_menu->parent_id = $master_module->id; //for root menu
        $bank_gl_menu->type = 1; // 1 = file
        $bank_gl_menu->path = "17,25"; // path menu id for easy get structur
        $bank_gl_menu->name = "Bank GL";
        $bank_gl_menu->description = "Menu for manage bank GL";
        $bank_gl_menu->url = "master/bank-gl";
        $bank_gl_menu->permission_menu = "C,R,U,D";
        $bank_gl_menu->sort_order = 9;
        $bank_gl_menu->flag_end = 1;
        $bank_gl_menu->save();

        // create menu file 'Special GL' 26
        $special_gl_menu = new Menu;
        $special_gl_menu->parent_id = $master_module->id; //for root menu
        $special_gl_menu->type = 1; // 1 = file
        $special_gl_menu->path = "17,26"; // path menu id for easy get structur
        $special_gl_menu->name = "Special GL";
        $special_gl_menu->description = "Menu for manage special gl";
        $special_gl_menu->url = "master/special-gl";
        $special_gl_menu->permission_menu = "C,R,U,D";
        $special_gl_menu->sort_order = 10;
        $special_gl_menu->flag_end = 1;
        $special_gl_menu->save();

        // create menu file 'Bank Charge GL' 27
        $bank_charge_gl_menu = new Menu;
        $bank_charge_gl_menu->parent_id = $master_module->id; //for root menu
        $bank_charge_gl_menu->type = 1; // 1 = file
        $bank_charge_gl_menu->path = "17,27"; // path menu id for easy get structur
        $bank_charge_gl_menu->name = "Bank Charge GL";
        $bank_charge_gl_menu->description = "Menu for manage bank charge gl";
        $bank_charge_gl_menu->url = "master/bank-charge-gl";
        $bank_charge_gl_menu->permission_menu = "C,R,U,D";
        $bank_charge_gl_menu->sort_order = 11;
        $bank_charge_gl_menu->flag_end = 1;
        $bank_charge_gl_menu->save();

        // create menu file 'Pos' 28
        $area_order_menu = new Menu;
        $area_order_menu->parent_id = $master_module->id; //for root menu
        $area_order_menu->type = 1; // 1 = file
        $area_order_menu->path = "17,28"; // path menu id for easy get structur
        $area_order_menu->name = "Pos";
        $area_order_menu->description = "Menu for manage data pos";
        $area_order_menu->url = "master/pos";
        $area_order_menu->permission_menu = "C,R,U,D";
        $area_order_menu->sort_order = 2;
        $area_order_menu->flag_end = 1;
        $area_order_menu->save();

        // create menu folder 'Finance Accounting' 29
        $finance_module = new Menu;
        $finance_module->parent_id = 0; //for root menu
        $finance_module->type = 3; // 3 = module
        $finance_module->path = "17"; // path menu id for easy get structur
        $finance_module->name = "Finance Accounting";
        $finance_module->description = "Menu module for finance accounting";
        $finance_module->sort_order = 4;
        $finance_module->flag_end = 0;
        $finance_module->save();

        // create menu folder 'Asset' 30
        $asset_folder = new Menu;
        $asset_folder->parent_id = $finance_module->id; //for root menu
        $asset_folder->type = 2; // 2 = folder
        $asset_folder->path = "29,30"; // path menu id for easy get structur
        $asset_folder->name = "Asset";
        $asset_folder->description = "Menu folder for module asset";
        $asset_folder->sort_order = 1;
        $asset_folder->flag_end = 0;
        $asset_folder->save();

        // create menu file 'Asset List' 31
        $asset_list_menu = new Menu;
        $asset_list_menu->parent_id = $asset_folder->id; //for root menu
        $asset_list_menu->type = 1; // 1 = file
        $asset_list_menu->path = "29,30,31"; // path menu id for easy get structur
        $asset_list_menu->name = "Asset List";
        $asset_list_menu->description = "Menu for asset list";
        $asset_list_menu->url = "financeacc/asset/list";
        $asset_list_menu->permission_menu = "C,R,U,D";
        $asset_list_menu->sort_order = 1;
        $asset_list_menu->flag_end = 1;
        $asset_list_menu->save();

        // create menu file 'Asset Transfer' 32
        $asset_transfer_menu = new Menu;
        $asset_transfer_menu->parent_id = $asset_folder->id; //for root menu
        $asset_transfer_menu->type = 1; // 1 = file
        $asset_transfer_menu->path = "29,30,32"; // path menu id for easy get structur
        $asset_transfer_menu->name = "Asset Transfer";
        $asset_transfer_menu->description = "Menu for asset transfer";
        $asset_transfer_menu->url = "financeacc/asset/mutation";
        $asset_transfer_menu->permission_menu = "C,R,U,D";
        $asset_transfer_menu->sort_order = 2;
        $asset_transfer_menu->flag_end = 1;
        $asset_transfer_menu->save();

        // create menu file 'Asset Transfer Manual' 33
        $asset_transfer_manual_menu = new Menu;
        $asset_transfer_manual_menu->parent_id = $asset_folder->id; //for root menu
        $asset_transfer_manual_menu->type = 1; // 1 = file
        $asset_transfer_manual_menu->path = "29,30,33"; // path menu id for easy get structur
        $asset_transfer_manual_menu->name = "Asset Transfer Manual";
        $asset_transfer_manual_menu->description = "Menu for depat asset transfer in sap manualy";
        $asset_transfer_manual_menu->url = "financeacc/asset/mutationmanual";
        $asset_transfer_manual_menu->permission_menu = "C,R,U,D";
        $asset_transfer_manual_menu->sort_order = 3;
        $asset_transfer_manual_menu->flag_end = 1;
        $asset_transfer_manual_menu->save();

        // create menu file 'Asset Print SJ' 34
        $asset_print_sj_menu = new Menu;
        $asset_print_sj_menu->parent_id = $asset_folder->id; //for root menu
        $asset_print_sj_menu->type = 1; // 1 = file
        $asset_print_sj_menu->path = "29,30,34"; // path menu id for easy get structur
        $asset_print_sj_menu->name = "Asset Print SJ";
        $asset_print_sj_menu->description = "Menu for print SJ asset transfer";
        $asset_print_sj_menu->url = "financeacc/asset/printsj";
        $asset_print_sj_menu->permission_menu = "C,R,U,D";
        $asset_print_sj_menu->sort_order = 4;
        $asset_print_sj_menu->flag_end = 1;
        $asset_print_sj_menu->save();

        // create menu file 'Asset SO' 35
        $asset_so_menu = new Menu;
        $asset_so_menu->parent_id = $asset_folder->id; //for root menu
        $asset_so_menu->type = 1; // 1 = file
        $asset_so_menu->path = "29,30,35"; // path menu id for easy get structur
        $asset_so_menu->name = "Asset SO";
        $asset_so_menu->description = "Menu for asset SO";
        $asset_so_menu->url = "financeacc/asset/so";
        $asset_so_menu->permission_menu = "C,R,U,D";
        $asset_so_menu->sort_order = 5;
        $asset_so_menu->flag_end = 1;
        $asset_so_menu->save();

        // create menu file 'Asset Validator' 36
        $asset_validator = new Menu;
        $asset_validator->parent_id = $asset_folder->id; //for root menu
        $asset_validator->type = 1; // 1 = file
        $asset_validator->path = "29,30,36"; // path menu id for easy get structur
        $asset_validator->name = "Asset Validator";
        $asset_validator->description = "Menu for manage validator asset";
        $asset_validator->url = "financeacc/asset/validator";
        $asset_validator->permission_menu = "C,R,U,D";
        $asset_validator->sort_order = 6;
        $asset_validator->flag_end = 1;
        $asset_validator->save();

        // create menu file 'Asset Mapping Admin Depart' 37
        $asset_mapping_menu = new Menu;
        $asset_mapping_menu->parent_id = $asset_folder->id; //for root menu
        $asset_mapping_menu->type = 1; // 1 = file
        $asset_mapping_menu->path = "29,30,37"; // path menu id for easy get structur
        $asset_mapping_menu->name = "Asset Mapping Admin Depart";
        $asset_mapping_menu->description = "Menu for mapping user to admin department";
        $asset_mapping_menu->url = "financeacc/asset/admin-depart";
        $asset_mapping_menu->permission_menu = "C,R,U,D";
        $asset_mapping_menu->sort_order = 7;
        $asset_mapping_menu->flag_end = 1;
        $asset_mapping_menu->save();

        // create menu file 'Petty Cash' 38
        $pettycash_menu = new Menu;
        $pettycash_menu->parent_id = $finance_module->id; //for root menu
        $pettycash_menu->type = 1; // 1 = file
        $pettycash_menu->path = "29,38"; // path menu id for easy get structur
        $pettycash_menu->name = "Petty Cash";
        $pettycash_menu->description = "Menu petty cash for outlet and DC";
        $pettycash_menu->url = "financeacc/pettycash";
        $pettycash_menu->permission_menu = "C,R,U,D";
        $pettycash_menu->sort_order = 2;
        $pettycash_menu->flag_end = 1;
        $pettycash_menu->save();

        // create menu file 'Mass Clearing' 39
        $mass_clearing_menu = new Menu;
        $mass_clearing_menu->parent_id = $finance_module->id; //for root menu
        $mass_clearing_menu->type = 1; // 1 = file
        $mass_clearing_menu->path = "29,39"; // path menu id for easy get structur
        $mass_clearing_menu->name = "Mass Clearing";
        $mass_clearing_menu->description = "Menu for generate mass clearing";
        $mass_clearing_menu->url = "financeacc/mass-clearing";
        $mass_clearing_menu->permission_menu = "C,R,U,D";
        $mass_clearing_menu->sort_order = 3;
        $mass_clearing_menu->flag_end = 1;
        $mass_clearing_menu->save();

        // create menu file 'Finance Accounting Configuration' 40
        $conf_finance_menu = new Menu;
        $conf_finance_menu->parent_id = $finance_module->id; //for root menu
        $conf_finance_menu->type = 1; // 1 = file
        $conf_finance_menu->path = "29,40"; // path menu id for easy get structur
        $conf_finance_menu->name = "Finance Accounting Configuration";
        $conf_finance_menu->description = "Menu for configuration finance accounting";
        $conf_finance_menu->url = "financeacc/configuration-financeacc";
        $conf_finance_menu->permission_menu = "C,R,U,D";
        $conf_finance_menu->sort_order = 4;
        $conf_finance_menu->flag_end = 1;
        $conf_finance_menu->save();

        // create menu folder 'Inventory' 41
        $inventory_module = new Menu;
        $inventory_module->parent_id = 0; //for root menu
        $inventory_module->type = 3; // 3 = module
        $inventory_module->path = "41"; // path menu id for easy get structur
        $inventory_module->name = "Inventory";
        $inventory_module->description = "Menu module for inventory";
        $inventory_module->sort_order = 5;
        $inventory_module->flag_end = 0;
        $inventory_module->save();

        // create menu folder 'Used Oil' 42
        $used_oil_folder = new Menu;
        $used_oil_folder->parent_id = $inventory_module->id; //for root menu
        $used_oil_folder->type = 2; // 2 = folder
        $used_oil_folder->path = "41,42"; // path menu id for easy get structur
        $used_oil_folder->name = "Used Oil";
        $used_oil_folder->description = "menu folder for used oil module";
        $used_oil_folder->sort_order = 1;
        $used_oil_folder->flag_end = 0;
        $used_oil_folder->save();

        // create menu file 'Material Used Oil' 43
        $material_used_oil_menu = new Menu;
        $material_used_oil_menu->parent_id = $used_oil_folder->id; //for root menu
        $material_used_oil_menu->type = 1; // 1 = file
        $material_used_oil_menu->path = "41,42,43"; // path menu id for easy get structur
        $material_used_oil_menu->name = "Material Used Oil";
        $material_used_oil_menu->description = "Menu for manage material used oil";
        $material_used_oil_menu->url = "inventory/usedoil/uo-material";
        $material_used_oil_menu->permission_menu = "C,R,U,D";
        $material_used_oil_menu->sort_order = 1;
        $material_used_oil_menu->flag_end = 1;
        $material_used_oil_menu->save();

        // create menu file 'Category Price' 44
        $category_price_menu = new Menu;
        $category_price_menu->parent_id = $used_oil_folder->id; //for root menu
        $category_price_menu->type = 1; // 1 = file
        $category_price_menu->path = "41,42,44"; // path menu id for easy get structur
        $category_price_menu->name = "Category Price";
        $category_price_menu->description = "Menu for manage price category vendor";
        $category_price_menu->url = "inventory/usedoil/uo-price-category";
        $category_price_menu->permission_menu = "C,R,U,D";
        $category_price_menu->sort_order = 2;
        $category_price_menu->flag_end = 1;
        $category_price_menu->save();

        // create menu file 'Vendor Used Oil' 45
        $vendor_used_oil_menu = new Menu;
        $vendor_used_oil_menu->parent_id = $used_oil_folder->id; //for root menu
        $vendor_used_oil_menu->type = 1; // 1 = file
        $vendor_used_oil_menu->path = "41,42,45"; // path menu id for easy get structur
        $vendor_used_oil_menu->name = "Vendor Used Oil";
        $vendor_used_oil_menu->description = "Menu for manage master vendor used oil";
        $vendor_used_oil_menu->url = "inventory/usedoil/uo-vendor";
        $vendor_used_oil_menu->permission_menu = "C,R,U,D";
        $vendor_used_oil_menu->sort_order = 3;
        $vendor_used_oil_menu->flag_end = 1;
        $vendor_used_oil_menu->save();

        // create menu file 'Vendor Deposit' 46
        $vendor_deposit_menu = new Menu;
        $vendor_deposit_menu->parent_id = $used_oil_folder->id; //for root menu
        $vendor_deposit_menu->type = 1; // 1 = file
        $vendor_deposit_menu->path = "41,42,46"; // path menu id for easy get structur
        $vendor_deposit_menu->name = "Vendor Deposit";
        $vendor_deposit_menu->description = "Menu for deposit vendor";
        $vendor_deposit_menu->url = "inventory/usedoil/uo-deposit";
        $vendor_deposit_menu->permission_menu = "C,R,U,D";
        $vendor_deposit_menu->sort_order = 4;
        $vendor_deposit_menu->flag_end = 1;
        $vendor_deposit_menu->save();

        // create menu file 'Vendor Mutation Saldo' 47
        $vendor_mutation_menu = new Menu;
        $vendor_mutation_menu->parent_id = $used_oil_folder->id; //for root menu
        $vendor_mutation_menu->type = 1; // 1 = file
        $vendor_mutation_menu->path = "41,42,47"; // path menu id for easy get structur
        $vendor_mutation_menu->name = "Vendor Mutation Saldo";
        $vendor_mutation_menu->description = "Menu for mutation saldo vendor";
        $vendor_mutation_menu->url = "inventory/usedoil/uo-mutation-saldo";
        $vendor_mutation_menu->permission_menu = "C,R,U,D";
        $vendor_mutation_menu->sort_order = 5;
        $vendor_mutation_menu->flag_end = 1;
        $vendor_mutation_menu->save();

        // create menu file 'Stock Adjustment' 48
        $stock_adjusment_menu = new Menu;
        $stock_adjusment_menu->parent_id = $used_oil_folder->id; //for root menu
        $stock_adjusment_menu->type = 1; // 1 = file
        $stock_adjusment_menu->path = "41,42,48"; // path menu id for easy get structur
        $stock_adjusment_menu->name = "Stock Adjustment";
        $stock_adjusment_menu->description = "Menu for adjustment stock material";
        $stock_adjusment_menu->url = "inventory/usedoil/uo-stock-adjustment";
        $stock_adjusment_menu->permission_menu = "C,R,U,D";
        $stock_adjusment_menu->sort_order = 6;
        $stock_adjusment_menu->flag_end = 1;
        $stock_adjusment_menu->save();

        // create menu file 'Good Receipt' 49
        $good_receive_menu = new Menu;
        $good_receive_menu->parent_id = $used_oil_folder->id; //for root menu
        $good_receive_menu->type = 1; // 1 = file
        $good_receive_menu->path = "41,42,49"; // path menu id for easy get structur
        $good_receive_menu->name = "Good Receipt";
        $good_receive_menu->description = "Menu for good receipt";
        $good_receive_menu->url = "inventory/usedoil/uo-good-receipt";
        $good_receive_menu->permission_menu = "C,R,U,D";
        $good_receive_menu->sort_order = 7;
        $good_receive_menu->flag_end = 1;
        $good_receive_menu->save();

        // create menu file 'GI Transfer' 50
        $gi_transfer_menu = new Menu;
        $gi_transfer_menu->parent_id = $used_oil_folder->id; //for root menu
        $gi_transfer_menu->type = 1; // 1 = file
        $gi_transfer_menu->path = "41,42,50"; // path menu id for easy get structur
        $gi_transfer_menu->name = "GI Transfer";
        $gi_transfer_menu->description = "Menu for GI Transfer";
        $gi_transfer_menu->url = "inventory/usedoil/uo-gitransfer";
        $gi_transfer_menu->permission_menu = "C,R,U,D";
        $gi_transfer_menu->sort_order = 8;
        $gi_transfer_menu->flag_end = 1;
        $gi_transfer_menu->save();

        // create menu file 'GR Transfer' 51
        $gr_transfer_menu = new Menu;
        $gr_transfer_menu->parent_id = $used_oil_folder->id; //for root menu
        $gr_transfer_menu->type = 1; // 1 = file
        $gr_transfer_menu->path = "41,42,51"; // path menu id for easy get structur
        $gr_transfer_menu->name = "GR Transfer";
        $gr_transfer_menu->description = "Menu for GR Transfer";
        $gr_transfer_menu->url = "inventory/usedoil/uo-grtransfer";
        $gr_transfer_menu->permission_menu = "C,R,U,D";
        $gr_transfer_menu->sort_order = 9;
        $gr_transfer_menu->flag_end = 1;
        $gr_transfer_menu->save();

        // create menu file 'Sales' 52
        $sales_menu = new Menu;
        $sales_menu->parent_id = $used_oil_folder->id; //for root menu
        $sales_menu->type = 1; // 1 = file
        $sales_menu->path = "41,42,52"; // path menu id for easy get structur
        $sales_menu->name = "Sales";
        $sales_menu->description = "Menu for sales material used oil";
        $sales_menu->url = "inventory/usedoil/uo-sales";
        $sales_menu->permission_menu = "C,R,U,D";
        $sales_menu->sort_order = 10;
        $sales_menu->flag_end = 1;
        $sales_menu->save();

        // create menu file 'GI Plant' 53
        $gi_plant_menu = new Menu;
        $gi_plant_menu->parent_id = $inventory_module->id; //for root menu
        $gi_plant_menu->type = 1; // 1 = file
        $gi_plant_menu->path = "41,53"; // path menu id for easy get structur
        $gi_plant_menu->name = "GI Plant";
        $gi_plant_menu->description = "Menu for GI Plant";
        $gi_plant_menu->url = "inventory/giplant";
        $gi_plant_menu->permission_menu = "C,R,U,D";
        $gi_plant_menu->sort_order = 2;
        $gi_plant_menu->flag_end = 1;
        $gi_plant_menu->save();

        // create menu file 'GR Plant' 54
        $gr_plant_menu = new Menu;
        $gr_plant_menu->parent_id = $inventory_module->id; //for root menu
        $gr_plant_menu->type = 1; // 1 = file
        $gr_plant_menu->path = "41,54"; // path menu id for easy get structur
        $gr_plant_menu->name = "GR Plant";
        $gr_plant_menu->description = "Menu for GR Plant";
        $gr_plant_menu->url = "inventory/grplant";
        $gr_plant_menu->permission_menu = "C,R,U,D";
        $gr_plant_menu->sort_order = 3;
        $gr_plant_menu->flag_end = 1;
        $gr_plant_menu->save();

        // create menu file 'GR PO Vendor' 55
        $gr_po_vendor_menu = new Menu;
        $gr_po_vendor_menu->parent_id = $inventory_module->id; //for root menu
        $gr_po_vendor_menu->type = 1; // 1 = file
        $gr_po_vendor_menu->path = "41,55"; // path menu id for easy get structur
        $gr_po_vendor_menu->name = "GR PO Vendor";
        $gr_po_vendor_menu->description = "Menu for GR PO Vendor";
        $gr_po_vendor_menu->url = "inventory/grvendor";
        $gr_po_vendor_menu->permission_menu = "C,R,U,D";
        $gr_po_vendor_menu->sort_order = 4;
        $gr_po_vendor_menu->flag_end = 1;
        $gr_po_vendor_menu->save();

        // create menu file 'Opname' 56
        $opname_menu = new Menu;
        $opname_menu->parent_id = $inventory_module->id; //for root menu
        $opname_menu->type = 1; // 1 = file
        $opname_menu->path = "41,56"; // path menu id for easy get structur
        $opname_menu->name = "Opname";
        $opname_menu->description = "Menu for opname to SAP";
        $opname_menu->url = "inventory/opname";
        $opname_menu->permission_menu = "C,R,U,D";
        $opname_menu->sort_order = 5;
        $opname_menu->flag_end = 1;
        $opname_menu->save();

        // create menu file 'Waste / Scraping' 57
        $waste_menu = new Menu;
        $waste_menu->parent_id = $inventory_module->id; //for root menu
        $waste_menu->type = 1; // 1 = file
        $waste_menu->path = "41,57"; // path menu id for easy get structur
        $waste_menu->name = "Waste / Scraping";
        $waste_menu->description = "Menu for waste / scraping";
        $waste_menu->url = "inventory/waste";
        $waste_menu->permission_menu = "C,R,U,D";
        $waste_menu->sort_order = 6;
        $waste_menu->flag_end = 1;
        $waste_menu->save();

        // create menu file 'Inventory Configuration' 58
        $inventory_conf_menu = new Menu;
        $inventory_conf_menu->parent_id = $inventory_module->id; //for root menu
        $inventory_conf_menu->type = 1; // 1 = file
        $inventory_conf_menu->path = "41,58"; // path menu id for easy get structur
        $inventory_conf_menu->name = "Inventory Configuration";
        $inventory_conf_menu->description = "Menu for manage configuration inventory";
        $inventory_conf_menu->url = "inventory/configuration-inventory";
        $inventory_conf_menu->permission_menu = "C,R,U,D";
        $inventory_conf_menu->sort_order = 7;
        $inventory_conf_menu->flag_end = 1;
        $inventory_conf_menu->save();

        // create menu module 'Tax' 59
        $tax_module = new Menu;
        $tax_module->parent_id = 0; //for root menu
        $tax_module->type = 3; // 3 = module
        $tax_module->path = "59"; // path menu id for easy get structur
        $tax_module->name = "Tax";
        $tax_module->description = "Menu module for tax";
        $tax_module->sort_order = 6;
        $tax_module->flag_end = 0;
        $tax_module->save();

        // create menu file 'FTP Government' 60
        $ftp_goverment_menu = new Menu;
        $ftp_goverment_menu->parent_id = $tax_module->id; //for root menu
        $ftp_goverment_menu->type = 1; // 1 = file
        $ftp_goverment_menu->path = "59,60"; // path menu id for easy get structur
        $ftp_goverment_menu->name = "FTP Government";
        $ftp_goverment_menu->description = "Menu for list ftp / sftp tax government";
        $ftp_goverment_menu->url = "tax/ftp-government";
        $ftp_goverment_menu->permission_menu = "C,R,U,D";
        $ftp_goverment_menu->sort_order = 1;
        $ftp_goverment_menu->flag_end = 1;
        $ftp_goverment_menu->save();

        // create menu file 'Send Tax' 61
        $send_tax_menu = new Menu;
        $send_tax_menu->parent_id = $tax_module->id; //for root menu
        $send_tax_menu->type = 1; // 1 = file
        $send_tax_menu->path = "59,61"; // path menu id for easy get structur
        $send_tax_menu->name = "Send Tax";
        $send_tax_menu->description = "Menu for setting and manual send tax to government";
        $send_tax_menu->url = "tax/send-tax";
        $send_tax_menu->permission_menu = "C,R,U,D";
        $send_tax_menu->sort_order = 2;
        $send_tax_menu->flag_end = 1;
        $send_tax_menu->save();

        // create menu module 'Report' 62
        $report_module = new Menu;
        $report_module->parent_id = 0; //for root menu
        $report_module->type = 3; // 3 = module
        $report_module->path = "62"; // path menu id for easy get structur
        $report_module->name = "Report";
        $report_module->description = "Menu module for report";
        $report_module->sort_order = 7;
        $report_module->flag_end = 0;
        $report_module->save();

        // create menu folder 'Finance Accounting' 63
        $finance_accounting_folder = new Menu;
        $finance_accounting_folder->parent_id = $report_module->id; //for root menu
        $finance_accounting_folder->type = 2; // 2 = folder
        $finance_accounting_folder->path = "62,63"; // path menu id for easy get structur
        $finance_accounting_folder->name = "Finance Accounting";
        $finance_accounting_folder->description = "Menu folder for report finance acc module";
        $finance_accounting_folder->sort_order = 1;
        $finance_accounting_folder->flag_end = 0;
        $finance_accounting_folder->save();

        // create menu folder 'Asset' 64
        $asset_folder = new Menu;
        $asset_folder->parent_id = $finance_accounting_folder->id; //for root menu
        $asset_folder->type = 2; // 2 = folder
        $asset_folder->path = "62,63,64"; // path menu id for easy get structur
        $asset_folder->name = "Asset";
        $asset_folder->description = "Module Report Asset";
        $asset_folder->sort_order = 1;
        $asset_folder->flag_end = 0;
        $asset_folder->save();

        // create menu file 'Report Asset Transfer Outstanding' 65
        $report_asset_transfer_outstanding_menu = new Menu;
        $report_asset_transfer_outstanding_menu->parent_id = $asset_folder->id; //for root menu
        $report_asset_transfer_outstanding_menu->type = 1; // 1 = file
        $report_asset_transfer_outstanding_menu->path = "62,63,64,65"; // path menu id for easy get structur
        $report_asset_transfer_outstanding_menu->name = "Asset Transfer Outstanding Report";
        $report_asset_transfer_outstanding_menu->description = "Menu for report outstanding asset transfer";
        $report_asset_transfer_outstanding_menu->url = "report/financeacc/outstanding-mutation-asset";
        $report_asset_transfer_outstanding_menu->permission_menu = "C,R,U,D";
        $report_asset_transfer_outstanding_menu->sort_order = 1;
        $report_asset_transfer_outstanding_menu->flag_end = 1;
        $report_asset_transfer_outstanding_menu->save();

        // create menu file 'Report Asset Transfer' 66
        $report_asset_transfer_menu = new Menu;
        $report_asset_transfer_menu->parent_id = $asset_folder->id; //for root menu
        $report_asset_transfer_menu->type = 1; // 1 = file
        $report_asset_transfer_menu->path = "62,63,64,66"; // path menu id for easy get structur
        $report_asset_transfer_menu->name = "Asset Transfer Report";
        $report_asset_transfer_menu->description = "Menu for report asset transfer log";
        $report_asset_transfer_menu->url = "report/financeacc/log-mutation-asset";
        $report_asset_transfer_menu->permission_menu = "C,R,U,D";
        $report_asset_transfer_menu->sort_order = 2;
        $report_asset_transfer_menu->flag_end = 1;
        $report_asset_transfer_menu->save();

        // create menu file 'Report Asset SO' 67
        $report_asset_so_menu = new Menu;
        $report_asset_so_menu->parent_id = $asset_folder->id; //for root menu
        $report_asset_so_menu->type = 1; // 1 = file
        $report_asset_so_menu->path = "62,63,64,67"; // path menu id for easy get structur
        $report_asset_so_menu->name = "Asset SO Report";
        $report_asset_so_menu->description = "Menu for report asset so";
        $report_asset_so_menu->url = "report/financeacc/asset-so";
        $report_asset_so_menu->permission_menu = "C,R,U,D";
        $report_asset_so_menu->sort_order = 3;
        $report_asset_so_menu->flag_end = 1;
        $report_asset_so_menu->save();

        // create menu file 'Report Selisih Asset SO' 68
        $report_selisih_asset_so_menu = new Menu;
        $report_selisih_asset_so_menu->parent_id = $asset_folder->id; //for root menu
        $report_selisih_asset_so_menu->type = 1; // 1 = file
        $report_selisih_asset_so_menu->path = "62,63,64,68"; // path menu id for easy get structur
        $report_selisih_asset_so_menu->name = "Selisih Asset SO Report";
        $report_selisih_asset_so_menu->description = "Menu for report selisih asset so";
        $report_selisih_asset_so_menu->url = "report/financeacc/selisih-asset-so";
        $report_selisih_asset_so_menu->permission_menu = "C,R,U,D";
        $report_selisih_asset_so_menu->sort_order = 4;
        $report_selisih_asset_so_menu->flag_end = 1;
        $report_selisih_asset_so_menu->save();

        // create menu folder 'Inventory' 69
        $inventory_report_folder = new Menu;
        $inventory_report_folder->parent_id = $report_module->id; //for root menu
        $inventory_report_folder->type = 2; // 2 = folder
        $inventory_report_folder->path = "62,69"; // path menu id for easy get structur
        $inventory_report_folder->name = "Inventory";
        $inventory_report_folder->description = "Menu folder for report inventory module";
        $inventory_report_folder->sort_order = 2;
        $inventory_report_folder->flag_end = 0;
        $inventory_report_folder->save();

        // create menu folder 'Used Oil' 70
        $used_oil_report_folder = new Menu;
        $used_oil_report_folder->parent_id = $inventory_report_folder->id; //for root menu
        $used_oil_report_folder->type = 2; // 2 = folder
        $used_oil_report_folder->path = "62,69,70"; // path menu id for easy get structur
        $used_oil_report_folder->name = "Used Oil";
        $used_oil_report_folder->description = "Menu for report used oil";
        $used_oil_report_folder->sort_order = 1;
        $used_oil_report_folder->flag_end = 0;
        $used_oil_report_folder->save();

        // create menu file 'Stock Material Plant' 71
        $stock_material_plant_menu = new Menu;
        $stock_material_plant_menu->parent_id = $used_oil_report_folder->id; //for root menu
        $stock_material_plant_menu->type = 1; // 1 = file
        $stock_material_plant_menu->path = "62,69,70,71"; // path menu id for easy get structur
        $stock_material_plant_menu->name = "Stock Material Plant Report";
        $stock_material_plant_menu->description = "Menu for report stock material plant";
        $stock_material_plant_menu->url = "report/inventory/uo-stock-material-plant";
        $stock_material_plant_menu->permission_menu = "C,R,U,D";
        $stock_material_plant_menu->sort_order = 1;
        $stock_material_plant_menu->flag_end = 1;
        $stock_material_plant_menu->save();

        // create menu file 'Saldo Vendor' 72
        $saldo_vendor_menu = new Menu;
        $saldo_vendor_menu->parent_id = $used_oil_report_folder->id; //for root menu
        $saldo_vendor_menu->type = 1; // 1 = file
        $saldo_vendor_menu->path = "62,69,70,72"; // path menu id for easy get structur
        $saldo_vendor_menu->name = "Saldo Vendor Report";
        $saldo_vendor_menu->description = "Menu for report saldo vendor";
        $saldo_vendor_menu->url = "report/inventory/uo-saldo-vendor";
        $saldo_vendor_menu->permission_menu = "C,R,U,D";
        $saldo_vendor_menu->sort_order = 2;
        $saldo_vendor_menu->flag_end = 1;
        $saldo_vendor_menu->save();

        // create menu file 'History Saldo Vendor' 73
        $history_saldo_vendor_menu = new Menu;
        $history_saldo_vendor_menu->parent_id = $used_oil_report_folder->id; //for root menu
        $history_saldo_vendor_menu->type = 1; // 1 = file
        $history_saldo_vendor_menu->path = "62,69,70,73"; // path menu id for easy get structur
        $history_saldo_vendor_menu->name = "History Saldo Vendor Report";
        $history_saldo_vendor_menu->description = "Menu for report history saldo vendor";
        $history_saldo_vendor_menu->url = "report/inventory/uo-history-saldo-vendor";
        $history_saldo_vendor_menu->permission_menu = "C,R,U,D";
        $history_saldo_vendor_menu->sort_order = 3;
        $history_saldo_vendor_menu->flag_end = 1;
        $history_saldo_vendor_menu->save();

        // create menu file 'Income Sales Detail' 74
        $income_sales_detail_menu = new Menu;
        $income_sales_detail_menu->parent_id = $used_oil_report_folder->id; //for root menu
        $income_sales_detail_menu->type = 1; // 1 = file
        $income_sales_detail_menu->path = "62,69,70,74"; // path menu id for easy get structur
        $income_sales_detail_menu->name = "Income Sales Detail Report";
        $income_sales_detail_menu->description = "Menu for report income sales detail";
        $income_sales_detail_menu->url = "report/inventory/uo-income-sales-detail";
        $income_sales_detail_menu->permission_menu = "C,R,U,D";
        $income_sales_detail_menu->sort_order = 4;
        $income_sales_detail_menu->flag_end = 1;
        $income_sales_detail_menu->save();

        // create menu file 'Income Sales Summary' 75
        $income_sales_summary_menu = new Menu;
        $income_sales_summary_menu->parent_id = $used_oil_report_folder->id; //for root menu
        $income_sales_summary_menu->type = 1; // 1 = file
        $income_sales_summary_menu->path = "62,69,70,75"; // path menu id for easy get structur
        $income_sales_summary_menu->name = "Income Sales Summary Report";
        $income_sales_summary_menu->description = "Menu for report income sales summary";
        $income_sales_summary_menu->url = "report/inventory/uo-income-sales-summary";
        $income_sales_summary_menu->permission_menu = "C,R,U,D";
        $income_sales_summary_menu->sort_order = 5;
        $income_sales_summary_menu->flag_end = 1;
        $income_sales_summary_menu->save();

        // create menu file 'GI Plant Report' 76
        $gi_plant_report_menu = new Menu;
        $gi_plant_report_menu->parent_id = $inventory_report_folder->id; //for root menu
        $gi_plant_report_menu->type = 1; // 1 = file
        $gi_plant_report_menu->path = "62,69,76"; // path menu id for easy get structur
        $gi_plant_report_menu->name = "GI Plant Report";
        $gi_plant_report_menu->description = "Menu for report gi plant";
        $gi_plant_report_menu->url = "report/inventory/gi-plant";
        $gi_plant_report_menu->permission_menu = "C,R,U,D";
        $gi_plant_report_menu->sort_order = 2;
        $gi_plant_report_menu->flag_end = 1;
        $gi_plant_report_menu->save();

        // create menu file 'GR PO Vendor Report' 77
        $gr_po_vendor_report_menu = new Menu;
        $gr_po_vendor_report_menu->parent_id = $inventory_report_folder->id; //for root menu
        $gr_po_vendor_report_menu->type = 1; // 1 = file
        $gr_po_vendor_report_menu->path = "62,69,77"; // path menu id for easy get structur
        $gr_po_vendor_report_menu->name = "GR PO Vendor Report";
        $gr_po_vendor_report_menu->description = "Menu for report GR PO Vendor";
        $gr_po_vendor_report_menu->url = "report/inventory/gr-vendor";
        $gr_po_vendor_report_menu->permission_menu = "C,R,U,D";
        $gr_po_vendor_report_menu->sort_order = 4;
        $gr_po_vendor_report_menu->flag_end = 1;
        $gr_po_vendor_report_menu->save();

        // create menu file 'Waste / Scrap Report' 78
        $waste_report_menu = new Menu;
        $waste_report_menu->parent_id = $inventory_report_folder->id; //for root menu
        $waste_report_menu->type = 1; // 1 = file
        $waste_report_menu->path = "62,69,78"; // path menu id for easy get structur
        $waste_report_menu->name = "Waste / Scrap Report";
        $waste_report_menu->description = "Menu for report waste";
        $waste_report_menu->url = "report/inventory/waste";
        $waste_report_menu->permission_menu = "C,R,U,D";
        $waste_report_menu->sort_order = 5;
        $waste_report_menu->flag_end = 1;
        $waste_report_menu->save();

        // create menu file 'Current Stock Report' 79
        $current_stock_report_menu = new Menu;
        $current_stock_report_menu->parent_id = $inventory_report_folder->id; //for root menu
        $current_stock_report_menu->type = 1; // 1 = file
        $current_stock_report_menu->path = "62,69,79"; // path menu id for easy get structur
        $current_stock_report_menu->name = "Current Stock Report";
        $current_stock_report_menu->description = "Menu for report current Stock";
        $current_stock_report_menu->url = "report/inventory/current-stock";
        $current_stock_report_menu->permission_menu = "C,R,U,D";
        $current_stock_report_menu->sort_order = 6;
        $current_stock_report_menu->flag_end = 1;
        $current_stock_report_menu->save();

        // create menu file 'Outstanding PO-STO Report' 80
        $outstanding_posto_report_menu = new Menu;
        $outstanding_posto_report_menu->parent_id = $inventory_report_folder->id; //for root menu
        $outstanding_posto_report_menu->type = 1; // 1 = file
        $outstanding_posto_report_menu->path = "62,69,80"; // path menu id for easy get structur
        $outstanding_posto_report_menu->name = "Outstanding PO-STO Report";
        $outstanding_posto_report_menu->description = "Menu for report outstanding PO-STO from SAP";
        $outstanding_posto_report_menu->url = "report/inventory/outstanding-posto";
        $outstanding_posto_report_menu->permission_menu = "C,R,U,D";
        $outstanding_posto_report_menu->sort_order = 7;
        $outstanding_posto_report_menu->flag_end = 1;
        $outstanding_posto_report_menu->save();

        // create menu folder 'POS' 81
        $pos_report_folder = new Menu;
        $pos_report_folder->parent_id = $report_module->id; //for root menu
        $pos_report_folder->type = 2; // 2 = folder
        $pos_report_folder->path = "62,81"; // path menu id for easy get structur
        $pos_report_folder->name = "POS";
        $pos_report_folder->description = "Menu folder for report pos module";
        $pos_report_folder->sort_order = 3;
        $pos_report_folder->flag_end = 0;
        $pos_report_folder->save();

        // create menu file 'Report Payment Detail POS' 82
        $payment_detail_pos_report_menu = new Menu;
        $payment_detail_pos_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $payment_detail_pos_report_menu->type = 1; // 1 = file
        $payment_detail_pos_report_menu->path = "62,81,82"; // path menu id for easy get structur
        $payment_detail_pos_report_menu->name = "Payment Detail POS Report";
        $payment_detail_pos_report_menu->description = "Menu for report payment detail POS";
        $payment_detail_pos_report_menu->url = "report/pos/payment-detail-pos";
        $payment_detail_pos_report_menu->permission_menu = "C,R,U,D";
        $payment_detail_pos_report_menu->sort_order = 1;
        $payment_detail_pos_report_menu->flag_end = 1;
        $payment_detail_pos_report_menu->save();

        // create menu file 'Payment POS Report' 83
        $payment_pos_report_menu = new Menu;
        $payment_pos_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $payment_pos_report_menu->type = 1; // 1 = file
        $payment_pos_report_menu->path = "62,81,83"; // path menu id for easy get structur
        $payment_pos_report_menu->name = "Payment POS Report";
        $payment_pos_report_menu->description = "Menu for report payment POS";
        $payment_pos_report_menu->url = "report/pos/payment-pos";
        $payment_pos_report_menu->permission_menu = "C,R,U,D";
        $payment_pos_report_menu->sort_order = 2;
        $payment_pos_report_menu->flag_end = 1;
        $payment_pos_report_menu->save();

        // create menu file 'Promotion Type POS Report' 84
        $promotion_type_pos_report_menu = new Menu;
        $promotion_type_pos_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $promotion_type_pos_report_menu->type = 1; // 1 = file
        $promotion_type_pos_report_menu->path = "62,81,84"; // path menu id for easy get structur
        $promotion_type_pos_report_menu->name = "Promotion Type POS Report";
        $promotion_type_pos_report_menu->description = "Menu for report promotion type pos";
        $promotion_type_pos_report_menu->url = "report/pos/promotion-type-pos";
        $promotion_type_pos_report_menu->permission_menu = "C,R,U,D";
        $promotion_type_pos_report_menu->sort_order = 3;
        $promotion_type_pos_report_menu->flag_end = 1;
        $promotion_type_pos_report_menu->save();

        // create menu file 'Sales By Menu POS Report' 85
        $sales_by_menu_pos_report_menu = new Menu;
        $sales_by_menu_pos_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $sales_by_menu_pos_report_menu->type = 1; // 1 = file
        $sales_by_menu_pos_report_menu->path = "62,81,85"; // path menu id for easy get structur
        $sales_by_menu_pos_report_menu->name = "Sales By Menu POS Report";
        $sales_by_menu_pos_report_menu->description = "Menu for report sales by menu all pos";
        $sales_by_menu_pos_report_menu->url = "report/pos/sales-by-menu-pos";
        $sales_by_menu_pos_report_menu->permission_menu = "C,R,U,D";
        $sales_by_menu_pos_report_menu->sort_order = 4;
        $sales_by_menu_pos_report_menu->flag_end = 1;
        $sales_by_menu_pos_report_menu->save();

        // create menu file 'Sales By Inventory POS Report' 86
        $sales_by_inventory_pos_report_menu = new Menu;
        $sales_by_inventory_pos_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $sales_by_inventory_pos_report_menu->type = 1; // 1 = file
        $sales_by_inventory_pos_report_menu->path = "62,81,86"; // path menu id for easy get structur
        $sales_by_inventory_pos_report_menu->name = "Sales By Inventory POS Report";
        $sales_by_inventory_pos_report_menu->description = "Menu for report sales by inventory all pos";
        $sales_by_inventory_pos_report_menu->url = "report/pos/sales-by-inventory-pos";
        $sales_by_inventory_pos_report_menu->permission_menu = "C,R,U,D";
        $sales_by_inventory_pos_report_menu->sort_order = 5;
        $sales_by_inventory_pos_report_menu->flag_end = 1;
        $sales_by_inventory_pos_report_menu->save();

        // create menu file 'Summary Payment and Promotion Report' 87
        $summary_payment_promotion_report_menu = new Menu;
        $summary_payment_promotion_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $summary_payment_promotion_report_menu->type = 1; // 1 = file
        $summary_payment_promotion_report_menu->path = "62,81,87"; // path menu id for easy get structur
        $summary_payment_promotion_report_menu->name = "Summary Payment and Promotion Report";
        $summary_payment_promotion_report_menu->description = "Menu for report Summary Payment and Promotion Report";
        $summary_payment_promotion_report_menu->url = "report/pos/summary-payment-promotion-pos";
        $summary_payment_promotion_report_menu->permission_menu = "C,R,U,D";
        $summary_payment_promotion_report_menu->sort_order = 6;
        $summary_payment_promotion_report_menu->flag_end = 1;
        $summary_payment_promotion_report_menu->save();

        // create menu file 'Sales by Menu Per Hour POS Report' 88
        $sales_by_menu_per_hour_pos_report_menu = new Menu;
        $sales_by_menu_per_hour_pos_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $sales_by_menu_per_hour_pos_report_menu->type = 1; // 1 = file
        $sales_by_menu_per_hour_pos_report_menu->path = "62,81,88"; // path menu id for easy get structur
        $sales_by_menu_per_hour_pos_report_menu->name = "Sales by Menu Per Hour POS Report";
        $sales_by_menu_per_hour_pos_report_menu->description = "Menu for report Sales by Menu Per Hour POS";
        $sales_by_menu_per_hour_pos_report_menu->url = "report/pos/sales-menu-per-hour-pos";
        $sales_by_menu_per_hour_pos_report_menu->permission_menu = "C,R,U,D";
        $sales_by_menu_per_hour_pos_report_menu->sort_order = 7;
        $sales_by_menu_per_hour_pos_report_menu->flag_end = 1;
        $sales_by_menu_per_hour_pos_report_menu->save();

        // create menu file 'Sales by Inventory Hour POS Report' 89
        $sales_by_inventory_per_hour_pos_report_menu = new Menu;
        $sales_by_inventory_per_hour_pos_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $sales_by_inventory_per_hour_pos_report_menu->type = 1; // 1 = file
        $sales_by_inventory_per_hour_pos_report_menu->path = "62,81,89"; // path menu id for easy get structur
        $sales_by_inventory_per_hour_pos_report_menu->name = "Sales by Inventory Hour POS Report";
        $sales_by_inventory_per_hour_pos_report_menu->description = "Menu for report Sales by Inventory Hour pos";
        $sales_by_inventory_per_hour_pos_report_menu->url = "report/pos/sales-inventory-per-hour-pos";
        $sales_by_inventory_per_hour_pos_report_menu->permission_menu = "C,R,U,D";
        $sales_by_inventory_per_hour_pos_report_menu->sort_order = 8;
        $sales_by_inventory_per_hour_pos_report_menu->flag_end = 1;
        $sales_by_inventory_per_hour_pos_report_menu->save();

        // create menu file 'Void (Refund) Pos Report' 90
        $void_pos_report_menu = new Menu;
        $void_pos_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $void_pos_report_menu->type = 1; // 1 = file
        $void_pos_report_menu->path = "62,81,90"; // path menu id for easy get structur
        $void_pos_report_menu->name = "Void (Refund) Pos Report";
        $void_pos_report_menu->description = "Menu for report void refund pos";
        $void_pos_report_menu->url = "report/pos/void-pos";
        $void_pos_report_menu->permission_menu = "C,R,U,D";
        $void_pos_report_menu->sort_order = 9;
        $void_pos_report_menu->flag_end = 1;
        $void_pos_report_menu->save();

        // create menu file 'Sales Per Hour POS Report' 91
        $sales_per_hour_pos_report_menu = new Menu;
        $sales_per_hour_pos_report_menu->parent_id = $pos_report_folder->id; //for root menu
        $sales_per_hour_pos_report_menu->type = 1; // 1 = file
        $sales_per_hour_pos_report_menu->path = "62,81,91"; // path menu id for easy get structur
        $sales_per_hour_pos_report_menu->name = "Sales Per Hour POS Report";
        $sales_per_hour_pos_report_menu->description = "Menu for report sales per hour pos";
        $sales_per_hour_pos_report_menu->url = "report/pos/sales-per-hour-pos";
        $sales_per_hour_pos_report_menu->permission_menu = "C,R,U,D";
        $sales_per_hour_pos_report_menu->sort_order = 10;
        $sales_per_hour_pos_report_menu->flag_end = 1;
        $sales_per_hour_pos_report_menu->save();

        // create menu folder 'Tax' 92
        $tax_report_folder = new Menu;
        $tax_report_folder->parent_id = $report_module->id; //for root menu
        $tax_report_folder->type = 2; // 2 = folder
        $tax_report_folder->path = "62,92"; // path menu id for easy get structur
        $tax_report_folder->name = "Tax";
        $tax_report_folder->description = "Menu folder for report tax";
        $tax_report_folder->sort_order = 4;
        $tax_report_folder->flag_end = 0;
        $tax_report_folder->save();

        // create menu file 'History Send FTP Tax Report 93
        $history_send_tax_report_menu = new Menu;
        $history_send_tax_report_menu->parent_id = $tax_report_folder->id; //for root menu
        $history_send_tax_report_menu->type = 1; // 1 = file
        $history_send_tax_report_menu->path = "62,92,93"; // path menu id for easy get structur
        $history_send_tax_report_menu->name = "History Send FTP Tax Report";
        $history_send_tax_report_menu->description = "Menu for report history send ftp tax";
        $history_send_tax_report_menu->url = "report/tax/history-send-ftp";
        $history_send_tax_report_menu->permission_menu = "C,R,U,D";
        $history_send_tax_report_menu->sort_order = 1;
        $history_send_tax_report_menu->flag_end = 1;
        $history_send_tax_report_menu->save();

        // create menu folder 'General Configurations' 94
        $configurations_folder = new Menu;
        $configurations_folder->parent_id = $application_module->id; //for root menu
        $configurations_folder->type = 2; // 2 = folder
        $configurations_folder->path = "1,94"; // path menu id for easy get structur
        $configurations_folder->name = "General Configurations";
        $configurations_folder->description = "Menu folder for manage general configurations";
        $configurations_folder->sort_order = 2;
        $configurations_folder->flag_end = 0;
        $configurations_folder->save();

        // create menu file 'Configuration Group 95
        $configuration_group_menu = new Menu;
        $configuration_group_menu->parent_id = $configurations_folder->id; //for root menu
        $configuration_group_menu->type = 1; // 1 = file
        $configuration_group_menu->path = "1,94,95"; // path menu id for easy get structur
        $configuration_group_menu->name = "Configuration Group";
        $configuration_group_menu->description = "Menu for manage configuration group";
        $configuration_group_menu->url = "application/general-configuration/configuration-group";
        $configuration_group_menu->permission_menu = "C,R,U,D";
        $configuration_group_menu->sort_order = 1;
        $configuration_group_menu->flag_end = 1;
        $configuration_group_menu->save();

        // create menu file 'Configuration 96
        $configuration_menu = new Menu;
        $configuration_menu->parent_id = $configurations_folder->id; //for root menu
        $configuration_menu->type = 1; // 1 = file
        $configuration_menu->path = "1,94,96"; // path menu id for easy get structur
        $configuration_menu->name = "Configuration";
        $configuration_menu->description = "Menu for manage configuration";
        $configuration_menu->url = "application/general-configuration/configuration";
        $configuration_menu->permission_menu = "C,R,U,D";
        $configuration_menu->sort_order = 2;
        $configuration_menu->flag_end = 1;
        $configuration_menu->save();

        // create menu file 'Pettycash GL CC' 97
        $pettycash_glcc_menu = new Menu;
        $pettycash_glcc_menu->parent_id = $master_module->id; //for root menu
        $pettycash_glcc_menu->type = 1; // 1 = file
        $pettycash_glcc_menu->path = "17,97"; // path menu id for easy get structur
        $pettycash_glcc_menu->name = "Pettycash GL CC";
        $pettycash_glcc_menu->description = "Menu for manage data gl cc pettycash";
        $pettycash_glcc_menu->url = "master/pettycash-glcc";
        $pettycash_glcc_menu->permission_menu = "C,R,U,D";
        $pettycash_glcc_menu->sort_order = 12;
        $pettycash_glcc_menu->flag_end = 1;
        $pettycash_glcc_menu->save();

        // create menu file 'Opname Material Formula' 98
        $opname_material_formula_menu = new Menu;
        $opname_material_formula_menu->parent_id = $master_module->id; //for root menu
        $opname_material_formula_menu->type = 1; // 1 = file
        $opname_material_formula_menu->path = "17,98"; // path menu id for easy get structur
        $opname_material_formula_menu->name = "Opname Material Formula";
        $opname_material_formula_menu->description = "Menu for manage data opname material formula";
        $opname_material_formula_menu->url = "master/opname-material-formula";
        $opname_material_formula_menu->permission_menu = "C,R,U,D";
        $opname_material_formula_menu->sort_order = 13;
        $opname_material_formula_menu->flag_end = 1;
        $opname_material_formula_menu->save();

        // create menu file 'GR Plant Report' 99
        $gr_plant_report_menu = new Menu;
        $gr_plant_report_menu->parent_id = $inventory_report_folder->id; //for root menu
        $gr_plant_report_menu->type = 1; // 1 = file
        $gr_plant_report_menu->path = "62,69,76"; // path menu id for easy get structur
        $gr_plant_report_menu->name = "GR Plant Report";
        $gr_plant_report_menu->description = "Menu for report gr plant";
        $gr_plant_report_menu->url = "report/inventory/gr-plant";
        $gr_plant_report_menu->permission_menu = "C,R,U,D";
        $gr_plant_report_menu->sort_order = 3;
        $gr_plant_report_menu->flag_end = 1;
        $gr_plant_report_menu->save();

        // commit transaction
        DB::commit();
    }
}
