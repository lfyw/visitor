<?php

namespace Database\Seeders;

use App\Models\PassingLog;
use Illuminate\Database\Seeder;

class PassingLogTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PassingLog::factory()->count(5)->create();
    }
}
