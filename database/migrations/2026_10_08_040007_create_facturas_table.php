<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->date('fecha_emision');
            $table->decimal('total', 12, 2);
            $table->string('formato', 20)->default('PDF');
            $table->foreignId('venta_id')->unique()->constrained('ventas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
