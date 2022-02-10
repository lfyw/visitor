<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserType::create([
            'name' => '运行人员',
            'note' => '倒班员工，每次进入生产区不能超过9小时'
        ]);
        UserType::create([
            'name' => '行政人员',
            'note' => '行政办公人员，每次进入生产区不能超过5小时'
        ]);
        UserType::create([
            'name' => '外协人员',
            'note' => '外协人员，每次进入生产区不能超过12小时'
        ]);
    }
}
