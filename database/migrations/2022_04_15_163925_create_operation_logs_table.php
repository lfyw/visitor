<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('module')->comment('操作模块');
            $table->string('content')->comment('操作内容');
            $table->unsignedInteger('user_id')->default(0)->comment('操作人');
            $table->ipAddress('operated_ip')->comment('操作ip');
            $table->dateTime('operated_at')->comment('操作时间');
        });
        DB::statement("COMMENT ON TABLE operation_logs is '操作日志表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_logs');
    }
}
