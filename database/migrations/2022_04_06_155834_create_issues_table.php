<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->morphs('visitorable');
            $table->unsignedBigInteger('gate_id')->comment('下发闸机id');
            $table->boolean('issue_status')->default(true)->comment('下发状态');
            $table->string('gate_number')->comment('闸机编号');
            $table->string('gate_ip')->comment('闸机ip');
            $table->string('rule')->comment('闸机方向');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issues');
    }
}
