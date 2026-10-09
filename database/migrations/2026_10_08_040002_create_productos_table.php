<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->decimal('precio_venta', 12, 2)->default(0);
            $table->decimal('precio_compra', 12, 2)->default(0);
            $table->date('fecha_ingreso')->useCurrent();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->string('proveedor', 200)->nullable();
            $table->string('imagen', 255)->nullable();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
