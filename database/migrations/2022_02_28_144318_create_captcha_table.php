<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaptchaTable extends Migration
{
    public function up()
    {
        Schema::create('captcha', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('phone');
            $table->string('code');
            $table->string('key')->index();
            $table->dateTime('send_time');
            $table->dateTime('expired_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('captcha');
    }
}
