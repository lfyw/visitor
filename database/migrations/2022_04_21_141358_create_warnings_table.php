<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('姓名');
            $table->string('type')->comment('访客/人员类型');
            $table->string('gender')->comment('性别');
            $table->string('age')->comment('年龄');
            $table->string('id_card')->comment('身份证号');
            $table->string('phone')->comment('手机号');
            $table->string('unit')->nullable()->comment('所属单位');
            $table->string('user_real_name')->nullable()->comment('被访者姓名');
            $table->string('user_department')->nullable()->comment('被访者部门');
            $table->string('reason')->nullable()->comment('访问事由');
            $table->date('access_date_from')->nullable()->comment('访问开始时间');
            $table->date('access_date_to')->nullable()->comment('访问结束时间');
            $table->string('ways')->nullable()->comment('访问路线');
            $table->string('access_time_from')->nullable()->comment('开始访问时间');
            $table->string('access_time_to')->nullable()->comment('结束访问时间');
            $table->unsignedInteger('limiter')->default(0)->comment('访问次数限制');
            $table->string('relation')->nullable()->comment('访客关系');

            $table->string('warning_type')->comment('预警类型:超时未出、无进有出');
            $table->dateTime('warning_at')->nullable()->comment('预警时间');
            $table->tinyInteger('status')->nullable()->comment('处置状态：1 => 已离开 2 => 未离开');
            $table->text('note')->nullable()->comment('处置结果');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE warnings is '预警内容表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warnings');
    }
}
