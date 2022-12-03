<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MenuSeeder::class,
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class,
            ConfigurationsSeeder::class,
            PermissionsSeeder::class,
            PettycashGlCcSeeder::class
        ]);
    }
}
