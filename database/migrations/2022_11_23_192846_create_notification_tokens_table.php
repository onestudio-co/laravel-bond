<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');

            $table->string('token')->unique();
            $table->string('device_id');
            $table->string('device_type');
            $table->unique(['device_id', 'device_type']);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_tokens');
    }
};
