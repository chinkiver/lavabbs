<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsSendsTable extends Migration
{
    public function up()
    {
        Schema::create('sms_sends', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('code');
            $table->string('key')->index();
            $table->dateTime('send_time');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_sends');
    }
}
