<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('correo')->index();
            $table->string('token', 6);
            $table->enum('estado', ['activo', 'usado', 'expirado'])->default('activo');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_tokens');
    }
};