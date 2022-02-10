<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => '超级管理员'
        ]);
        Role::create([
            'name' => '系统管理员'
        ]);
        Role::create([
            'name' => '部门管理员'
        ]);
        Role::create([
            'name' => '内部员工'
        ]);
    }
}
