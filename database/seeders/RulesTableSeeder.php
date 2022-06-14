<?php

namespace Database\Seeders;

use App\Models\Rule;
use Illuminate\Database\Seeder;

class RulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rule::updateOrCreate([
            'name' => '预警设置'
        ], [
            'value' => [
                'no_out' => [
                    [
                        'user_type_id' => 1,
                        'duration' => 9
                    ],
                    [
                        'user_type_id' => 2,
                        'duration' => 5
                    ],
                    [
                        'user_type_id' => 3,
                        'duration' => 5
                    ],
                ],
                'scope' => [
                    1, 2
                ],
                'board' => [
                    1, 2
                ],
            ]
        ]);
    }
}
