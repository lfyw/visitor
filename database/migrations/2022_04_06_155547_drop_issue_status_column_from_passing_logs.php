<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropIssueStatusColumnFromPassingLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passing_logs', function (Blueprint $table) {
            $table->dropColumn('issue_status');
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
            $table->integer('issue_status')->nullable()->comment('下发状态:1 => 成功, 2 => 失败, 3 => 部分成功');
        });
    }
}
