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
        // 1. Add timestamps
        Schema::table('keluarga', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('pengaturan_frontend', function (Blueprint $table) {
            $table->timestamps();
        });

        // 2. Add soft deletes
        Schema::table('administrators', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('penduduk', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('inventaris_fasilitas', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('bot_knowledges', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 3. Make dokumen_bukti nullable
        Schema::table('mutasi_penduduk', function (Blueprint $table) {
            $table->string('dokumen_bukti')->nullable()->change();
        });

        // 4. Add unique constraint on penduduk (no_kk, nik)
        Schema::table('penduduk', function (Blueprint $table) {
            $table->unique(['no_kk', 'nik'], 'penduduk_no_kk_nik_unique');
        });

        // 5. Add Foreign Key constraints
        Schema::table('penduduk', function (Blueprint $table) {
            $table->foreign('agama_id')->references('id')->on('ref_agama')->nullOnDelete();
            $table->foreign('pendidikan_id')->references('id')->on('ref_pendidikan')->nullOnDelete();
            $table->foreign('pekerjaan_id')->references('id')->on('ref_pekerjaan')->nullOnDelete();
            $table->foreign('status_perkawinan_id')->references('id')->on('ref_status_perkawinan')->nullOnDelete();
            $table->foreign('status_keluarga_id')->references('id')->on('ref_status_keluarga')->nullOnDelete();
        });

        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->foreign('kategori_id')->references('id')->on('kategori_informasi')->nullOnDelete();
        });

        Schema::table('inventaris_fasilitas', function (Blueprint $table) {
            $table->foreign('jenis_fasilitas_id')->references('id')->on('ref_jenis_fasilitas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 5. Drop FKs
        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropForeign(['agama_id']);
            $table->dropForeign(['pendidikan_id']);
            $table->dropForeign(['pekerjaan_id']);
            $table->dropForeign(['status_perkawinan_id']);
            $table->dropForeign(['status_keluarga_id']);
        });

        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
        });

        Schema::table('inventaris_fasilitas', function (Blueprint $table) {
            $table->dropForeign(['jenis_fasilitas_id']);
        });

        // 4. Drop unique constraint
        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropUnique('penduduk_no_kk_nik_unique');
        });

        // 3. Make dokumen_bukti required again
        Schema::table('mutasi_penduduk', function (Blueprint $table) {
            $table->string('dokumen_bukti')->nullable(false)->change();
        });

        // 2. Drop soft deletes
        Schema::table('administrators', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('inventaris_fasilitas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('bot_knowledges', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // 1. Drop timestamps
        Schema::table('keluarga', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('pengaturan_frontend', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
