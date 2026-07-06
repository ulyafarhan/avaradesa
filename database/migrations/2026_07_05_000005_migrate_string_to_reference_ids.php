<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate agama
        $this->migrateStringToId('penduduk', 'agama', 'ref_agama', 'agama_id');
        // Migrate pendidikan
        $this->migrateStringToId('penduduk', 'pendidikan', 'ref_pendidikan', 'pendidikan_id');
        // Migrate pekerjaan
        $this->migrateStringToId('penduduk', 'pekerjaan', 'ref_pekerjaan', 'pekerjaan_id');
        // Migrate status_perkawinan
        $this->migrateStringToId('penduduk', 'status_perkawinan', 'ref_status_perkawinan', 'status_perkawinan_id');
        // Migrate status_keluarga
        $this->migrateStringToId('penduduk', 'status_keluarga', 'ref_status_keluarga', 'status_keluarga_id');
        // Migrate kategori informasi
        $this->migrateStringToId('informasi_publik', 'kategori', 'kategori_informasi', 'kategori_id');
        // Migrate jenis_fasilitas
        $this->migrateStringToId('inventaris_fasilitas', 'jenis_fasilitas', 'ref_jenis_fasilitas', 'jenis_fasilitas_id');
    }

    protected function migrateStringToId(string $table, string $stringColumn, string $refTable, string $idColumn): void
    {
        $values = DB::table($table)->distinct()->pluck($stringColumn)->filter()->values();

        foreach ($values as $value) {
            $trimmed = trim($value);
            $refRow = DB::table($refTable)->where('nama', $trimmed)->first();

            if (!$refRow) {
                // Insert missing value into lookup table
                $refRow = (object)['id' => DB::table($refTable)->insertGetId(['nama' => $trimmed, 'created_at' => now(), 'updated_at' => now()])];
            }

            DB::table($table)
                ->where($stringColumn, $trimmed)
                ->update([$idColumn => $refRow->id]);
        }
    }

    public function down(): void
    {
        DB::table('penduduk')->update([
            'agama_id' => null,
            'pendidikan_id' => null,
            'pekerjaan_id' => null,
            'status_perkawinan_id' => null,
            'status_keluarga_id' => null,
        ]);
        DB::table('informasi_publik')->update(['kategori_id' => null]);
        DB::table('inventaris_fasilitas')->update(['jenis_fasilitas_id' => null]);
    }
};
