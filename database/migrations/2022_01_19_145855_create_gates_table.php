<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\GateRule;

class CreateGatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gates', function (Blueprint $table) {
            $table->id();
            $table->string('number')->comment('闸机编号');
            $table->string('type')->comment('闸机型号');
            $table->string('location')->comment('闸机位置');
            $table->string('rule')->default(GateRule::IN->value)->comment('进出规则');
            $table->string('note', 256)->nullable()->comment('备注');
        });
        DB::statement("COMMENT ON TABLE gates is '闸机表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gates');
    }
}
