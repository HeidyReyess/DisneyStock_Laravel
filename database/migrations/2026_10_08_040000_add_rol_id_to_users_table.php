<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Esta migración queda vacía porque la FK rol_id ya se agregó
// directamente en la migración original de users.
return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};
