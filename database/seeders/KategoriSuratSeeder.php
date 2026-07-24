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
                'body_content' => 'Bahwa yang bersangkutan benar memiliki dan menjalankan usaha di Desa {nama_desa}, Kecamatan {kecamatan}, Kabupaten {kabupaten}. Usaha tersebut telah berjalan dengan baik dan menjadi salah satu sumber pendapatan utama keluarga. Surat keterangan ini dibuat untuk keperluan pengajuan dokumen administrasi dan perizinan usaha di instansi terkait.',
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
                    'Surat Pengantar dari Kepala Desa (jika lintas desa/kelurahan)',
                ],
                'body_content' => 'Bahwa yang bersangkutan benar berdomisili dan bertempat tinggal tetap di Desa {nama_desa}, Kecamatan {kecamatan}, Kabupaten {kabupaten}. Selama tinggal di desa ini, yang bersangkutan telah dikenal oleh masyarakat sekitar dan berkelakuan baik. Surat keterangan ini dibuat untuk keperluan administrasi kependudukan.',
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
                'body_content' => 'Bahwa yang bersangkutan benar warga kurang mampu dari segi ekonomi. Penghasilan kepala keluarga sehari-hari tidak mencukupi untuk memenuhi kebutuhan pokok rumah tangga. Dengan kondisi ekonomi yang demikian, yang bersangkutan tergolong keluarga TIDAK MAMPU dan layak mendapatkan bantuan. Surat ini dibuat untuk keperluan pengajuan bantuan sosial dan/atau keringanan biaya pendidikan.',
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
                'body_content' => 'Bahwa di Desa {nama_desa}, Kecamatan {kecamatan}, Kabupaten {kabupaten}, telah lahir seorang anak ke {anak_ke} dari pasangan suami istri tersebut di atas. Kelahiran ini disaksikan oleh bidan desa dan telah dilaporkan oleh orang tua kepada perangkat desa setempat. Surat keterangan ini dibuat untuk pengurusan akta kelahiran dan pencatatan administrasi kependudukan di Dinas Kependudukan dan Pencatatan Sipil.',
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
                'body_content' => 'Bahwa benar telah meninggal dunia di Desa {nama_desa}, Kecamatan {kecamatan}, Kabupaten {kabupaten} pada hari dan tanggal sebagaimana disebutkan di atas. Jenazah telah dimakamkan di pemakaman umum desa setempat. Surat keterangan ini dibuat untuk pengurusan administrasi kependudukan, penghentian bantuan sosial, dan/atau keperluan ahli waris.',
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
                'body_content' => 'Bahwa yang bersangkutan berkelakuan baik, sopan santun dalam pergaulan sehari-hari, dan tidak pernah terlibat dalam tindak pidana maupun pelanggaran hukum lainnya. Surat pengantar ini dibuat sebagai syarat pengurusan Surat Keterangan Catatan Kepolisian (SKCK) di Kepolisian Resor setempat untuk keperluan sebagaimana disebutkan di atas.',
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
                    'Pas Foto 3x4 (2 lembar)',
                ],
                'body_content' => 'Bahwa yang bersangkutan benar berstatus LAJANG dan belum pernah menikah menurut hukum agama Islam maupun hukum negara yang berlaku. Sepanjang pengetahuan perangkat desa dan masyarakat setempat, yang bersangkutan tidak terikat hubungan pernikahan dengan siapa pun. Surat keterangan ini dibuat untuk keperluan persyaratan administrasi dan/atau rencana pernikahan.',
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
                    'Surat Keterangan dari Desa Tujuan (jika ada)',
                    'Akte Kelahiran Anak (jika membawa anak)',
                ],
                'body_content' => 'Bahwa yang bersangkutan benar akan pindah tempat tinggal dari Desa {nama_desa}, Kecamatan {kecamatan}, Kabupaten {kabupaten} ke tempat tujuan sebagaimana disebutkan di atas. Selama berdomisili di desa ini, yang bersangkutan berkelakuan baik dan tidak memiliki tunggakan kewajiban kepada desa. Surat keterangan ini dibuat untuk pengurusan administrasi kependudukan di desa tujuan.',
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
                    'Surat Izin Orang Tua/Wali (jika belum 21 tahun)',
                    'Surat Rekomendasi dari Kepala Desa (untuk nikah di luar KUA)',
                ],
                'body_content' => 'Bahwa kedua calon mempelai tersebut di atas benar warga Desa {nama_desa}, Kecamatan {kecamatan}, Kabupaten {kabupaten} dan telah memenuhi syarat untuk melaksanakan pernikahan menurut agama Islam dan peraturan perundang-undangan yang berlaku. Tidak ada halangan syar\'i maupun hukum untuk melangsungkan pernikahan. Surat pengantar ini dibuat untuk keperluan pencatatan pernikahan di Kantor Urusan Agama (KUA) Kecamatan {kecamatan}.',
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
                    'Surat Keterangan Usaha (jika pedagang/pengusaha)',
                    'Slip Gaji (jika karyawan)',
                ],
                'body_content' => 'Bahwa yang bersangkutan benar memiliki penghasilan tetap sebagai warga Desa {nama_desa}, Kecamatan {kecamatan}, Kabupaten {kabupaten}. Penghasilan tersebut diperoleh dari pekerjaan/usaha yang dijalani secara sah dan halal. Surat keterangan ini dibuat untuk keperluan administrasi dan pengajuan dokumen perbankan maupun instansi terkait.',
                'is_active' => true,
            ],
        ];

        foreach ($kategoriSurat as $kategori) {
            KategoriSurat::updateOrCreate(
                ['kode_surat' => $kategori['kode_surat']],
                $kategori
            );
        }
    }
}
