<?php

namespace Database\Seeders;

use App\Models\Gate;
use Illuminate\Database\Seeder;

class GateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gates = Gate::factory()->count(20)->make();
        Gate::insert($gates->toArray());
    }
}
