<?php

namespace Database\Seeders;

use App\Models\VisitorSetting;
use App\Models\VisitorType;
use App\Models\Way;
use Illuminate\Database\Seeder;

class VisitorSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //亲属访客设置
        $relationVisitorType = VisitorType::firstWhere('name', '家属');
        $relationVisitorSetting = VisitorSetting::updateOrCreate([
            'visitor_type_id' => $relationVisitorType->id
        ], [
            'apply_period' => 'year',
            'approver' => [
                [
                'type' => 'interviewee',
                'order' => 1
                ]
            ],
            'visitor_limiter' => 6,
            'visitor_relation' => true,
        ]);

        $relationVisitorSetting->ways()->sync(Way::inRandomOrder()->first()->value('id'));

        //临时访客访客设置
        $temporaryVisitorType = VisitorType::firstWhere('name', '临时访客');
        $temporaryVisitorTypeSetting = VisitorSetting::updateOrCreate([
            'visitor_type_id' => $temporaryVisitorType->id
        ], [
            'apply_period' => 'day',
            'approver' => [
                [
                'type' => 'interviewee',
                'order' => 1
                ],
            ],
            'visitor_limiter' => 1,
            'visitor_relation' => false,
        ]);
        $temporaryVisitorTypeSetting->ways()->sync(Way::inRandomOrder()->first()->value('id'));
    }
}
