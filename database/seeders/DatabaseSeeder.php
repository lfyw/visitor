<?php

namespace Database\Seeders;

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
            WayTableSeeder::class,
            DepartmentTableSeeder::class,
            RoleTableSeeder::class,
            UserTypeTableSeeder::class,
            VisitorTypeTableSeeder::class,
            VisitorSettingTableSeeder::class,
            UserTableSeeder::class
        ]);
    }
}
