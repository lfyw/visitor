<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlacklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('姓名');
            $table->string('id_card')->comment('身份证号');
            $table->string('gender')->comment('性别');
            $table->string('phone')->comment('手机号');
            $table->string('reason')->nullable()->comment('加入黑名单原因');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE blacklists is '黑名单'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blacklists');
    }
}
