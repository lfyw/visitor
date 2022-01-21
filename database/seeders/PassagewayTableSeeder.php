<?php

namespace Database\Seeders;

use App\Models\Gate;
use App\Models\Passageway;
use Illuminate\Database\Seeder;

class PassagewayTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Passageway::factory()->has(
            Gate::factory()->state(fn(array $attribute, Passageway $passageway) => ['location' => $passageway->name])->count(3)
        )->count(10)->create();
    }
}
