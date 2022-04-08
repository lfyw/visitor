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
            $table->string('id_card')->comment('身份证号');
            $table->unsignedInteger('gate_id')->index()->comment('通行闸机id');
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
