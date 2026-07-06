<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_agama', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nama', 20)->unique();
            $table->timestamps();
        });

        Schema::create('ref_pendidikan', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        Schema::create('ref_pekerjaan', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        Schema::create('ref_status_perkawinan', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nama', 20)->unique();
            $table->timestamps();
        });

        Schema::create('ref_status_keluarga', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nama', 30)->unique();
            $table->timestamps();
        });

        Schema::create('kategori_informasi', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nama', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        Schema::create('ref_jenis_fasilitas', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('nama', 50)->unique();
            $table->timestamps();
        });

        Schema::create('knowledge_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('bot_knowledge_id')->constrained('bot_knowledges')->cascadeOnDelete();
            $table->string('kata_kunci', 100);
            $table->timestamps();
            $table->index('kata_kunci');
        });

        // Relasi circular: tambah is_kepala_keluarga ke penduduk + kolom FK baru
        Schema::table('penduduk', function (Blueprint $table) {
            $table->boolean('is_kepala_keluarga')->default(false)->after('status_keluarga');
            $table->unsignedTinyInteger('agama_id')->nullable()->after('is_kepala_keluarga');
            $table->unsignedTinyInteger('pendidikan_id')->nullable()->after('agama_id');
            $table->unsignedTinyInteger('pekerjaan_id')->nullable()->after('pendidikan_id');
            $table->unsignedTinyInteger('status_perkawinan_id')->nullable()->after('pekerjaan_id');
            $table->unsignedTinyInteger('status_keluarga_id')->nullable()->after('status_perkawinan_id');
        });

        // kategori_informasi FK di informasi_publik
        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->unsignedTinyInteger('kategori_id')->nullable()->after('kategori');
        });

        // jenis_fasilitas FK di inventaris_fasilitas
        Schema::table('inventaris_fasilitas', function (Blueprint $table) {
            $table->unsignedTinyInteger('jenis_fasilitas_id')->nullable()->after('jenis_fasilitas');
        });

        // Insert data lookup default
        $agama = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
        $pendidikan = ['Tidak/Belum Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1-D3', 'D4/S1', 'S2', 'S3'];
        $pekerjaan = ['Belum/Tidak Bekerja', 'Pelajar/Mahasiswa', 'PNS', 'TNI/Polri', 'Karyawan Swasta', 'Wiraswasta', 'Petani', 'Nelayan', 'Buruh', 'Ibu Rumah Tangga', 'Pensiunan', 'Lainnya'];
        $kawin = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
        $keluarga = ['Kepala Keluarga', 'Istri', 'Anak', 'Orang Tua', 'Mertua', 'Famili Lain', 'Lainnya'];
        $kategori = ['Berita', 'Pengumuman', 'Agenda', 'Artikel', 'Kegiatan'];
        $fasilitas = ['Gedung', 'Jalan', 'Jembatan', 'Drainase', 'Air Bersih', 'Sanitasi', 'Pendidikan', 'Kesehatan', 'Olahraga', 'Ibadah', 'Lainnya'];

        foreach ($agama as $i) { DB::table('ref_agama')->insert(['nama' => $i]); }
        foreach ($pendidikan as $i) { DB::table('ref_pendidikan')->insert(['nama' => $i]); }
        foreach ($pekerjaan as $i) { DB::table('ref_pekerjaan')->insert(['nama' => $i]); }
        foreach ($kawin as $i) { DB::table('ref_status_perkawinan')->insert(['nama' => $i]); }
        foreach ($keluarga as $i) { DB::table('ref_status_keluarga')->insert(['nama' => $i]); }
        foreach ($kategori as $i) { DB::table('kategori_informasi')->insert(['nama' => $i, 'slug' => str()->slug($i)]); }
        foreach ($fasilitas as $i) { DB::table('ref_jenis_fasilitas')->insert(['nama' => $i]); }
    }

    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropColumn(['is_kepala_keluarga', 'agama_id', 'pendidikan_id', 'pekerjaan_id', 'status_perkawinan_id', 'status_keluarga_id']);
        });
        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->dropColumn('kategori_id');
        });
        Schema::table('inventaris_fasilitas', function (Blueprint $table) {
            $table->dropColumn('jenis_fasilitas_id');
        });
        Schema::dropIfExists('knowledge_keywords');
        Schema::dropIfExists('ref_jenis_fasilitas');
        Schema::dropIfExists('kategori_informasi');
        Schema::dropIfExists('ref_status_keluarga');
        Schema::dropIfExists('ref_status_perkawinan');
        Schema::dropIfExists('ref_pekerjaan');
        Schema::dropIfExists('ref_pendidikan');
        Schema::dropIfExists('ref_agama');
    }
};
