<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassagewayWayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passageway_way', function (Blueprint $table) {
            $table->unsignedBigInteger('passageway_id')->index();
            $table->unsignedBigInteger('way_id')->index();
        });
        DB::statement("COMMENT ON TABLE passageway_way is '路线通道中间表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passageway_way');
    }
}
