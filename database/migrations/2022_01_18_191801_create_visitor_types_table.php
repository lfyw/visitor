<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('访客类型名称');
            $table->string('note', 256)->nullable()->comment('备注');
        });
        DB::statement("COMMENT ON TABLE visitor_types is '访客类型表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitor_types');
    }
}
