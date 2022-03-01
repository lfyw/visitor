<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auditors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_id')->comment('审核id');
            $table->unsignedBigInteger('user_id')->comment('审核人id');
            $table->integer('audit_status')->default(\App\Enums\AuditStatus::WAITING->value)->comment('审核状态:1 => 待审核 2 => 通过 3 => 审核拒绝');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE auditors is '审核人'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auditors');
    }
}
