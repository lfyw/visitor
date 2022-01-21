<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassagewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passageways', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('通道名称');
            $table->string('note', 256)->nullable()->comment('备注');
        });
        DB::statement("COMMENT ON TABLE passageways is '通道表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passageways');
    }
}
