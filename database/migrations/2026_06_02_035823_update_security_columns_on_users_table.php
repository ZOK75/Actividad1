<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
        // Guarda la fecha y hora hasta la cual el usuario está bloqueado
            $table->timestamp('banned_until')->nullable()->after('password');
        // Cuenta cuántas oleadas de bloqueos lleva acumuladas
            $table->integer('blocked_turns')->default(0)->after('banned_until');
        });
    }

public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['banned_until', 'blocked_turns']);
        });
    }

};
