<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('姓名');
            $table->unsignedInteger('visitor_type_id')->comment('访客类型id');
            $table->string('id_card')->comment('身份证号');
            $table->string('gender')->comment('性别');
            $table->integer('age')->comment('年龄');
            $table->string('phone')->comment('手机号');
            $table->string('unit')->nullable()->comment('所属单位');
            $table->string('reason')->nullable()->comment('访问事由');
            $table->string('relation')->nullable()->comment('访客关系');

            $table->unsignedInteger('user_id')->comment('被访问者id');

            $table->unsignedInteger('limiter')->default(0)->comment('访问次数');
            $table->date('access_date_from')->nullable()->comment('起始访问期限');
            $table->date('access_date_to')->nullable()->comment('结束访问期限');
            $table->string('access_time_from')->nullable()->comment('起始访问时间');
            $table->string('access_time_to')->nullable()->comment('结束访问时间段');

            $table->unsignedInteger('access_count')->default(0)->comment('访问记录');

            $table->tinyInteger('issue_status')->nullable()->comment('下发状态：1 => 成功 2 => 失败 3 => 部分成功');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE visitors is '访客表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitors');
    }
}
