<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('姓名');
            $table->string('id_card')->comment('身份证号');
            $table->string('phone')->comment('手机号');
            $table->string('department')->comment('单位');
            $table->unsignedInteger('user_id')->comment('被访问者');
            $table->unsignedInteger('visitor_type_id')->comment('访客类型id');
            $table->date('access_date_from')->comment('访问日期起');
            $table->date('access_date_to')->comment('访问日期止');
            $table->string('reason')->nullable()->comment('访问事由');
            $table->string('relation')->nullable()->comment('访客关系');

            $table->json('access_time')->nullable()->comment('访问时间');
            $table->integer('access_count')->default(0)->comment('访问次数');

            $table->string('refused_reason')->nullable()->comment('拒绝理由');

            $table->boolean('status')->nullable()->comment('审核状态');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE audits is '临时访客审核表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audits');
    }
}
