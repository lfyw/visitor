<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHandlerIdToWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warnings', function (Blueprint $table) {
            $table->unsignedBigInteger('handler_id')->default(0)->comment('处置人id');
            $table->dateTime('handled_at')->nullable()->comment('处置时间');
            $table->unsignedBigInteger('visitor_id')->comment('访客id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warnings', function (Blueprint $table) {
            $table->dropColumn('handler_id');
            $table->dropColumn('handled_at');
            $table->dropColumn('visitor_id');
        });
    }
}
