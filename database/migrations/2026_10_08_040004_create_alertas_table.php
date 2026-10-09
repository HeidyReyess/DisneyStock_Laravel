<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_alerta', 80)->default('stock_bajo');
            $table->text('mensaje');
            $table->date('fecha_alerta');
            $table->date('fecha_resolucion')->nullable();
            $table->enum('estado', ['activa', 'resuelta'])->default('activa');
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
