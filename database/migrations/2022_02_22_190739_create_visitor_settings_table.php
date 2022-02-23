<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('visitor_type_id')->comment('访客类型id');
            $table->string('apply_period')->comment('申请有效期：day => 日, year => 年');
            $table->json('approver')->nullable()->comment('审批人');
            $table->integer('visitor_limiter')->default(0)->comment('每位员工该类访客人数限制');
            $table->boolean('visitor_relation')->default(false)->comment('是否开启访客关系选择');
        });
        DB::statement("COMMENT ON TABLE visitor_settings is '访客设置表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitor_settings');
    }
}
