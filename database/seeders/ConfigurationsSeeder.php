<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Configuration;
use App\Models\ConfigurationGroup;

class ConfigurationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configurationGroup = new ConfigurationGroup;
        $configurationGroup->name = 'APPLICATION';
        $configurationGroup->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'text';
        $configuration->label = 'Name Company';
        $configuration->description = 'label company in footer';
        $configuration->key = 'name';
        $configuration->value = 'Richeese Factory';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'text';
        $configuration->label = 'Title Web';
        $configuration->description = 'title web tabs browser';
        $configuration->key = 'title';
        $configuration->value = 'apps richeese factory';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'text';
        $configuration->label = 'Label Company';
        $configuration->description = 'label company in sidebar';
        $configuration->key = 'label';
        $configuration->value = 'RF QSR';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'text';
        $configuration->label = 'Logo Company';
        $configuration->description = 'logo company in sidebar';
        $configuration->key = 'logo';
        $configuration->value = 'images/logo/logo-rki-side.png';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'text';
        $configuration->label = 'Logo User';
        $configuration->description = 'top right user logo';
        $configuration->key = 'logo_user';
        $configuration->value = 'images/portrait/small/avatar-r.png';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'text';
        $configuration->label = 'Year';
        $configuration->description = 'year the Configuration was made';
        $configuration->key = 'year';
        $configuration->value = '2022';
        $configuration->option = '';
        $configuration->save();

        $configurationGroup = new ConfigurationGroup;
        $configurationGroup->name = 'FEATURE';
        $configurationGroup->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'select';
        $configuration->label = 'Sidebar';
        $configuration->description = 'status of the first sidebar access';
        $configuration->key = 'sidebar_collapse';
        $configuration->value = 'open';
        $configuration->option = json_encode(['open', 'close']);
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'select';
        $configuration->label = 'Fullscreen';
        $configuration->description = 'status hide/show fullscreen';
        $configuration->key = 'fullscreen_status';
        $configuration->value = 'hide';
        $configuration->option = json_encode(['show', 'hide']);
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'text';
        $configuration->label = 'Version CSS';
        $configuration->description = 'version css current web';
        $configuration->key = 'version_css';
        $configuration->value = '3';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'web';
        $configuration->type = 'text';
        $configuration->label = 'Version JS';
        $configuration->description = 'version js current web';
        $configuration->key = 'version_js';
        $configuration->value = '1';
        $configuration->option = '';
        $configuration->save();

        $configurationGroup = new ConfigurationGroup;
        $configurationGroup->name = 'GENERAL MASTER';
        $configurationGroup->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'general_master';
        $configuration->type = 'select2';
        $configuration->label = 'Role Area Manager';
        $configuration->description = 'role area manager';
        $configuration->key = 'role_am';
        $configuration->value = '2';
        $configuration->option = 'application/authentication/role/select?superadmin=true';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'general_master';
        $configuration->type = 'select2';
        $configuration->label = 'Role Regional Manager';
        $configuration->description = 'role regional manager';
        $configuration->key = 'role_rm';
        $configuration->value = '4';
        $configuration->option = 'application/authentication/role/select?superadmin=true';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'general_master';
        $configuration->type = 'select2';
        $configuration->label = 'Role Store Manager';
        $configuration->description = 'role Store manager';
        $configuration->key = 'role_sm';
        $configuration->value = '3';
        $configuration->option = 'application/authentication/role/select?superadmin=true';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->for = 'general_master';
        $configuration->type = 'select2';
        $configuration->label = 'Role Store Crew';
        $configuration->description = 'role Store Crew';
        $configuration->key = 'role_sc';
        $configuration->value = '5';
        $configuration->option = 'application/authentication/role/select?superadmin=true';
        $configuration->save();

        $configurationGroup = new ConfigurationGroup;
        $configurationGroup->name = 'PETTYCASH';
        $configurationGroup->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'text';
        $configuration->label = 'Vendor ID Outlet';
        $configuration->description = 'default vendor id for outlet';
        $configuration->key = 'vendor_id_outlet';
        $configuration->value = '700000';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'text';
        $configuration->label = 'Vendor ID DC';
        $configuration->description = 'default vendor id for DC';
        $configuration->key = 'vendor_id_dc';
        $configuration->value = 'INV_LOG';
        $configuration->option = '';
        $configuration->save();

        $configurationGroup = new ConfigurationGroup;
        $configurationGroup->name = 'ASSET SO';
        $configurationGroup->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'text';
        $configuration->label = 'Date DC Submit Asset SO';
        $configuration->description = 'Date system submit dc asset so';
        $configuration->key = 'date_submit_dc_asset_so';
        $configuration->value = '27';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'text';
        $configuration->label = 'Status Asset SO DC';
        $configuration->description = 'Status asset so dc';
        $configuration->key = 'status_dc_asset_so';
        $configuration->value = 'Not Running';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'text';
        $configuration->label = 'Key Notification Asset SO';
        $configuration->description = 'Key created from notification system for asset so';
        $configuration->key = 'key_notification_asset_so';
        $configuration->value = 'asset-so';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'textarea';
        $configuration->label = 'Cost Center Exclude';
        $configuration->description = 'Cost center not include in asset so';
        $configuration->key = 'cost_center_exclude';
        $configuration->value = 'C1200005,C1100016,C3311101,C2100001,C1100010,C3320000,C1200002,C1100001,C1200003,C1200004,C1100004,C1100002,C1100014,C1100005,C1200001,C1100006,C2200001,C1100008,C3121103,C3121104,C3121102,C3121101,C3141115,C3141116,C1100003,C1100012,C3131110,C1100013,C3111101,C3111102,C3112105,C3112102,C3112201,C3112202,C3113104,C3111106,C3112105,C3112501';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'textarea';
        $configuration->label = 'Email Depart Asset';
        $configuration->description = 'List email depart asset';
        $configuration->key = 'email_depart_asset';
        $configuration->value = 'asset1.qsr@richeesefactory.com,asset2.qsr@richeesefactory.com,asset3.qsr@richeesefactory.com';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'select';
        $configuration->label = 'Status Generate Asset SO';
        $configuration->description = 'Flag for company run asset so or not';
        $configuration->key = 'status_generate_asset_so';
        $configuration->value = 'true';
        $configuration->option = '["true","false"]';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'text';
        $configuration->label = 'Date Outlet Submit Asset SO';
        $configuration->description = 'Date system submit outlet asset so';
        $configuration->key = 'date_submit_outlet_asset_so';
        $configuration->value = '8';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'text';
        $configuration->label = 'Date DC Generate Asset SO';
        $configuration->description = 'Date system generate dc asset so';
        $configuration->key = 'date_generate_dc_asset_so';
        $configuration->value = '10';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'text';
        $configuration->label = 'Status Asset SO Outlet';
        $configuration->description = 'Status asset so outlet';
        $configuration->key = 'status_outlet_asset_so';
        $configuration->value = 'Not Running';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'financeacc';
        $configuration->type = 'text';
        $configuration->label = 'Date Outlet Generate Asset SO';
        $configuration->description = 'Date system generate outlet asset so';
        $configuration->key = 'date_generate_outlet_asset_so';
        $configuration->value = '8';
        $configuration->option = '';
        $configuration->save();

        $configurationGroup = new ConfigurationGroup;
        $configurationGroup->name = 'GI / GR';
        $configurationGroup->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'inventory';
        $configuration->type = 'textarea';
        $configuration->label = 'Validate Mat. Code (KG)';
        $configuration->description = 'list mat.code for validate input batch kg';
        $configuration->key = 'mat_code_batch';
        $configuration->value = '1000406,1000110,1000112,1000214,1000393,4000133,4000141,4000140,4000128,4000049,4000046,4000148,1000433,1000572,1000573,1000574,1000582,1000585';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'inventory';
        $configuration->type = 'select';
        $configuration->label = 'Lock GI & GR';
        $configuration->description = 'flag for lock GI / GR Upload';
        $configuration->key = 'lock_gi_gr';
        $configuration->value = 'unlock';
        $configuration->option = '["lock","unlock"]';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'inventory';
        $configuration->type = 'textarea';
        $configuration->label = 'Allow Vendor &gt; 130 D';
        $configuration->description = 'list vendor id to allow more than 130 days';
        $configuration->key = 'vendor_allow';
        $configuration->value = '700299,700334,700295,700633,700705,701779,700384,700653,700497,700397,700255,700719,700977,701181,701236,701023,701339,700797,701320,702025,700520,700080,702069,702274,702425,702274';
        $configuration->option = '';
        $configuration->save();

        $configurationGroup = new ConfigurationGroup;
        $configurationGroup->name = 'OPNAME';
        $configurationGroup->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'inventory';
        $configuration->type = 'textarea';
        $configuration->label = 'Skip Material Upload';
        $configuration->description = 'list mat.code for skip to upload sap';
        $configuration->key = 'mat_code_skip_opname';
        $configuration->value = '9071000,9071001,9071002,9071003,9071004,9071005,9071012,9071013,9071014,9071015,9071016,9071017,1000194, 9023139';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'inventory';
        $configuration->type = 'textarea';
        $configuration->label = 'Hide Material';
        $configuration->description = 'list mat.code for hide qty sap';
        $configuration->key = 'mat_code_hide_opname';
        $configuration->value = '1000179,1000236,1000460,3000034,3000035,3000036,3000037,3000038,3000039,3000041,3000044,3000046,3000047,3000048,3000051,3000052,3000053,3000054,3000055,3000056,3000057,3000058,4000046,4000049,4000128,4000133,4000141,4000144,4000148,4000157,4000158,4000203,4000204,9400013,9400015,9400016,9400017,9400019,9400021,9800001,1000514,1000515,1000517,1000512,1000513,1000519,3000064,3000065,3000066,3100010,3000067,3000068, 1000597, 1000599, 1000598, 1000600, 1000601, 1000602, 3000070, 3000071';
        $configuration->option = '';
        $configuration->save();

        $configurationGroup = new ConfigurationGroup;
        $configurationGroup->name = 'USED OIL';
        $configurationGroup->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'inventory';
        $configuration->type = 'text';
        $configuration->label = 'Bank Richeese';
        $configuration->description = 'list bank richeese for transfer deposit used oil';
        $configuration->key = 'uo_bank_richeese';
        $configuration->value = 'Mandiri 220,BCA,Syariah,Mandiri AMO,';
        $configuration->option = '';
        $configuration->save();

        $configuration = new Configuration;
        $configuration->configuration_group_id = $configurationGroup->id;
        $configuration->company_id = 1;
        $configuration->for = 'inventory';
        $configuration->type = 'text';
        $configuration->label = 'Email Confirmation FA';
        $configuration->description = 'list email for send confirmation deposit vendor';
        $configuration->key = 'uo_email_fa';
        $configuration->value = '';
        $configuration->option = '';
        $configuration->save();

    }
}
