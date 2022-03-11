<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'password' => bcrypt('admin123456'),
            'role_id' => Role::whereName(Role::SUPER_ADMIN)->value('id'),
            'real_name' => 'admin',
            'id_card' => '110101199003075912',
            'phone_number' => '14525754156'
        ]);
        User::factory()->count(5)->create();
    }
}
