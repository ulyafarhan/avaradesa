<?php

/**
 * SEEDER — Riwayat Transaksi Dummy (Desa Udeung)
 *
 * Mengisi data riwayat pengajuan surat dan riwayat mutasi penduduk secara
 * realistis untuk keperluan simulasi dasbor dan laporan. Data transaksi
 * disesuaikan dengan kebutuhan administrasi di Desa Udeung.
 */

namespace Database\Seeders;

use App\Models\KategoriSurat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\MutasiPenduduk;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransaksiDummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Pastikan ada penduduk dan kategori surat
        $penduduk = Penduduk::inRandomOrder()->take(80)->get();
        $kategoriList = KategoriSurat::all();

        if ($penduduk->isEmpty() || $kategoriList->isEmpty()) {
            return;
        }

        // 1. Pembangkitan Data Pengajuan Surat (50 transaksi realistis)
        $statusSurat = ['Pending', 'Diproses', 'Selesai', 'Selesai', 'Selesai', 'Selesai', 'Ditolak'];

        // Daftar keperluan yang realistis untuk Desa Udeung
        $daftarKeperluan = [
            'Melamar Pekerjaan di PT. Pertamina',
            'Persyaratan Beasiswa PPA',
            'Pengajuan Kredit di Bank Aceh Syariah',
            'Persyaratan Nikah di Kantor Urusan Agama',
            'Pendaftaran CPNS Kabupaten Pidie Jaya',
            'Persyaratan Masuk Polisi',
            'Pengajuan BPJS Kesehatan',
            'Persyaratan Sekolah Anak',
            'Urusan Keluarga di Disdukcapil',
            'Pengajuan Kartu Prakerja',
            'Persyaratan Leasing Kendaraan',
            'Urusan Pajak Kendaraan Bermotor',
            'Persyaratan Kerja di Luar Negeri',
            'Pengajuan BLT Desa',
            'Persyaratan Seleksi Bintara',
            'Urusan Warisan Tanah',
            'Pengajuan Modal Usaha dari Dinas Koperasi',
            'Persyaratan Wisuda Anak',
            'Urusan Sertifikat Tanah',
            'Pengajuan Bantuan Pertanian',
        ];

        // Generate nomor registrasi yang realistis
        $kategoriMap = [
            'SKU' => 'Usaha',
            'SKD' => 'Domisili',
            'SKTM' => 'Tidak Mampu',
            'SKL' => 'Kelahiran',
            'SKK' => 'Kematian',
            'SKCK' => 'SKCK',
            'SKBM' => 'Belum Menikah',
            'SKP' => 'Pindah',
            'SPN' => 'Nikah',
            'SKPeng' => 'Penghasilan',
        ];

        for ($i = 0; $i < 50; $i++) {
            $pemohon = $penduduk->random();
            $kategori = $kategoriList->random();

            // Build data isian JSON berdasarkan template schema
            $isian = [];
            foreach ($kategori->schema_isian as $schema) {
                $field = $schema['field'];

                if ($field === 'keperluan' || $field === 'tujuan_skck') {
                    $isian[$field] = $faker->randomElement($daftarKeperluan);
                } elseif ($field === 'nama_usaha') {
                    $tipeUsaha = $faker->randomElement(['Toko', 'Warung', 'Bengkel', 'Laundry', 'Percetakan', 'Fotocopy']);
                    $isian[$field] = $tipeUsaha . ' ' . $faker->lastName;
                } elseif ($field === 'jenis_usaha') {
                    $isian[$field] = $faker->randomElement([
                        'Kelontong', 'Sembako', 'Pakaian', 'Jasa Servis',
                        'Pertanian', 'Perikanan', 'Peternakan', 'Jasa',
                    ]);
                } elseif ($field === 'alamat_usaha' || $field === 'alamat_sekarang') {
                    $isian[$field] = $faker->address . ', Desa Udeung, Kec. Bandar Baru';
                } elseif ($field === 'tahun_berdiri') {
                    $isian[$field] = $faker->numberBetween(2015, 2024);
                } elseif ($field === 'lama_menetap') {
                    $isian[$field] = $faker->numberBetween(1, 15) . ' Tahun';
                } elseif ($field === 'status_tempat_tinggal') {
                    $isian[$field] = $faker->randomElement(['Milik Sendiri', 'Sewa', 'Kos']);
                } elseif ($field === 'pekerjaan_ortu' || $field === 'pekerjaan') {
                    $isian[$field] = $faker->randomElement([
                        'Petani', 'Pedagang', 'Wiraswasta', 'Buruh',
                        'Nelayan', 'PNS', 'Karyawan Swasta',
                    ]);
                } elseif ($field === 'penghasilan_perbulan') {
                    $isian[$field] = $faker->numberBetween(800000, 3500000);
                } elseif ($field === 'jumlah_tanggungan' || $field === 'anak_ke') {
                    $isian[$field] = $faker->numberBetween(1, 5);
                } elseif (str_contains($field, 'nama') && !str_contains($field, 'ayah') && !str_contains($field, 'ibu')) {
                    // Nama Aceh
                    $namaDepan = $faker->randomElement(['Muhammad', 'Ahmad', 'Abdullah', 'Ibrahim', 'Siti', 'Fatimah', 'Aminah', 'Cut']);
                    $namaBelakang = $faker->randomElement(['Abdullah', 'Ahmad', 'Sulaiman', 'Yusuf', 'Husein', 'Rahman']);
                    if ($namaDepan === 'Cut') {
                        $isian[$field] = $namaDepan . ' ' . $faker->randomElement(['Nurhaliza', 'Rabiah', 'Nurbaiti']) . ' ' . $namaBelakang;
                    } else {
                        $isian[$field] = $namaDepan . ' ' . $namaBelakang;
                    }
                } elseif ($field === 'jenis_kelamin') {
                    $isian[$field] = $faker->randomElement(['Laki-laki', 'Perempuan']);
                } elseif ($field === 'tanggal_lahir' || $field === 'waktu_kematian') {
                    $isian[$field] = $faker->dateTimeThisYear()->format('d-m-Y');
                } elseif ($field === 'tempat_lahir' || $field === 'tempat_kematian' || $field === 'tempat_nikah') {
                    $isian[$field] = $faker->randomElement([
                        'RSUD Meureudu', 'Puskesmas Bandar Baru', 'Rumah',
                        'Meureudu', 'Banda Aceh', 'Sigli',
                    ]);
                } elseif ($field === 'penyebab_kematian') {
                    $isian[$field] = $faker->randomElement([
                        'Sakit Jantung', 'Diabetes', 'Kecelakaan', 'Tua',
                    ]);
                } elseif ($field === 'hubungan_pelapor') {
                    $isian[$field] = $faker->randomElement([
                        'Anak Kandung', 'Suami', 'Istri', 'Saudara Kandung',
                        'Menantu', 'Cucu',
                    ]);
                } elseif ($field === 'status_perkawinan') {
                    $isian[$field] = $faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
                } elseif ($field === 'pendidikan_terakhir') {
                    $isian[$field] = $faker->randomElement(['SD', 'SMP', 'SMA', 'SMK', 'D3', 'S1']);
                } elseif ($field === 'alamat_tujuan') {
                    $isian[$field] = $faker->address;
                } elseif ($field === 'desa_tujuan' || $field === 'kecamatan_tujuan' || $field === 'kabupaten_tujuan') {
                    $isian[$field] = $faker->city;
                } elseif ($field === 'alasan_pindah') {
                    $isian[$field] = $faker->randomElement([
                        'Ikut Suami/Istri', 'Pekerjaan', 'Pendidikan',
                        'Mendirikan Rumah Tangga Baru', 'Lainnya',
                    ]);
                } elseif ($field === 'jumlah_pindah') {
                    $isian[$field] = $faker->numberBetween(1, 6);
                } elseif ($field === 'nik_calon_suami' || $field === 'nik_calon_istri') {
                    $isian[$field] = '111806' . $faker->numerify('################');
                } elseif ($field === 'tanggal_rencana_nikah') {
                    $isian[$field] = $faker->dateTimeBetween('now', '+6 months')->format('d-m-Y');
                } else {
                    $isian[$field] = $faker->word;
                }
            }

            // Tanggal pengajuan realistis (3 bulan terakhir)
            $tglPengajuan = $faker->dateTimeBetween('-3 months', 'now');
            $status = $faker->randomElement($statusSurat);
            $catatan = $status === 'Ditolak' ? $faker->randomElement([
                'Dokumen persyaratan tidak lengkap.',
                'Data yang diberikan tidak sesuai.',
                'Surat keterangan dari dusun belum dilampirkan.',
                'Foto copy KTP tidak jelas.',
            ]) : null;

            // Nomor registrasi realistis
            $namaSingkat = $kategoriMap[$kategori->kode_surat] ?? 'Surat';
            $noRegistrasi = $kategori->kode_surat . '/' . date('Y', $tglPengajuan->getTimestamp()) . '/' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);

            // Nomor surat untuk status Selesai
            $nomorSurat = null;
            if ($status === 'Selesai') {
                $romawiBulan = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
                $bulan = $romawiBulan[(int) date('n', $tglPengajuan->getTimestamp())];
                $nomorSurat = sprintf(
                    '%s/%03d/%s/%s',
                    $kategori->kode_surat,
                    $faker->numberBetween(1, 100),
                    $bulan,
                    date('Y', $tglPengajuan->getTimestamp())
                );
            }

            $tanggalAju = $tglPengajuan;

            PengajuanSurat::create([
                'kategori_surat_id' => $kategori->id,
                'nik_pemohon' => $pemohon->nik,
                'nomor_registrasi' => $noRegistrasi,
                'data_isian' => $isian,
                'file_syarat' => ['dummy1.jpg', 'dummy2.pdf'],
                'status' => $status,
                'created_at' => $tanggalAju,
                'updated_at' => $tanggalAju,
                'qr_hash' => $status === 'Selesai' ? hash('sha256', Str::random(10)) : null,
                'nomor_surat' => $nomorSurat,
            ]);
        }

        // 2. Pembangkitan Data Mutasi Penduduk (25 transaksi realistis)
        $jenisMutasi = [
            'Kelahiran', 'Kelahiran', 'Kelahiran',
            'Kematian', 'Kematian',
            'Kepindahan', 'Kepindahan',
            'Kedatangan', 'Kedatangan',
        ];
        $statusMutasiVer = ['Pending', 'Disetujui', 'Disetujui', 'Disetujui', 'Ditolak'];

        for ($i = 0; $i < 25; $i++) {
            $warga = $penduduk->random();
            $jenis = $faker->randomElement($jenisMutasi);
            $tglMutasi = $faker->dateTimeBetween('-6 months', 'now');
            $statusVer = $faker->randomElement($statusMutasiVer);

            $keterangan = '';
            $namaAnak = '';
            switch ($jenis) {
                case 'Kelahiran':
                    $jkAnak = $faker->randomElement(['Laki-laki', 'Perempuan']);
                    $namaAnak = $faker->randomElement(['Muhammad', 'Ahmad', 'Siti', 'Fatimah', 'Abdullah']) . ' ' . $faker->lastName;
                    $keterangan = 'Kelahiran anak ' . $jkAnak . ' bernama ' . $namaAnak . ' di RSUD Meureudu';
                    break;
                case 'Kematian':
                    $keterangan = 'Meninggal karena ' . $faker->randomElement([
                        'sakit jantung', 'diabetes', 'kecelakaan lalu lintas',
                        'stroke', 'flek paru-paru', 'usia lanjut',
                    ]) . ' di ' . $faker->randomElement(['RSUD Meureudu', 'Rumah Sakit Banda Aceh', 'Rumah']);
                    break;
                case 'Kepindahan':
                    $keterangan = 'Pindah domisili ke ' . $faker->randomElement([
                        'Desa Meunara, Kec. Bandar Baru',
                        'Desa Dayah Baro, Kec. Meureudu',
                        'Desa Pante Raja, Kec. Ulim',
                        'Kota Banda Aceh',
                        'Kota Lhokseumawe',
                    ]) . ' karena ' . $faker->randomElement(['pekerjaan', 'menikah', 'keluarga']);
                    break;
                case 'Kedatangan':
                    $keterangan = 'Pindah datang dari ' . $faker->randomElement([
                        'Desa Seunong, Kec. Bandar Baru',
                        'Desa Cot Glumpang, Kec. Meureudu',
                        'Desa Pulo Kiton, Kec. Ulim',
                        'Kabupaten Pidie',
                        'Kabupaten Bireuen',
                    ]) . ' untuk ' . $faker->randomElement(['menetap', 'bekerja', 'mendirikan rumah tangga']);
                    break;
            }

            MutasiPenduduk::create([
                'nik' => $warga->nik,
                'jenis_mutasi' => $jenis,
                'tanggal_mutasi' => $tglMutasi,
                'keterangan' => $keterangan,
                'dokumen_bukti' => 'storage/mutasi/bukti_' . ($i + 1) . '.pdf',
                'status_verifikasi' => $statusVer,
                'diverifikasi_oleh' => null,
            ]);

            // Update status penduduk jika mutasi disetujui
            if ($statusVer === 'Disetujui') {
                if ($jenis === 'Kematian') {
                    $warga->update(['status_mutasi' => 'Meninggal']);
                } elseif ($jenis === 'Kepindahan') {
                    $warga->update(['status_mutasi' => 'Pindah']);
                }
            }
        }
    }
}
