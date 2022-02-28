<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passing_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('passageway_id')->index()->comment('通行通道id');
            $table->unsignedInteger('gate_id')->index()->comment('通行闸机id');
            $table->integer('issue_status')->nullable()->comment('下发状态:1 => 成功, 2 => 失败, 3 => 部分成功');
            $table->dateTime('passed_at')->comment('通过时间');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE passing_logs is '通行记录'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passing_logs');
    }
}
