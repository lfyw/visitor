<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditWayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_way', function (Blueprint $table) {
            $table->unsignedInteger('audit_id')->comment('审核id');
            $table->unsignedInteger('way_id')->comment('路线id');
        });
        DB::statement("COMMENT ON TABLE audit_way is '审核路线中间表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_way');
    }
}
