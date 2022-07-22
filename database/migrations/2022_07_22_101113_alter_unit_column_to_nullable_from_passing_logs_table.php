<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUnitColumnToNullableFromPassingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passing_logs', function (Blueprint $table) {
            $table->string('unit')->nullable()->comment('所属单位')->change();
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
            //
        });
    }
}
