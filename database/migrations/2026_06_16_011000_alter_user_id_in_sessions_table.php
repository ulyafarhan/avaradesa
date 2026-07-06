<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi untuk mengubah tipe kolom user_id pada tabel sessions menjadi string.
 * Hal ini diperlukan untuk menampung format primary key bertipe ULID pada model User/Administrator.
 */
return new class extends Migration
{
    /**
     * Menjalankan proses perubahan kolom database.
     */
    public function up(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->string('user_id', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->change();
        });
    }
};
