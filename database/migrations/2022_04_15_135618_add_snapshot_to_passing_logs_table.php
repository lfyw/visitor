<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSnapshotToPassingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passing_logs', function (Blueprint $table) {
            $table->string('snapshot')->nullable()->comment('抓拍照片地址');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('passing_logs', function (Blueprint $table) {
            $table->dropColumn('snapshot');
        });
    }
}
