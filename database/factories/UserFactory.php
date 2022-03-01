<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Role;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $names = [
            'lidakang' => '李达康',
            'zhaodonglai' => '赵东来',
            'houliangping' => '侯亮平',
            'gaoyuliang' => '高育良',
            'sharuijin' => '沙瑞金',
            'qitongwei' => '祁同伟',
            'jichangming' => '季昌明',
            'chenyanshi' => '陈岩石',
            'gaoxiaofeng' => '高小凤',
            'gaoxiaoqin' => '高小琴'
        ];

        $idCards = [
            '110101199003073255',
            '110101199003070716',
            '110101199003072519',
            '110101199003072172',
            '110101199003071313',
            '110101199003071997',
            '110101199003078398',
            '110101199003077352',
            '110101199003073212',
            '110101199001071272'
        ];


        return [
            'name' => ($this->faker->unique->randomElement(array_keys($names))),
            'real_name' => $this->faker->randomElement($names),
            'password' => bcrypt('1234567890'),
            'department_id' => $this->faker->randomElement(Department::pluck('id')->toArray()),
            'user_type_id' => $this->faker->randomElement(UserType::pluck('id')->toArray()),
            'role_id' => $this->faker->randomElement(Role::pluck('id')->toArray()),
            'user_status' => $this->faker->randomElement(\App\Enums\UserStatus::getValues()),
            'duty' => $this->faker->randomElement(['职员']),
            'id_card' => $this->faker->unique->randomElement($idCards),
            'phone_number' => $this->faker->phoneNumber,
        ];
    }
}
