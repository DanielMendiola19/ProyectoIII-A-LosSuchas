<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reporte_diario', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('vendidos');
            $table->integer('stock_final');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_diario');
    }
};