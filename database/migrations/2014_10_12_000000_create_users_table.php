<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\UserStatus;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('登录用户名')->unique();
            $table->string('real_name')->comment('人员姓名');
            $table->unsignedBigInteger('department_id')->default(0)->comment('所属部门id');
            $table->unsignedInteger('user_type_id')->default(0)->comment('用户类型id');
            $table->unsignedInteger('role_id')->default(0)->comment('角色id');
            $table->string('user_status')->default(UserStatus::EMPLOYMENT->value)->comment('用户状态：在职、离职等');
            $table->string('duty')->nullable()->comment('职务');
            $table->string('id_card')->comment('身份证号');
            $table->string('phone_number')->comment('手机号');
            $table->unsignedInteger('issue_status')->nullable()->comment('下发状态: 1 => 成功;2 => 失败;3 => 部分成功');
            $table->string('password');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE users is '人员表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
