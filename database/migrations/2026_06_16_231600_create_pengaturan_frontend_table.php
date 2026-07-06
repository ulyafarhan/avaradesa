<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('pengaturan_frontend', function (Blueprint $table) {
            $table->string('kunci', 50)->primary();
            $table->text('nilai')->nullable();
            $table->string('tipe_data', 20)->default('string');
            $table->string('deskripsi')->nullable();
        });

        // Seed data awal untuk konten publik
        $data = [
            [
                'kunci' => 'nama_sekdes',
                'nilai' => 'Nama Sekretaris Desa',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama lengkap Sekretaris Desa',
            ],
            [
                'kunci' => 'foto_sekdes',
                'nilai' => null,
                'tipe_data' => 'string',
                'deskripsi' => 'Foto Sekretaris Desa',
            ],
            [
                'kunci' => 'nama_operator',
                'nilai' => 'Nama Operator Layanan',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama lengkap Operator Layanan',
            ],
            [
                'kunci' => 'foto_operator',
                'nilai' => null,
                'tipe_data' => 'string',
                'deskripsi' => 'Foto Operator Layanan',
            ],
            [
                'kunci' => 'foto_kepala_desa',
                'nilai' => null,
                'tipe_data' => 'string',
                'deskripsi' => 'Foto Kepala Desa',
            ],
            [
                'kunci' => 'telepon_operator',
                'nilai' => '0812-xxxx-xxxx',
                'tipe_data' => 'string',
                'deskripsi' => 'Nomor WhatsApp/Telepon Operator Layanan',
            ],
            [
                'kunci' => 'medsos_facebook',
                'nilai' => 'https://facebook.com',
                'tipe_data' => 'string',
                'deskripsi' => 'Link akun Facebook resmi desa',
            ],
            [
                'kunci' => 'medsos_instagram',
                'nilai' => 'https://instagram.com',
                'tipe_data' => 'string',
                'deskripsi' => 'Link akun Instagram resmi desa',
            ],
            [
                'kunci' => 'medsos_twitter',
                'nilai' => 'https://twitter.com',
                'tipe_data' => 'string',
                'deskripsi' => 'Link akun Twitter/X resmi desa',
            ],
            [
                'kunci' => 'medsos_youtube',
                'nilai' => 'https://youtube.com',
                'tipe_data' => 'string',
                'deskripsi' => 'Link akun YouTube resmi desa',
            ],
            [
                'kunci' => 'tahun_anggaran',
                'nilai' => date('Y'),
                'tipe_data' => 'string',
                'deskripsi' => 'Tahun anggaran berjalan',
            ],
            [
                'kunci' => 'alamat_kantor',
                'nilai' => 'Jalan Utama Desa Avara, Kecamatan Contoh, Kabupaten Contoh, Provinsi Contoh',
                'tipe_data' => 'string',
                'deskripsi' => 'Alamat lengkap kantor desa',
            ]
        ];

        foreach ($data as $item) {
            DB::table('pengaturan_frontend')->insert($item);
        }
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_frontend');
    }
};
