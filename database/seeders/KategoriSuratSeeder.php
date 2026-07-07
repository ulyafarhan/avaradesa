<?php

/**
 * SEEDER — Kategori Surat Desa (Desa Udeung)
 *
 * Mengisi data master jenis surat desa yang paling sering
 * diajukan oleh warga Desa Udeung. Sesuai dengan kebutuhan
 * administrasi di wilayah Kabupaten Pidie Jaya, Provinsi Aceh.
 */

namespace Database\Seeders;

use App\Models\KategoriSurat;
use Illuminate\Database\Seeder;

class KategoriSuratSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriSurat = [
            // 1. Surat Keterangan Usaha (SKU)
            [
                'kode_surat' => 'SKU',
                'nama_surat' => 'Surat Keterangan Usaha',
                'template_view' => 'usaha',
                'schema_isian' => [
                    [
                        'field' => 'nama_usaha',
                        'label' => 'Nama Usaha',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'jenis_usaha',
                        'label' => 'Jenis Usaha / Bidang',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'alamat_usaha',
                        'label' => 'Alamat Tempat Usaha',
                        'type' => 'textarea',
                        'required' => true,
                    ],
                    [
                        'field' => 'tahun_berdiri',
                        'label' => 'Tahun Berdiri',
                        'type' => 'number',
                        'required' => true,
                    ],
                    [
                        'field' => 'keperluan',
                        'label' => 'Tujuan / Keperluan Surat',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'KTP Asli dan Fotokopi',
                    'Foto Tempat Usaha',
                    'Kartu Keluarga',
                ],
                'is_active' => true,
            ],

            // 2. Surat Keterangan Domisili (SKD)
            [
                'kode_surat' => 'SKD',
                'nama_surat' => 'Surat Keterangan Domisili',
                'template_view' => 'domisili',
                'schema_isian' => [
                    [
                        'field' => 'alamat_sekarang',
                        'label' => 'Alamat Domisili Sekarang',
                        'type' => 'textarea',
                        'required' => true,
                    ],
                    [
                        'field' => 'lama_menetap',
                        'label' => 'Lama Menetap (Bulan/Tahun)',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'status_tempat_tinggal',
                        'label' => 'Status Tempat Tinggal',
                        'type' => 'select',
                        'options' => ['Milik Sendiri', 'Sewa', 'Kos', 'Lainnya'],
                        'required' => true,
                    ],
                    [
                        'field' => 'keperluan',
                        'label' => 'Keperluan Pembuatan',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'KTP Asli dan Fotokopi',
                    'Kartu Keluarga',
                ],
                'is_active' => true,
            ],

            // 3. Surat Keterangan Tidak Mampu (SKTM)
            [
                'kode_surat' => 'SKTM',
                'nama_surat' => 'Surat Keterangan Tidak Mampu',
                'template_view' => 'sktm',
                'schema_isian' => [
                    [
                        'field' => 'pekerjaan_ortu',
                        'label' => 'Pekerjaan Kepala Keluarga / Orang Tua',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'penghasilan_perbulan',
                        'label' => 'Rata-rata Penghasilan Per Bulan (Rp)',
                        'type' => 'number',
                        'required' => true,
                    ],
                    [
                        'field' => 'jumlah_tanggungan',
                        'label' => 'Jumlah Tanggungan Keluarga',
                        'type' => 'number',
                        'required' => true,
                    ],
                    [
                        'field' => 'keperluan',
                        'label' => 'Tujuan / Keperluan Surat',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'KTP Asli dan Fotokopi',
                    'Kartu Keluarga',
                    'Foto Kondisi Rumah Depan & Dalam',
                    'Surat Pernyataan Tidak Mampu dari Tetangga',
                ],
                'is_active' => true,
            ],

            // 4. Surat Keterangan Kelahiran (SKL)
            [
                'kode_surat' => 'SKL',
                'nama_surat' => 'Surat Keterangan Kelahiran',
                'template_view' => 'kelahiran',
                'schema_isian' => [
                    [
                        'field' => 'nama_anak',
                        'label' => 'Nama Anak',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'jenis_kelamin',
                        'label' => 'Jenis Kelamin Anak',
                        'type' => 'select',
                        'options' => ['Laki-laki', 'Perempuan'],
                        'required' => true,
                    ],
                    [
                        'field' => 'tempat_lahir',
                        'label' => 'Tempat Kelahiran',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'tanggal_lahir',
                        'label' => 'Tanggal & Waktu Lahir',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'nama_ayah',
                        'label' => 'Nama Ayah Kandung',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'nama_ibu',
                        'label' => 'Nama Ibu Kandung',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'anak_ke',
                        'label' => 'Anak Ke-',
                        'type' => 'number',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'Surat Keterangan Bidan / Rumah Sakit',
                    'KTP Ayah & Ibu',
                    'Kartu Keluarga',
                    'Buku Nikah / Akta Perkawinan',
                ],
                'is_active' => true,
            ],

            // 5. Surat Keterangan Kematian (SKK)
            [
                'kode_surat' => 'SKK',
                'nama_surat' => 'Surat Keterangan Kematian',
                'template_view' => 'kematian',
                'schema_isian' => [
                    [
                        'field' => 'waktu_kematian',
                        'label' => 'Hari, Tanggal & Jam Kematian',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'tempat_kematian',
                        'label' => 'Tempat Meninggal',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'penyebab_kematian',
                        'label' => 'Penyebab Kematian',
                        'type' => 'select',
                        'options' => ['Sakit Biasa/Tua', 'Wabah Penyakit', 'Kecelakaan', 'Lainnya'],
                        'required' => true,
                    ],
                    [
                        'field' => 'nama_pelapor',
                        'label' => 'Nama Pelapor',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'hubungan_pelapor',
                        'label' => 'Hubungan Pelapor dengan Jenazah',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'Surat Keterangan Kematian dari RS / Puskesmas',
                    'KTP Jenazah',
                    'Kartu Keluarga',
                    'KTP Pelapor',
                ],
                'is_active' => true,
            ],

            // 6. Surat Pengantar SKCK
            [
                'kode_surat' => 'SKCK',
                'nama_surat' => 'Surat Pengantar SKCK',
                'template_view' => 'pengantar_skck',
                'schema_isian' => [
                    [
                        'field' => 'status_perkawinan',
                        'label' => 'Status Perkawinan',
                        'type' => 'select',
                        'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'],
                        'required' => true,
                    ],
                    [
                        'field' => 'pendidikan_terakhir',
                        'label' => 'Pendidikan Terakhir',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'tujuan_skck',
                        'label' => 'Tujuan Pembuatan SKCK',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'Fotokopi KTP',
                    'Fotokopi Kartu Keluarga',
                    'Fotokopi Akta Kelahiran / Ijazah Terakhir',
                    'Pas Foto 4x6 Background Merah',
                ],
                'is_active' => true,
            ],

            // 7. Surat Keterangan Belum Menikah
            [
                'kode_surat' => 'SKBM',
                'nama_surat' => 'Surat Keterangan Belum Menikah',
                'template_view' => 'belum_menikah',
                'schema_isian' => [
                    [
                        'field' => 'pekerjaan',
                        'label' => 'Pekerjaan',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'keperluan',
                        'label' => 'Keperluan Surat',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'KTP Asli dan Fotokopi',
                    'Kartu Keluarga',
                    'Surat Pernyataan dari Orang Tua',
                ],
                'is_active' => true,
            ],

            // 8. Surat Keterangan Pindah
            [
                'kode_surat' => 'SKP',
                'nama_surat' => 'Surat Keterangan Pindah',
                'template_view' => 'pindah',
                'schema_isian' => [
                    [
                        'field' => 'alamat_tujuan',
                        'label' => 'Alamat Lengkap Tujuan Pindah',
                        'type' => 'textarea',
                        'required' => true,
                    ],
                    [
                        'field' => 'desa_tujuan',
                        'label' => 'Nama Desa / Desa Tujuan',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'kecamatan_tujuan',
                        'label' => 'Nama Kecamatan Tujuan',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'kabupaten_tujuan',
                        'label' => 'Nama Kabupaten Tujuan',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'alasan_pindah',
                        'label' => 'Alasan Pindah',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'jumlah_pindah',
                        'label' => 'Jumlah Anggota Keluarga yang Pindah',
                        'type' => 'number',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'KTP Asli dan Fotokopi',
                    'Kartu Keluarga',
                    'Surat Pengantar dari Desa Tujuan',
                ],
                'is_active' => true,
            ],

            // 9. Surat Pengantar Nikah
            [
                'kode_surat' => 'SPN',
                'nama_surat' => 'Surat Pengantar Nikah',
                'template_view' => 'pengantar_nikah',
                'schema_isian' => [
                    [
                        'field' => 'nama_calon_suami',
                        'label' => 'Nama Lengkap Calon Suami',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'nik_calon_suami',
                        'label' => 'NIK Calon Suami',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'nama_calon_istri',
                        'label' => 'Nama Lengkap Calon Istri',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'nik_calon_istri',
                        'label' => 'NIK Calon Istri',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'tanggal_rencana_nikah',
                        'label' => 'Tanggal Rencana Nikah',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'tempat_nikah',
                        'label' => 'Tempat Rencana Nikah',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'KTP Calon Suami & Istri',
                    'Kartu Keluarga Kedua Belah Pihak',
                    'Akta Kelahiran',
                    'Surat Keterangan Belum Menikah',
                    'Pas Foto 2x6 Background Biru',
                    'Surat Izin Orang Tua (jika belum 21 tahun)',
                ],
                'is_active' => true,
            ],

            // 10. Surat Keterangan Penghasilan
            [
                'kode_surat' => 'SKPeng',
                'nama_surat' => 'Surat Keterangan Penghasilan',
                'template_view' => 'penghasilan',
                'schema_isian' => [
                    [
                        'field' => 'pekerjaan',
                        'label' => 'Pekerjaan / Usaha',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'field' => 'penghasilan_perbulan',
                        'label' => 'Rata-rata Penghasilan Per Bulan (Rp)',
                        'type' => 'number',
                        'required' => true,
                    ],
                    [
                        'field' => 'keperluan',
                        'label' => 'Keperluan Surat',
                        'type' => 'text',
                        'required' => true,
                    ],
                ],
                'syarat_dokumen' => [
                    'KTP Asli dan Fotokopi',
                    'Kartu Keluarga',
                    'Surat Keterangan Usaha (jika pedagang)',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($kategoriSurat as $kategori) {
            KategoriSurat::create($kategori);
        }
    }
}
