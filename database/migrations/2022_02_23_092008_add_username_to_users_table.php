<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 添加用户姓名
 * Class AddUsernameToUsersTable
 */
class AddUsernameToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->string('username');
        });
    }

    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('username');
        });
    }
}
