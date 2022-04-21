<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('visitor_id')->comment('访客id');
            $table->unsignedInteger('way_id')->comment('路线id');
            $table->unsignedInteger('gate_id')->comment('闸机id');
            $table->unsignedInteger('passageway_id')->comment('通道id');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE scenes is '场景表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scenes');
    }
}
