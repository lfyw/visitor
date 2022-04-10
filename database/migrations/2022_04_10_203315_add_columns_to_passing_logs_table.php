<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPassingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passing_logs', function (Blueprint $table) {
            $table->string('name')->comment('姓名');
            $table->string('type')->comment('访客类型');
            $table->string('gender')->comment('性别');
            $table->integer('age')->comment('年龄');
            $table->string('phone')->comment('手机号');
            $table->string('unit')->comment('所属单位');
            $table->string('user_department')->nullable()->comment('被访者单位');
            $table->string('user_name')->nullable()->comment('被访者姓名');
            $table->string('reason')->nullable()->comment('访问事由');
            $table->string('relation')->nullable()->comment('访客关系');
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
