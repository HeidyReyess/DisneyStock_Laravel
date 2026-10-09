<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad_stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->date('fecha_actualizacion')->nullable();
            $table->foreignId('producto_id')->unique()->constrained('productos')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
