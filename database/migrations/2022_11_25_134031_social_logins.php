<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('social_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');

            $table->string('provider');
            $table->string('provider_id');
            $table->string('email');
            $table->text('last_token');

            $table->timestamps();

            $table->unique(['provider', 'email']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('social_logins');
    }
};
