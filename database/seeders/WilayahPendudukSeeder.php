<?php

/**
 * SEEDER — Wilayah, Keluarga & Penduduk (Desa Udeung)
 *
 * Mengisi data autentik Desa Udeung dengan 310 Kartu Keluarga
 * dan sekitar 1200-1500 penduduk. Data mencakup berbagai status:
 * - Tetap (aktif)
 * - Pindah (pindah domisili)
 * - Meninggal
 *
 * Data berdasarkan studi uji kelayakan di:
 * Desa Udeung, Kecamatan Bandar Baru, Kabupaten Pidie Jaya, Provinsi Aceh
 */

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\ReferensiWilayah;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WilayahPendudukSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Referensi Wilayah — Hierarki Administrasi Indonesia
        $wilayah = [
            ['kode_wilayah' => '11', 'nama_wilayah' => 'Prov. Aceh', 'level' => 'provinsi', 'parent_kode' => null],
            ['kode_wilayah' => '11.18', 'nama_wilayah' => 'Kab. Pidie Jaya', 'level' => 'kabupaten', 'parent_kode' => '11'],
            ['kode_wilayah' => '11.18.06', 'nama_wilayah' => 'Kec. Bandar Baru', 'level' => 'kecamatan', 'parent_kode' => '11.18'],
            ['kode_wilayah' => '11.18.06.2001', 'nama_wilayah' => 'Desa Udeung', 'level' => 'desa', 'parent_kode' => '11.18.06'],
        ];

        foreach ($wilayah as $w) {
            ReferensiWilayah::updateOrCreate(['kode_wilayah' => $w['kode_wilayah']], $w);
        }

        // 2. Daftar Dusun di Desa Udeung
        $dusunList = [
            'Dusun Tunong',
            'Dusun Tengah',
            'Dusun Baroh',
        ];

        // 3. Nama-nama Aceh
        $namaLakiAceh = [
            'Tgk. Muhammad', 'Tgk. Ibrahim', 'Tgk. Abdullah', 'Tgk. Ahmad', 'Tgk. Iskandar',
            'Tgk. Fauzi', 'Tgk. Rizal', 'Tgk. Husni', 'Tgk. Zubir', 'Tgk. Muhazar',
            'Tgk. Sulaiman', 'Tgk. Yusuf', 'Tgk. Husein', 'Tgk. Bakhtiar', 'Tgk. Nazar',
            'Tgk. Khairil', 'Tgk. Diman', 'Tgk. Mukhtar', 'Tgk. Syarifuddin', 'Tgk. Tarmizi',
            'Muhammad', 'Ibrahim', 'Abdullah', 'Ahmad', 'Iskandar',
            'Fauzi', 'Rizal', 'Husni', 'Zubir', 'Muhazar',
            'Sulaiman', 'Yusuf', 'Husein', 'Bakhtiar', 'Nazar',
            'Khairil', 'Diman', 'Mukhtar', 'Syarifuddin', 'Tarmizi',
            'Tgk. Khairullah', 'Tgk. M. Hasbi', 'Tgk. Jamiluddin', 'Tgk. Abrar',
            'M. Yunus', 'M. Arifin', 'M. Nafis', 'M. Rizky', 'M. Fauzan',
        ];

        $namaPerempuanAceh = [
            'Cut', 'Putri', 'Rabiah', 'Nurbaiti', 'Siti',
            'Fatimah', 'Aminah', 'Khadijah', 'Zainab', 'Halimah',
            'Nurul', 'Salma', 'Hanifa', 'Raihan', 'Aisyah',
            'Maryam', 'Zahra', 'Nisa', 'Adelia', 'Nisa',
            'Cut Rahma', 'Cut Sari', 'Cut Dewi', 'Cut Ayu', 'Cut Nadia',
            'Rika', 'Wati', 'Yuli', 'Sri', 'Murni',
        ];

        $namaBelakangAceh = [
            'Abdullah', 'Ahmad', 'Ibrahim', 'Sulaiman', 'Yusuf',
            'Husein', 'Zubir', 'Muhazar', 'Rizal', 'Fauzi',
            'Nazar', 'Bakhtiar', 'Iskandar', 'Husni', 'Tarmizi',
            'Syarifuddin', 'Khairil', 'Diman', 'Mukhtar', 'Usman',
            'Saputra', 'Maulana', 'Putra', 'Rahman', 'Hasanah',
            'Al-Farisi', 'Al-Idrus', 'Al-Aydrus', 'Al-Kaf', 'Al-Habsyi',
        ];

        // 4. Pekerjaan
        $pekerjaanList = [
            'Petani/Pekebun', 'Petani/Pekebun', 'Petani/Pekebun', 'Petani/Pekebun',
            'Pedagang', 'Pedagang', 'Pedagang',
            'Wiraswasta', 'Wiraswasta',
            'Buruh Harian Lepas', 'Buruh Harian Lepas',
            'Nelayan/Perikanan',
            'Karyawan Swasta',
            'PNS', 'Guru',
            'TNI/Polri',
            'Mengurus Rumah Tangga', 'Mengurus Rumah Tangga', 'Mengurus Rumah Tangga',
            'Pelajar/Mahasiswa', 'Pelajar/Mahasiswa',
            'Belum/Tidak Bekerja',
            'Tukang Kayu', 'Tukang Batu', 'Sopir',
        ];

        // 5. Pendidikan
        $pendidikanList = [
            'Tidak Sekolah', 'Tidak Sekolah', 'SD', 'SD', 'SMP', 'SMP',
            'SMA', 'SMA', 'SMA', 'SMA', 'SMA',
            'SMK', 'D3', 'D3', 'S1', 'S1',
        ];

        // 6. Alamat
        $alamatSpesifik = [
            'Jl. Utama Desa Udeung', 'Jl. Penghubung Dusun', 'Jl. Samping Meunasah',
            'Jl. Lapangan Desa', 'Jl. Kebun Sawit', 'Jl. Persawahan',
            'Dekat Pasar Udeung', 'Samping Kantor Kepala Desa', 'Belakang Meunasah',
            'Jl. Raya Bandar Baru', 'Jl. Menuju Meureudu', 'Dekat Pos Kamling',
            'Jl. Cot Udeung', 'Jl. Blang Udeung', 'Jl. Krueng Udeung',
            'Jl. Dayah Udeung', 'Jl. Pante Udeung', 'Jl. Buket Udeung',
        ];

        // 7. Tempat lahir
        $tempatLahirList = [
            'Meureudu', 'Meureudu', 'Meureudu', 'Meureudu',
            'Sigli', 'Banda Aceh', 'Lhokseumawe',
            'Langsa', 'Bireuen', 'Bireuen',
            'Pidie', 'Pidie Jaya', 'Pidie Jaya', 'Pidie Jaya',
            'Aceh Utara', 'Aceh Timur',
            'Meulaboh', 'Takengon',
        ];

        // 8. Generate Data Keluarga dan Penduduk
        $jumlahKeluarga = 310; // 310 KK realistis untuk desa

        for ($i = 0; $i < $jumlahKeluarga; $i++) {
            // Nomor KK (16 digit)
            $noKk = '111806' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . $faker->numerify('######');

            // Pilih dusun
            $dusun = $faker->randomElement($dusunList);

            // Alamat
            $namaJalan = $faker->randomElement($alamatSpesifik);
            $nomorRumah = $faker->numberBetween(1, 80);
            $alamatLengkap = $namaJalan . ' No. ' . $nomorRumah . ', ' . $dusun;

            $keluarga = Keluarga::create([
                'no_kk' => $noKk,
                'alamat' => $alamatLengkap,
                'dusun' => $dusun,
                'rt_rw' => sprintf('%03d/001', $faker->numberBetween(1, 6)),
            ]);

            // Status mutasi keluarga
            // 90% Tetap, 7% Pindah, 3% Kepala Keluarga Meninggal
            $statusMutasiKK = 'Tetap';
            $probStatus = $faker->numberBetween(1, 100);
            if ($probStatus <= 3) {
                $statusMutasiKK = 'Meninggal';
            } elseif ($probStatus <= 10) {
                $statusMutasiKK = 'Pindah';
            }

            // ============================================
            // KEPALA KELUARGA
            // ============================================
            $tahunLahirSuami = $faker->numberBetween(1955, 2000);
            $bulanLahir = str_pad($faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
            $hariLahir = str_pad($faker->numberBetween(1, 28), 2, '0', STR_PAD_LEFT);

            $namaDepanSuami = $faker->randomElement($namaLakiAceh);
            $namaBelakangSuami = $faker->randomElement($namaBelakangAceh);
            $namaLengkapSuami = $namaDepanSuami . ' ' . $namaBelakangSuami;

            $nikSuami = '111806' . substr($tahunLahirSuami, -2) . $bulanLahir . $hariLahir . $faker->numerify('####');

            $pekerjaanSuami = $faker->randomElement($pekerjaanList);
            $pendidikanSuami = $faker->randomElement($pendidikanList);

            // Status perkawinan berdasarkan usia
            $usiaSuami = 2026 - $tahunLahirSuami;
            if ($usiaSuami < 20) {
                $statusPerkawinanSuami = 'Belum Kawin';
            } elseif ($statusMutasiKK === 'Meninggal' && $faker->boolean(40)) {
                $statusPerkawinanSuami = 'Cerai Mati';
            } else {
                $statusPerkawinanSuami = 'Kawin';
            }

            Penduduk::create([
                'nik' => $nikSuami,
                'no_kk' => $noKk,
                'nama_lengkap' => $namaLengkapSuami,
                'jenis_kelamin' => 'L',
                'tempat_lahir' => $faker->randomElement($tempatLahirList),
                'tanggal_lahir' => $tahunLahirSuami . '-' . $bulanLahir . '-' . $hariLahir,
                'agama' => 'Islam',
                'pekerjaan' => $pekerjaanSuami,
                'pendidikan' => $pendidikanSuami,
                'status_perkawinan' => $statusPerkawinanSuami,
                'status_keluarga' => 'Kepala Keluarga',
                'status_mutasi' => $statusMutasiKK,
            ]);

            $keluarga->update(['kepala_keluarga_nik' => $nikSuami]);

            // ============================================
            // ISTRI (85% punya istri di KK)
            // ============================================
            $nikIstri = null;
            $tahunLahirIstri = null;

            if ($faker->boolean(85) && $statusMutasiKK !== 'Meninggal') {
                $tahunLahirIstri = $tahunLahirSuami + $faker->numberBetween(-3, 5);
                $bulanLahirIstri = str_pad($faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
                $hariLahirIstri = str_pad($faker->numberBetween(1, 28), 2, '0', STR_PAD_LEFT);

                $namaDepanIstri = $faker->randomElement($namaPerempuanAceh);
                $namaBelakangIstri = $faker->randomElement($namaBelakangAceh);

                if (str_starts_with($namaDepanIstri, 'Cut')) {
                    $namaLengkapIstri = $namaDepanIstri . ' ' . $faker->randomElement($namaPerempuanAceh) . ' ' . $namaBelakangIstri;
                } else {
                    $namaLengkapIstri = $namaDepanIstri . ' ' . $namaBelakangIstri;
                }

                $nikIstri = '111806' . substr($tahunLahirIstri, -2) . $bulanLahirIstri . $hariLahirIstri . $faker->numerify('####');

                // Status mutasi istri mengikuti suami, atau bisa juga sendiri
                $statusMutasiIstri = $statusMutasiKK;
                if ($statusMutasiKK === 'Tetap' && $faker->boolean(5)) {
                    $statusMutasiIstri = 'Pindah'; // Istri pindah duluan (contoh: cerai)
                }

                Penduduk::create([
                    'nik' => $nikIstri,
                    'no_kk' => $noKk,
                    'nama_lengkap' => $namaLengkapIstri,
                    'jenis_kelamin' => 'P',
                    'tempat_lahir' => $faker->randomElement($tempatLahirList),
                    'tanggal_lahir' => $tahunLahirIstri . '-' . $bulanLahirIstri . '-' . $hariLahirIstri,
                    'agama' => 'Islam',
                    'pekerjaan' => 'Mengurus Rumah Tangga',
                    'pendidikan' => $faker->randomElement(['SMA', 'SMA', 'SMP', 'SD', 'S1']),
                    'status_perkawinan' => $statusMutasiKK === 'Meninggal' ? 'Cerai Mati' : 'Kawin',
                    'status_keluarga' => 'Istri',
                    'status_mutasi' => $statusMutasiIstri,
                ]);
            }

            // ============================================
            // ANAK-ANAK (0-5 anak)
            // ============================================
            $jumlahAnak = $faker->numberBetween(0, 5);
            $tahunLahirAnakBase = $tahunLahirSuami + $faker->numberBetween(2, 5);

            for ($j = 0; $j < $jumlahAnak; $j++) {
                $jk = $faker->randomElement(['L', 'P']);

                if ($jk === 'L') {
                    $namaDepanAnak = $faker->randomElement($namaLakiAceh);
                } else {
                    $namaDepanAnak = $faker->randomElement($namaPerempuanAceh);
                }
                $namaBelakangAnak = $namaBelakangSuami;

                if (str_starts_with($namaDepanAnak, 'Cut')) {
                    $namaLengkapAnak = $namaDepanAnak . ' ' . $faker->randomElement($namaPerempuanAceh) . ' ' . $namaBelakangAnak;
                } else {
                    $namaLengkapAnak = $namaDepanAnak . ' ' . $namaBelakangAnak;
                }

                $tahunAnak = $tahunLahirAnakBase + ($j * $faker->numberBetween(2, 4));
                if ($tahunAnak > 2026) break;

                $bulanAnak = str_pad($faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
                $hariAnak = str_pad($faker->numberBetween(1, 28), 2, '0', STR_PAD_LEFT);

                $nikAnak = '111806' . substr($tahunAnak, -2) . $bulanAnak . $hariAnak . $faker->numerify('####');
                $usiaAnak = 2026 - $tahunAnak;

                // Pekerjaan dan pendidikan berdasarkan usia
                if ($usiaAnak < 6) {
                    $pekerjaanAnak = 'Belum/Tidak Bekerja';
                    $pendidikanAnak = 'Belum Sekolah';
                } elseif ($usiaAnak < 12) {
                    $pekerjaanAnak = 'Pelajar/Mahasiswa';
                    $pendidikanAnak = 'SD';
                } elseif ($usiaAnak < 15) {
                    $pekerjaanAnak = 'Pelajar/Mahasiswa';
                    $pendidikanAnak = 'SMP';
                } elseif ($usiaAnak < 18) {
                    $pekerjaanAnak = 'Pelajar/Mahasiswa';
                    $pendidikanAnak = 'SMA';
                } elseif ($usiaAnak < 22) {
                    $pekerjaanAnak = 'Pelajar/Mahasiswa';
                    $pendidikanAnak = $faker->randomElement(['SMA', 'SMK', 'D3', 'S1']);
                } else {
                    $pekerjaanAnak = $faker->randomElement($pekerjaanList);
                    $pendidikanAnak = $faker->randomElement(['SMA', 'SMK', 'D3', 'S1']);
                }

                $statusPerkawinanAnak = ($usiaAnak < 20) ? 'Belum Kawin' : $faker->randomElement(['Belum Kawin', 'Kawin', 'Kawin']);

                // Status mutasi anak
                $statusMutasiAnak = $statusMutasiKK;
                if ($statusMutasiKK === 'Tetap') {
                    // Anak muda lebih mungkin pindah (kuliah/kerja)
                    if ($usiaAnak >= 18 && $faker->boolean(10)) {
                        $statusMutasiAnak = 'Pindah';
                    }
                    // Anak kecil bisa meninggal (sangat kecil kemungkinannya)
                    if ($usiaAnak < 5 && $faker->boolean(1)) {
                        $statusMutasiAnak = 'Meninggal';
                    }
                }

                Penduduk::create([
                    'nik' => $nikAnak,
                    'no_kk' => $noKk,
                    'nama_lengkap' => $namaLengkapAnak,
                    'jenis_kelamin' => $jk,
                    'tempat_lahir' => $faker->randomElement(['Pidie Jaya', 'Pidie Jaya', 'Meureudu', 'Banda Aceh']),
                    'tanggal_lahir' => $tahunAnak . '-' . $bulanAnak . '-' . $hariAnak,
                    'agama' => 'Islam',
                    'pekerjaan' => $pekerjaanAnak,
                    'pendidikan' => $pendidikanAnak,
                    'status_perkawinan' => $statusPerkawinanAnak,
                    'status_keluarga' => 'Anak',
                    'status_mutasi' => $statusMutasiAnak,
                ]);
            }

            // ============================================
            // ORANG TUA / MERTUA (35% chance)
            // ============================================
            if ($faker->boolean(35)) {
                $tahunLahirOrtu = $faker->numberBetween(1935, 1965);
                $bulanOrtu = str_pad($faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
                $hariOrtu = str_pad($faker->numberBetween(1, 28), 2, '0', STR_PAD_LEFT);

                $namaDepanOrtu = $faker->randomElement($faker->boolean(50) ? $namaLakiAceh : $namaPerempuanAceh);
                $namaBelakangOrtu = $faker->randomElement($namaBelakangAceh);

                if (str_starts_with($namaDepanOrtu, 'Cut')) {
                    $namaLengkapOrtu = $namaDepanOrtu . ' ' . $faker->randomElement($namaPerempuanAceh) . ' ' . $namaBelakangOrtu;
                } else {
                    $namaLengkapOrtu = $namaDepanOrtu . ' ' . $namaBelakangOrtu;
                }

                $nikOrtu = '111806' . substr($tahunLahirOrtu, -2) . $bulanOrtu . $hariOrtu . $faker->numerify('####');
                $jenisKelaminOrtu = $faker->boolean(50) ? 'L' : 'P';

                // Status mutasi orang tua
                $statusMutasiOrtu = 'Tetap';
                $usiaOrtu = 2026 - $tahunLahirOrtu;
                if ($usiaOrtu > 70 && $faker->boolean(20)) {
                    $statusMutasiOrtu = 'Meninggal';
                } elseif ($faker->boolean(8)) {
                    $statusMutasiOrtu = 'Pindah';
                }

                // Status perkawinan orang tua
                if ($statusMutasiOrtu === 'Meninggal') {
                    $statusPerkawinanOrtu = $faker->boolean(60) ? 'Cerai Mati' : 'Kawin';
                } else {
                    $statusPerkawinanOrtu = $faker->randomElement(['Kawin', 'Cerai Hidup', 'Cerai Mati']);
                }

                Penduduk::create([
                    'nik' => $nikOrtu,
                    'no_kk' => $noKk,
                    'nama_lengkap' => $namaLengkapOrtu,
                    'jenis_kelamin' => $jenisKelaminOrtu,
                    'tempat_lahir' => $faker->randomElement($tempatLahirList),
                    'tanggal_lahir' => $tahunLahirOrtu . '-' . $bulanOrtu . '-' . $hariOrtu,
                    'agama' => 'Islam',
                    'pekerjaan' => $usiaOrtu > 65 ? 'Belum/Tidak Bekerja' : $faker->randomElement($pekerjaanList),
                    'pendidikan' => $faker->randomElement(['Tidak Sekolah', 'Tidak Sekolah', 'SD', 'SMP']),
                    'status_perkawinan' => $statusPerkawinanOrtu,
                    'status_keluarga' => $jenisKelaminOrtu === 'L' ? 'Ayah' : 'Ibu',
                    'status_mutasi' => $statusMutasiOrtu,
                ]);
            }

            // ============================================
            // SAUDARA KANDUNG (20% chance)
            // ============================================
            if ($faker->boolean(20) && $statusMutasiKK === 'Tetap') {
                $tahunLahirSodara = $faker->numberBetween(1960, 2005);
                $bulanSodara = str_pad($faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
                $hariSodara = str_pad($faker->numberBetween(1, 28), 2, '0', STR_PAD_LEFT);

                $jkSodara = $faker->randomElement(['L', 'P']);
                if ($jkSodara === 'L') {
                    $namaSodara = $faker->randomElement($namaLakiAceh) . ' ' . $namaBelakangSuami;
                } else {
                    $namaSodara = $faker->randomElement($namaPerempuanAceh) . ' ' . $namaBelakangSuami;
                }

                $nikSodara = '111806' . substr($tahunLahirSodara, -2) . $bulanSodara . $hariSodara . $faker->numerify('####');
                $usiaSodara = 2026 - $tahunLahirSodara;

                $statusMutasiSodara = 'Tetap';
                if ($usiaSodara >= 18 && $faker->boolean(12)) {
                    $statusMutasiSodara = 'Pindah';
                }

                Penduduk::create([
                    'nik' => $nikSodara,
                    'no_kk' => $noKk,
                    'nama_lengkap' => $namaSodara,
                    'jenis_kelamin' => $jkSodara,
                    'tempat_lahir' => $faker->randomElement($tempatLahirList),
                    'tanggal_lahir' => $tahunLahirSodara . '-' . $bulanSodara . '-' . $hariSodara,
                    'agama' => 'Islam',
                    'pekerjaan' => $faker->randomElement($pekerjaanList),
                    'pendidikan' => $faker->randomElement($pendidikanList),
                    'status_perkawinan' => $usiaSodara < 20 ? 'Belum Kawin' : $faker->randomElement(['Kawin', 'Belum Kawin']),
                    'status_keluarga' => 'Saudara',
                    'status_mutasi' => $statusMutasiSodara,
                ]);
            }
        }
    }
}
