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
            $table->string('gender')->comment('性别');
            $table->string('age')->comment('年龄');
            $table->string('phone')->comment('手机号');
            $table->string('unit')->comment('单位');
            $table->unsignedInteger('user_id')->comment('被访问者');
            $table->unsignedInteger('visitor_type_id')->comment('访客类型id');
            $table->date('access_date_from')->comment('访问日期起');
            $table->date('access_date_to')->comment('访问日期止');
            $table->string('reason')->nullable()->comment('访问事由');
            $table->string('relation')->nullable()->comment('访客关系');
            $table->string('access_time_from')->nullable()->comment('访问时间起');
            $table->string('access_time_to')->nullable()->comment('访问时间止');
            $table->integer('limiter')->default(0)->comment('访问次数限制');

            $table->string('refused_reason')->nullable()->comment('拒绝理由');
            $table->integer('audit_status')->default(\App\Enums\AuditStatus::WAITING->value)->comment('审核状态:1 => 待审核 2 => 通过 3 => 审核拒绝');
            $table->softDeletes();
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
