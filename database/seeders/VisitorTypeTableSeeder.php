<?php

namespace Database\Seeders;

use App\Models\VisitorType;
use Illuminate\Database\Seeder;

class VisitorTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VisitorType::create([
            'name' => '家属'
        ]);
        VisitorType::create([
            'name' => '临时访客'
        ]);
    }
}
