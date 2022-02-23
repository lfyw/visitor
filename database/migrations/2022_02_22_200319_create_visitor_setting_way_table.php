<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorSettingWayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_setting_way', function (Blueprint $table) {
            $table->unsignedInteger('visitor_setting_id')->index();
            $table->unsignedInteger('way_id')->index();
        });
        DB::statement("COMMENT ON TABLE visitor_setting_way is '访客设置与申请线路中间表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitor_setting_way');
    }
}
