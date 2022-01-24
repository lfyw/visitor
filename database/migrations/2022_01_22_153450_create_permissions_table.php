<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->string('type')->comment('功能类型：菜单、按钮');
            $table->string('route')->nullable()->comment('路由');
            $table->string('note')->nullable()->comment('备注');
            $table->nestedSet();
        });
        DB::statement("COMMENT ON TABLE permissions is '权限表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
