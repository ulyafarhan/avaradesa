<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris_fasilitas', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nama_fasilitas', 150);
            $table->string('jenis_fasilitas', 50);
            $table->text('deskripsi')->nullable();
            $table->string('lokasi', 200)->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->year('tahun_dibangun')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status_penggunaan', ['Aktif', 'Tidak Aktif', 'Renovasi'])->default('Aktif');
            $table->boolean('is_publik')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris_fasilitas');
    }
};
