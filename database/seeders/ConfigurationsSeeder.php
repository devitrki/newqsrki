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

    }
}
