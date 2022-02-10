<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr = ['一', '二', '三'];

        foreach($arr as $key => $value){
            $department = Department::create([
                'name' => '部门' . $value,
                'address' => '本地'
            ]);
            foreach($arr as $k => $v){
                Department::create([
                    'name' => '科室' . $v,
                    'address' => '本地',
                    'parent_id' => $department->id
                ]);
            }
        }
    }
}
