<?php

namespace Database\Seeders;

use App\Models\PengaturanDesa;
use Illuminate\Database\Seeder;

class PengaturanDesaSeeder extends Seeder
{
    /**
     * Buat pengaturan default untuk Desa Udeung.
     *
     * Identitas wilayah: nama_desa, kecamatan, kabupaten, provinsi, kode_pos
     * Kontak          : email, telepon, alamat_kantor
     * Pejabat         : nama_kepala_desa, nip_kepala_desa, nama_sekdes
     * Profil          : logo_desa, visi, misi, sejarah singkat
     * Administrasi    : jam_pelayanan, batas_pengajuan
     */
    public function run(): void
    {
        $settings = [
            // Identitas Wilayah
            [
                'kunci' => 'nama_desa',
                'nilai' => 'Udeung',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama resmi desa',
            ],
            [
                'kunci' => 'kecamatan',
                'nilai' => 'Bandar Baru',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama kecamatan',
            ],
            [
                'kunci' => 'kabupaten',
                'nilai' => 'Pidie Jaya',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama kabupaten',
            ],
            [
                'kunci' => 'provinsi',
                'nilai' => 'Aceh',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama provinsi',
            ],
            [
                'kunci' => 'kode_pos',
                'nilai' => '24186',
                'tipe_data' => 'string',
                'deskripsi' => 'Kode pos desa',
            ],
            [
                'kunci' => 'kode_desa',
                'nilai' => '11.18.06.2001',
                'tipe_data' => 'string',
                'deskripsi' => 'Kode desa sesuai Kemendagri',
            ],

            // Kontak & Alamat
            [
                'kunci' => 'email',
                'nilai' => 'desa.udeung@pidiejaya.go.id',
                'tipe_data' => 'string',
                'deskripsi' => 'Email resmi desa',
            ],
            [
                'kunci' => 'telepon',
                'nilai' => '0852-7000-1234',
                'tipe_data' => 'string',
                'deskripsi' => 'Nomor telepon kantor desa',
            ],
            [
                'kunci' => 'alamat_kantor',
                'nilai' => 'Jl. Utama Desa Udeung, Kec. Bandar Baru, Kab. Pidie Jaya, Aceh 24186',
                'tipe_data' => 'string',
                'deskripsi' => 'Alamat lengkap kantor kepala desa',
            ],

            // Pejabat Desa
            [
                'kunci' => 'nama_kepala_desa',
                'nilai' => 'Tgk. H. Muhammad Yusuf',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama lengkap Kepala Desa Udeung',
            ],
            [
                'kunci' => 'nip_kepala_desa',
                'nilai' => '197503152005011004',
                'tipe_data' => 'string',
                'deskripsi' => 'NIP Kepala Desa (jika PNS)',
            ],
            [
                'kunci' => 'nama_sekdes',
                'nilai' => 'Cut Nurhaliza, S.E.',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama lengkap Sekretaris Desa',
            ],
            [
                'kunci' => 'nama_operator',
                'nilai' => 'Muhammad Rizal',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama operator pelayanan desa',
            ],
            [
                'kunci' => 'no_hp',
                'nilai' => '6285270001234',
                'tipe_data' => 'string',
                'deskripsi' => 'Nomor WhatsApp kantor desa',
            ],

            // Profil Desa
            [
                'kunci' => 'logo_desa',
                'nilai' => '/images/logo-desa.png',
                'tipe_data' => 'string',
                'deskripsi' => 'Path logo desa',
            ],
            [
                'kunci' => 'visi',
                'nilai' => 'Mewujudkan Desa Udeung yang Maju, Mandiri, dan Sejahtera Berlandaskan Gotong Royong dan Nilai-Nilai Luhur',
                'tipe_data' => 'string',
                'deskripsi' => 'Visi desa',
            ],
            [
                'kunci' => 'misi',
                'nilai' => json_encode([
                    'Meningkatkan kualitas pelayanan publik yang transparan dan akuntabel',
                    'Memberdayakan ekonomi masyarakat melalui program pemberdayaan UMKM',
                    'Meningkatkan infrastruktur desa secara merata dan berkelanjutan',
                    'Menjaga keamanan dan ketertiban masyarakat melalui pola kebersamaan',
                    'Meningkatkan kualitas sumber daya manusia melalui pendidikan dan pelatihan',
                    'Melestarikan adat istiadat dan kearifan lokal di lingkungan desa',
                ]),
                'tipe_data' => 'json',
                'deskripsi' => 'Misi desa',
            ],
            [
                'kunci' => 'sejarah_singkat',
                'nilai' => 'Desa Udeung merupakan salah satu desa yang terletak di Kecamatan Bandar Baru, Kabupaten Pidie Jaya, Provinsi Aceh. Nama "Udeung" berasal dari bahasa daerah setempat yang berarti "tanah tinggi" karena geografis desa ini yang berada di dataran agak tinggi. Desa ini dikenal sebagai penghasil padi dan kelapa yang melimpah. Masyarakat Desa Udeung mayoritas berprofesi sebagai petani dan pedagang kecil.',
                'tipe_data' => 'string',
                'deskripsi' => 'Sejarah singkat desa',
            ],

            // Administrasi & Pelayanan
            [
                'kunci' => 'jam_pelayanan',
                'nilai' => 'Senin - Jumat: 08.00 - 12.00 WIB',
                'tipe_data' => 'string',
                'deskripsi' => 'Jam pelayanan kantor desa',
            ],
            [
                'kunci' => 'batas_pengajuan_surat',
                'nilai' => '16:00',
                'tipe_data' => 'string',
                'deskripsi' => 'Batas waktu pengajuan surat per hari',
            ],
            [
                'kunci' => 'nomor_rekening',
                'nilai' => '0123-456-789012',
                'tipe_data' => 'string',
                'deskripsi' => 'Nomor rekening kas desa',
            ],
            [
                'kunci' => 'nama_bank',
                'nilai' => 'Bank Aceh Syariah',
                'tipe_data' => 'string',
                'deskripsi' => 'Nama bank kas desa',
            ],
            [
                'kunci' => 'wa_gateway_url',
                'nilai' => 'http://127.0.0.1:2785',
                'tipe_data' => 'string',
                'deskripsi' => 'URL gateway WhatsApp',
            ],
            [
                'kunci' => 'wa_default_target',
                'nilai' => '6285270001234',
                'tipe_data' => 'string',
                'deskripsi' => 'Nomor default untuk notifikasi WhatsApp',
            ],
            [
                'kunci' => 'wa_provider',
                'nilai' => 'wa-gateway',
                'tipe_data' => 'string',
                'deskripsi' => 'Provider WhatsApp (wa-gateway / fonnte)',
            ],
        ];

        foreach ($settings as $setting) {
            PengaturanDesa::create($setting);
        }
    }
}
