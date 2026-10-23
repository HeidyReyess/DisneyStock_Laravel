<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // [6] Hace categoria_id NOT NULL y cambia FK a restrictOnDelete
    // Seguro: se verificó que no hay productos con categoria_id NULL antes de correr
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Eliminar la FK anterior (nullOnDelete)
            $table->dropForeign(['categoria_id']);

            // Cambiar columna a NOT NULL
            $table->unsignedBigInteger('categoria_id')->nullable(false)->change();

            // Recrear FK con restrictOnDelete — no se puede borrar una categoría con productos
            $table->foreign('categoria_id')
                ->references('id')
                ->on('categorias')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);

            $table->unsignedBigInteger('categoria_id')->nullable()->change();

            $table->foreign('categoria_id')
                ->references('id')
                ->on('categorias')
                ->nullOnDelete();
        });
    }
};
