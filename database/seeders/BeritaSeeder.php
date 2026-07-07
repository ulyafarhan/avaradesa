<?php

/**
 * SEEDER — Berita & Informasi Publik (Desa Udeung)
 *
 * Mengisi data berita, pengumuman, dan informasi publik yang realistis
 * untuk Desa Udeung. Konten disusun sesuai dengan kondisi dan
 * kegiatan nyata di desa-desa Aceh, khususnya di Kabupaten Pidie Jaya.
 */

namespace Database\Seeders;

use App\Models\InformasiPublik;
use App\Models\Administrator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BeritaSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil admin pertama sebagai author
        $admin = Administrator::first();

        $berita = [
            // ========================================
            // PENGUMUMAN RESMI
            // ========================================
            [
                'kategori' => 'Pengumuman',
                'judul' => 'Penyaluran Bantuan Langsung Tunai (BLT) Dana Desa Tahap II Tahun 2025',
                'slug' => 'penyaluran-blt-dana-desa-tahap-ii-2025',
                'konten' => '<p>Pemerintah Desa Udeung mengumumkan bahwa penyaluran Bantuan Langsung Tunai (BLT) Dana Desa untuk Tahap II Tahun 2025 akan diselenggarakan pada:</p>
                <ul>
                    <li><strong>Hari/Tanggal:</strong> Kamis, 17 Juli 2025</li>
                    <li><strong>Waktu:</strong> 09.00 WIB - selesai</li>
                    <li><strong>Tempat:</strong> Aula Kantor Kepala Desa Desa Udeung</li>
                </ul>
                <p><strong>Yang perlu dibawa:</strong></p>
                <ul>
                    <li>Kartu Keluarga (KK) asli</li>
                    <li>KTP asli</li>
                    <li>Surat keterangan tidak mampu dari Kepala Desa</li>
                    <li>Buku rekening bank</li>
                </ul>
                <p>Bagi warga penerima manfaat yang tidak hadir pada waktu yang ditentukan, penyaluran dapat diwakili dengan membawa surat kuasa bermaterai.</p>
                <p>Demikian pengumuman ini dibuat untuk diketahui oleh seluruh warga Desa Udeung. Atas perhatiannya diucapkan terima kasih.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(2),
            ],

            [
                'kategori' => 'Pengumuman',
                'judul' => ' Jadwal Pelayanan Administrasi Desa Udeung Bulan Juli 2025',
                'slug' => 'jadwal-pelayanan-administrasi-juli-2025',
                'konten' => '<p>Dengan ini disampaikan jadwal pelayanan administrasi Desa Udeung untuk bulan Juli 2025:</p>
                <table>
                    <tr><td><strong>Hari</strong></td><td><strong>Jam Operasional</strong></td></tr>
                    <tr><td>Senin - Kamis</td><td>08.00 - 12.00 WIB</td></tr>
                    <tr><td>Jumat</td><td>08.00 - 11.30 WIB</td></tr>
                    <tr><td>Sabtu - Minggu</td><td>Tutup</td></tr>
                </table>
                <p><strong>Layanan yang tersedia:</strong></p>
                <ul>
                    <li>Pengajuan Surat Keterangan</li>
                    <li>Pengurusan Kartu Keluarga</li>
                    <li>Pengurusan Akta Kelahiran</li>
                    <li>Pengurusan Surat Pindah</li>
                    <li>Pelayanan Data Kependudukan</li>
                </ul>
                <p>Catatan: Pelayanan pada tanggal merah dan hari raya nasional ditutup.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(5),
            ],

            [
                'kategori' => 'Pengumuman',
                'judul' => 'Rekrutmen Calon Tenaga Kerja Sukarela (TKS) Desa Udeung',
                'slug' => 'rekrutmen-tks-desa-udeung-2025',
                'konten' => '<p>Pemerintah Desa Udeung membuka kesempatan bagi warga yang ingin menjadi Tenaga Kerja Sukarela (TKS) di Kantor Kepala Desa Desa Udeung.</p>
                <p><strong>Persyaratan:</strong></p>
                <ul>
                    <li>Warga Desa Udeung</li>
                    <li>Usia minimal 18 tahun</li>
                    <li>Pendidikan minimal SMA/SMK sederajat</li>
                    <li>Sehat jasmani dan rohani</li>
                    <li>Tidak sedang menempuh pendidikan</li>
                    <li>Mampu mengoperasikan komputer</li>
                </ul>
                <p><strong>Dokumen yang harus dilampirkan:</strong></p>
                <ul>
                    <li>Surat lamaran</li>
                    <li>Fotokopi KTP dan KK</li>
                    <li>Fotokopi ijazah terakhir</li>
                    <li>Pas foto 3x4 (2 lembar)</li>
                </ul>
                <p>Batas waktu pendaftaran: 25 Juli 2025. Informasi lebih lanjut dapat menghubungi Kantor Kepala Desa.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(8),
            ],

            // ========================================
            // BERITA KEGIATAN
            // ========================================
            [
                'kategori' => 'Berita',
                'judul' => 'Gotong Royong Bersihkan Saluran Irigasi di Dusun Tunong',
                'slug' => 'gotong-royong-bersihkan-saluran-irigasi-dusun-tunong',
                'konten' => '<p>Warga Desa Udeung melaksanakan kegiatan gotong royong massal dalam rangka pembersihan saluran irigasi di Dusun Tunong pada hari Minggu, 7 Juli 2025.</p>
                <p>Kegiatan yang dimulai pukul 07.00 WIB ini dihadiri oleh sekitar 40 warga dari berbagai dusun. Kepala Desa Desa Udeung, Tgk. H. Muhammad Yusuf, turut serta dalam kegiatan ini.</p>
                <p>"Pembersihan saluran irigasi ini sangat penting untuk memastikan pasokan air ke sawah warga tetap lancar, terutama menjelang musim tanam padi," ujar Kepala Desa.</p>
                <p>Kegiatan ini merupakan bagian dari program kerja pemerintah desa untuk menjaga infrastruktur pertanian. Saluran irigasi yang bersih dan lancar akan meningkatkan produktivitas pertanian warga.</p>
                <p>Warga berharap kegiatan serupa dapat dilaksanakan secara berkala untuk menjaga kebersihan dan kelancaran saluran irigasi di seluruh dusun.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(3),
            ],

            [
                'kategori' => 'Berita',
                'judul' => 'Kegiatan Pengajian Rutin Majelis Taklim Desa Udeung',
                'slug' => 'kegiatan-pengajian-rutin-majelis-taklim-desa-udeung',
                'konten' => '<p>Majelis Taklim Desa Udeung mengadakan kegiatan pengajian rutin setiap hari Rabu malam di Meunasah Desa Udeung. Pengajian ini diikuti oleh ibu-ibu dari seluruh dusun.</p>
                <p>Pada pengajian minggu ini, Ustazah Hj. Rahmawati menyampaikan materi tentang "Keutamaan Sedekah dalam Kehidupan Sehari-hari". Materi ini disambut antusias oleh para jamaah.</p>
                <p>"Pengajian rutin ini selain untuk menambah ilmu agama, juga menjadi ajang silaturahmi antar warga," kata Ketua Majelis Taklim, Ibu Cut Nurhaliza.</p>
                <p>Kegiatan pengajian dimulai pukul 20.00 WIB hingga selesai dengan format tadarus Al-Qur’an, ceramah, dan doa bersama.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(6),
            ],

            [
                'kategori' => 'Berita',
                'judul' => 'Vaksinasi Massal Anak Usia Dini di Posyandu Desa Udeung',
                'slug' => 'vaksinasi-massal-anak-usia-dini-posyandu-desa-udeung',
                'konten' => '<p>Posyandu Desa Udeung mengadakan kegiatan vaksinasi massal untuk anak usia dini (0-5 tahun) pada hari Kamis, 10 Juli 2025 di Aula Kantor Kepala Desa.</p>
                <p>Kegiatan ini dihadiri oleh 45 orang tua yang membawa anak mereka untuk divaksin. Petugas kesehatan dari Puskesmas Bandar Baru turut serta dalam kegiatan ini.</p>
                <p>Ketua Posyandu, Ibu Nurul Hidayah, mengatakan bahwa vaksinasi ini sangat penting untuk menjaga kesehatan anak-anak. "Vaksinasi adalah salah satu upaya pencegahan penyakit yang efektif untuk anak-anak kita," ujarnya.</p>
                <p>Jenis vaksin yang diberikan meliputi vaksin DPT, Campak, dan Polio. Selain vaksinasi, petugas juga melakukan penimbangan dan pemeriksaan kesehatan dasar anak.</p>
                <p>Pemerintah desa menghimbau agar seluruh orang tua membawa anak mereka untuk mendapatkan vaksinasi demi kesehatan generasi penerus desa.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(10),
            ],

            [
                'kategori' => 'Berita',
                'judul' => 'Musyawarah Desa Rencana Anggaran Pendapatan dan Belanja Desa (APBG) Tahun 2026',
                'slug' => 'musyawarah-desa-apbg-2026',
                'konten' => '<p>Pemerintah Desa Udeung mengadakan musyawarah desa untuk pembahasan Rencana Anggaran Pendapatan dan Belanja Desa (APBG) Tahun 2026 pada hari Sabtu, 5 Juli 2025 di Aula Kantor Kepala Desa.</p>
                <p>Musyawarah yang dihadiri oleh seluruh perangkat desa, tokoh masyarakat, tokoh agama, dan perwakilan warga dari setiap dusun ini membahas prioritas pembangunan untuk tahun 2026.</p>
                <p>Beberapa program prioritas yang dibahas antara lain:</p>
                <ul>
                    <li>Pembangunan jalan usaha tani di Dusun Tunong</li>
                    <li>Rehabilitasi saluran irigasi di Dusun Tengah</li>
                    <li>Pembangunan pagar meunasah</li>
                    <li>Pengadaan alat pertanian untuk kelompok tani</li>
                    <li>Program pemberdayaan UMKM desa</li>
                </ul>
                <p>Kepala Desa Desa Udeung menyampaikan bahwa RAPBG Tahun 2026 dirancang untuk menjawab kebutuhan masyarakat dan mendukung pembangunan berkelanjutan di desa.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(12),
            ],

            [
                'kategori' => 'Berita',
                'judul' => 'Pelatihan Pengolahan Kelapa bagi Ibu-Ibu PKK Desa Udeung',
                'slug' => 'pelatihan-pengolahan-kelapa-pkk-desa-udeung',
                'konten' => '<p>Tim Penggerak PKK Desa Udeung mengadakan pelatihan pengolahan kelapa bagi ibu-ibu anggota PKK pada hari Rabu, 2 Juli 2025 di Balai Penyuluhan Desa.</p>
                <p>Pelatihan yang diikuti oleh 25 peserta ini menghadirkan narasumber dari Dinas Perindustrian dan Perdagangan Kabupaten Pidie Jaya. Materi yang disampaikan meliputi pembuatan minyak kelapa murni, sabun kelapa, dan produk olahan kelapa lainnya.</p>
                <p>"Pelatihan ini diharapkan dapat memberikan keterampilan baru bagi ibu-ibu untuk meningkatkan penghasilan keluarga," kata Ketua PKK Desa Udeung, Ibu Cut Rahmawati.</p>
                <p>Produk olahan kelapa memiliki potensi pasar yang cukup baik, mengingat Desa Udeung merupakan penghasil kelapa yang cukup melimpah. Dengan pengolahan yang tepat, nilai jual kelapa dapat meningkat hingga 3-4 kali lipat.</p>
                <p>PKK desa berencana akan melanjutkan pelatihan ini dengan materi pengolahan hasil pertanian lainnya.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(15),
            ],

            [
                'kategori' => 'Berita',
                'judul' => 'Penyerahan Sertifikat Tanah Program PTSL di Desa Udeung',
                'slug' => 'penyerahan-sertifikat-tanah-ptsl-desa-udeung',
                'konten' => '<p>Pemerintah Desa Udeung menyerahkan sertifikat tanah program Pendaftaran Tanah Sistematis Lengkap (PTSL) kepada 45 warga pada hari Jumat, 28 Juni 2025.</p>
                <p>Penyerahan sertifikat dilakukan oleh Kepala Desa Desa Udeung di Aula Kantor Kepala Desa. Program PTSL ini merupakan program nasional yang bertujuan untuk mempercepat pendaftaran tanah di Indonesia.</p>
                <p>"Dengan memiliki sertifikat tanah, warga dapat menjadikan tanahnya sebagai jaminan untuk pengajuan pinjaman di bank," ujar Kepala Desa.</p>
                <p>Program PTSL di Desa Udeung telah berjalan sejak tahun 2023 dan telah menyerahkan sertifikat kepada lebih dari 200 bidang tanah. Untuk tahun 2025, target yang ditetapkan adalah 100 bidang tanah lagi.</p>
                <p>Warga yang menerima sertifikat mengaku senang karena tanah mereka telah memiliki kepastian hukum. "Saya sudah lama menunggu sertifikat ini. Sekarang saya merasa tenang karena tanah saya sudah bersertifikat," kata salah satu penerima sertifikat.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(18),
            ],

            [
                'kategori' => 'Berita',
                'judul' => 'Kegiatan Shalat Berjamaah dan Tausiyah di Meunasah Desa Udeung',
                'slug' => 'kegiatan-shalat-berjamaah-tausiyah-meunasah-desa-udeung',
                'konten' => '<p>Meunasah Desa Udeung mengadakan kegiatan shalat berjamaah dan tausiyah rutin setiap hari Jumat malam. Kegiatan ini diikuti oleh jamaah dari seluruh dusun.</p>
                <p>Pada tausiyah minggu ini, Ustaz Tgk. H. Abdullah Ibrahim menyampaikan materi tentang "Menjaga Persatuan dan Kesatuan Umat".</p>
                <p>"Di era digital seperti sekarang ini, kita harus bijak dalam menggunakan media sosial. Jangan sampai media sosial memecah belah persatuan kita," pesan Ustaz.</p>
                <p>Kegiatan ini merupakan program kerja remaja meunasah yang sudah berjalan selama 2 tahun. Selain tausiyah, kegiatan ini juga diisi dengan doa bersama untuk kesejahteraan desa.</p>
                <p>Pihak pengurus meunasah mengucapkan terima kasih kepada seluruh warga yang telah hadir dan berpartisipasi dalam kegiatan ini.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(20),
            ],

            // ========================================
            // PENGUMUMAN LAINNYA
            // ========================================
            [
                'kategori' => 'Pengumuman',
                'judul' => 'Jadwal Imunisasi Anak di Posyandu Desa Udeung',
                'slug' => 'jadwal-imunisasi-anak-posyandu-desa-udeung',
                'konten' => '<p>Posyandu Desa Udeung mengumumkan jadwal imunisasi anak untuk bulan Juli 2025:</p>
                <ul>
                    <li><strong>Tanggal:</strong> Setiap hari Kamis minggu kedua dan keempat</li>
                    <li><strong>Waktu:</strong> 08.00 - 11.00 WIB</li>
                    <li><strong>Tempat:</strong> Posyandu Desa Udeung</li>
                </ul>
                <p><strong>Jenis Imunisasi:</strong></p>
                <ul>
                    <li>BCG (Bayi 0-2 bulan)</li>
                    <li>DPT (Bayi 2-4 bulan)</li>
                    <li>Campak (Bayi 9 bulan)</li>
                    <li>Polio (Bayi 0-5 tahun)</li>
                </ul>
                <p>Bagi orang tua yang belum membawa anaknya untuk imunisasi, silakan datang pada jadwal yang telah ditentukan. Imunisasi gratis dan tidak dipungut biaya.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(25),
            ],

            [
                'kategori' => 'Pengumuman',
                'judul' => 'Penerimaan Siswa Baru Tahun Ajaran 2025/2026 di TK Desa Udeung',
                'slug' => 'penerimaan-siswa-baru-tk-desa-udeung-2025-2026',
                'konten' => '<p>TK Desa Udeung membuka pendaftaran siswa baru untuk tahun ajaran 2025/2026.</p>
                <p><strong>Persyaratan:</strong></p>
                <ul>
                    <li>Anak usia 4-6 tahun</li>
                    <li>Fotokopi akta kelahiran</li>
                    <li>Fotokopi KTP orang tua</li>
                    <li>Pas foto anak 2x3 (3 lembar)</li>
                </ul>
                <p><strong>Jadwal Pendaftaran:</strong></p>
                <ul>
                    <li>Gelombang I: 1 - 15 Juli 2025</li>
                    <li>Gelombang II: 16 - 31 Juli 2025</li>
                </ul>
                <p><strong>Piutang Masuk:</strong></p>
                <ul>
                    <li>SPP Bulanan: Rp 50.000</li>
                    <li>Uang Masuk: Rp 100.000</li>
                    <li>Uang Seragam: Rp 75.000</li>
                </ul>
                <p>Informasi lebih lanjut dapat menghubungi Kepala TK Desa Udeung atau datang langsung ke TK.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(28),
            ],

            // ========================================
            // BERITA LAINNYA
            // ========================================
            [
                'kategori' => 'Berita',
                'judul' => 'Kunjungan Tim Evaluasi Desa Tingkat Kabupaten Pidie Jaya ke Desa Udeung',
                'slug' => 'kunjungan-tim-evaluasi-desa-pidie-jaya-desa-udeung',
                'konten' => '<p>Tim Evaluasi Desa Tingkat Kabupaten Pidie Jaya mengunjungi Desa Udeung pada hari Selasa, 24 Juni 2025 dalam rangka evaluasi program pemberdayaan desa.</p>
                <p>Tim yang terdiri dari 5 orang ini dipimpin oleh Kepala Bidang Pemberdayaan Masyarakat Desa Dinas Pemberdayaan Masyarakat Desa Kabupaten Pidie Jaya.</p>
                <p>Evaluasi yang dilakukan meliputi aspek pemerintahan, pembangunan, pemberdayaan masyarakat, dan pelayanan publik. Tim meninjau langsung beberapa infrastruktur desa yang telah dibangun.</p>
                <p>"Desa Udeung termasuk dalam kategori desa yang aktif dalam melaksanakan program-program pembangunan. Kami berharap capaian ini dapat dipertahankan dan ditingkatkan," kata Ketua Tim Evaluasi.</p>
                <p>Kepala Desa Desa Udeung menyambut baik kunjungan ini sebagai bentuk monitoring dan evaluasi terhadap kinerja pemerintah desa.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(30),
            ],

            [
                'kategori' => 'Berita',
                'judul' => 'Kegiatan Khitanan Massal di Desa Udeung Tahun 2025',
                'slug' => 'kegiatan-khitanan-massal-desa-udeung-2025',
                'konten' => '<p>Pemerintah Desa Udeung bekerja sama dengan Rumah Sakit Umum Daerah Meureudu mengadakan kegiatan khitanan massal pada hari Sabtu, 21 Juni 2025.</p>
                <p>Kegiatan ini diikuti oleh 20 anak dari keluarga kurang mampu di Desa Udeung. Khitanan dilakukan oleh tim dokter dari RSUD Meureudu.</p>
                <p>"Khitanan massal ini merupakan program rutin pemerintah desa untuk membantu warga yang kurang mampu," kata Kepala Desa Desa Udeung.</p>
                <p>Selain khitanan massal, kegiatan ini juga diisi dengan penyuluhan kesehatan bagi orang tua anak tentang pentingnya menjaga kebersihan alat kelamin anak pasca khitan.</p>
                <p>Pemerintah desa mengucapkan terima kasih kepada RSUD Meureudu yang telah membantu melaksanakan kegiatan ini.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(35),
            ],

            [
                'kategori' => 'Pengumuman',
                'judul' => 'Pengumuman Pemenang Lomba Kebersihan Lingkungan Tingkat Dusun',
                'slug' => 'pengumuman-pemenang-lomba-kebersihan-lingkungan-dusun',
                'konten' => '<p>Berdasarkan hasil penilaian panitia lomba kebersihan lingkungan tingkat dusun di Desa Udeung, dengan ini diumumkan para pemenang:</p>
                <p><strong>Juara I:</strong> Dusun Tunong</p>
                <p><strong>Juara II:</strong> Dusun Tengah</p>
                <p><strong>Juara III:</strong> Dusun Baroh</p>
                <p>Penilaian dilakukan oleh tim juri yang terdiri dari perangkat desa, bidan desa, dan tokoh masyarakat. Kriteria penilaian meliputi kebersihan halaman, kebersihan parit, dan penataan taman.</p>
                <p>Pemenang akan mendapatkan hadiah berupa piagam penghargaan dan uang pembinaan. Penyerahan hadiah akan dilaksanakan pada upacara Hari Kemerdekaan.</p>
                <p>Semua dusun diharapkan dapat terus menjaga kebersihan lingkungan masing-masing demi kenyamanan dan kesehatan bersama.</p>',
                'is_published' => true,
                'created_at' => Carbon::now()->subDays(40),
            ],
        ];

        // Set author_id untuk setiap berita
        foreach ($berita as &$item) {
            $item['author_id'] = $admin->id;
        }

        // Simpan ke database
        foreach ($berita as $item) {
            InformasiPublik::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
