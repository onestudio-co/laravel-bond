<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('email');

        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email', 'deleted_at']);

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique(['email', 'deleted_at']);
        });
    }
};
