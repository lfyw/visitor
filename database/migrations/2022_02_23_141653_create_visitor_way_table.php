<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorWayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_way', function (Blueprint $table) {
            $table->unsignedBigInteger('visitor_id');
            $table->unsignedBigInteger('way_id');
        });
        DB::statement("COMMENT ON TABLE visitor_way is '访客线路中间表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitor_way');
    }
}
