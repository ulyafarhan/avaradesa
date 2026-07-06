<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administrators', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->enum('role', ['kepala_desa', 'sekdes', 'operator'])->default('operator');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrators');
    }
};
