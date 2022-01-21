<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatePassagewayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gate_passageway', function (Blueprint $table) {
           $table->unsignedBigInteger('gate_id')->index();
           $table->unsignedBigInteger('passageway_id')->index();
        });
        DB::statement("COMMENT ON TABLE gate_passageway is '闸门通道中间表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gate_passageway');
    }
}
