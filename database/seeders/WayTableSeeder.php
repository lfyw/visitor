<?php

namespace Database\Seeders;

use App\Models\Gate;
use App\Models\Passageway;
use App\Models\Way;
use Illuminate\Database\Seeder;

class WayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Way::factory()->has(
            Passageway::factory()->has(
                Gate::factory()->state(fn(array $attribute, Passageway $passageway) => ['location' => $passageway->name])->count(3)
            )->count(10)
        )->count(3)->create();
    }
}
