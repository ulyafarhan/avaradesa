<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->string('nomor_surat', 100)->nullable()->after('nomor_registrasi');
            $table->index('nomor_surat');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->dropIndex(['nomor_surat']);
            $table->dropColumn('nomor_surat');
        });
    }
};
