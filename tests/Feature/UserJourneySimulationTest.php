<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\KategoriSurat;
use App\Models\PengajuanSurat;
use App\Models\MutasiPenduduk;
use App\Models\InformasiPublik;
use App\Models\ChatbotLog;
use App\Models\AuditLog;
use Database\Seeders\ReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Simulasi Pengujian Otomatis End-to-End Layaknya User Sungguhan (User Journey Simulation)
 * 
 * Pengujian ini didesain sesuai standar industri untuk memvalidasi interaksi
 * sistem lintas lapisan:
 * 1. Simulasi akses Frontend Public (Guest)
 * 2. Login, autentikasi, dan verifikasi Security Headers (Citizen & Admin)
 * 3. Alur pengajuan surat mandiri oleh warga dengan input dinamis
 * 4. Pembuatan laporan mutasi oleh warga
 * 5. Interaksi fitur Chatbot (Knowledge Base)
 * 6. Validasi dari sisi Admin (Approve/Reject)
 * 7. Simulasi job pembuatan dokumen PDF dan pembuatan QR Code
 * 8. Validasi log jejak audit sistem (Audit Trail)
 * 9. Pengecekan statistik dan integritas performa database
 */
class UserJourneySimulationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $wargaA;
    protected $wargaB;
    protected $adminDesa;
    protected $kategoriSurat;

    protected function setUp(): void
    {
        parent::setUp();
        
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('queue.default', 'sync');
        
        \Illuminate\Support\Facades\Cache::flush();
        Storage::fake('public');

        // Menanam referensi master table
        $this->seed(ReferenceSeeder::class);

        // Menyiapkan data Desa
        \App\Models\PengaturanDesa::set('nama_desa', 'AvaraDesa Simulation');
        \App\Models\PengaturanDesa::set('ai_active_provider', 'openai');
        \App\Models\PengaturanDesa::set('ai_openai_key', 'mocked_openai_key_123');

        Config::set('services.ai.openai.api_key', 'mocked_openai_key_123');
        Config::set('services.ai.active_provider', 'openai');

        // Menyiapkan Aktor Warga A (Kepala Keluarga)
        $keluargaA = Keluarga::factory()->create(['dusun' => 'Dusun Mawar']);
        $this->wargaA = Penduduk::factory()->create([
            'no_kk' => $keluargaA->no_kk,
            'nama_lengkap' => 'Bapak Warga Sejahtera',
            'is_kepala_keluarga' => true,
            'status_keluarga' => 'Kepala Keluarga'
        ]);

        // Menyiapkan Aktor Warga B (Istri)
        $this->wargaB = Penduduk::factory()->create([
            'no_kk' => $keluargaA->no_kk,
            'nama_lengkap' => 'Ibu Warga Bahagia',
            'is_kepala_keluarga' => false,
            'status_keluarga' => 'Istri'
        ]);
        
        $keluargaA->update(['kepala_keluarga_nik' => $this->wargaA->nik]);

        // Menyiapkan Aktor Administrator (Kepala Desa)
        $this->adminDesa = Administrator::factory()->kepalaDesa()->create([
            'username' => 'kades_simulation',
            'password' => bcrypt('password_sangat_kuat_123'),
        ]);

        // Menyiapkan Entitas Laporan
        $this->kategoriSurat = KategoriSurat::factory()->create([
            'kode_surat' => 'SKD',
            'nama_surat' => 'Surat Keterangan Domisili',
            'schema_isian' => [
                ['field' => 'keperluan', 'label' => 'Keperluan', 'type' => 'text', 'required' => true],
                ['field' => 'lama_tinggal', 'label' => 'Lama Tinggal', 'type' => 'number', 'required' => true]
            ],
            'syarat_dokumen' => ['KTP', 'Kartu Keluarga'],
        ]);

        InformasiPublik::factory()->create([
            'judul' => 'Pengumuman Penting Sistem Baru',
            'is_published' => true,
        ]);
    }

    /**
     * @test
     * SCENARIO 1: Simulasi Lengkap Pengunjung Publik (Guest)
     */
    public function test_guest_public_exploration_journey()
    {
        // 1. User mengakses halaman beranda
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('AvaraDesa Simulation'); // Verifikasi setting DB dipanggil
        
        // 2. User melihat data statistik (API call oleh frontend)
        $statsResponse = $this->getJson('/api/v1/statistik/demografi');
        $statsResponse->assertStatus(200);
        $statsResponse->assertJsonStructure([
            'data' => ['total_penduduk', 'laki_laki', 'perempuan', 'total_keluarga']
        ]);
        $this->assertEquals(2, $statsResponse->json('data.total_penduduk')); // Sesuai wargaA dan wargaB
        
        // 3. User mengakses halaman profil desa dan fasilitas
        $this->get('/profil')->assertStatus(200);
        $this->get('/fasilitas')->assertStatus(200);
        
        // 4. User mencoba akses dashboard warga (Harus di-redirect ke login)
        $this->get('/warga/dashboard')->assertRedirect('login');
    }

    /**
     * @test
     * SCENARIO 2: Simulasi Interaksi Chatbot Telegram oleh Warga
     */
    public function test_telegram_chatbot_knowledge_interaction()
    {
        $webhookSecret = 'secure_secret_token_123';
        Config::set('services.telegram.webhook_secret', $webhookSecret);
        Config::set('services.telegram.bot_token', 'mocked_bot_token');
        
        $this->instance(\App\Services\Contracts\AiProviderInterface::class, new class implements \App\Services\Contracts\AiProviderInterface {
            public function generateResponse(string $msg, string $chatId, ?string $ctx = null): ?string {
                \App\Models\ChatbotLog::create([
                    'telegram_chat_id' => $chatId,
                    'pesan_masuk' => $msg,
                    'balasan_ai' => 'Jawaban simulasi chatbot AvaraDesa',
                ]);
                return 'Jawaban simulasi chatbot AvaraDesa';
            }
            public function fixCopywriting(string $t, ?string $title = null): ?string { return $t; }
            public function generateSeoMetadata(string $t, string $c): ?array { return []; }
            public function checkHealth(): bool { return true; }
        });
        
        Http::fake([
            '*api.telegram.org*' => Http::response(['ok' => true], 200)
        ]);
        
        // Warga menanyakan prosedur domisili via Telegram Bot
        $payload = [
            'update_id' => 123456789,
            'message' => [
                'message_id' => 1,
                'from' => ['id' => 999888777, 'is_bot' => false, 'first_name' => 'WargaA'],
                'chat' => ['id' => 999888777, 'type' => 'private'],
                'date' => time(),
                'text' => 'Tolong jelaskan secara singkat visi dari sistem desa ini?',
            ]
        ];

        // Simulasi request masuk ke Webhook dengan auth secret valid
        $response = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => $webhookSecret,
        ])->postJson('/api/v1/telegram/webhook', $payload);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('chatbot_logs', [
            'telegram_chat_id' => '999888777',
        ]);
    }

    /**
     * @test
     * SCENARIO 3: Alur Gabungan User & Administrator E2E (Citizen submit -> Admin approve -> Public verification)
     */
    public function test_citizen_and_administrator_end_to_end_operations_journey()
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        $this->instance(\App\Services\Contracts\AiProviderInterface::class, new class implements \App\Services\Contracts\AiProviderInterface {
            public function generateResponse(string $msg, string $chatId, ?string $ctx = null): ?string {
                \App\Models\ChatbotLog::create([
                    'telegram_chat_id' => $chatId,
                    'pesan_masuk' => $msg,
                    'balasan_ai' => 'Jawaban simulasi chatbot AvaraDesa',
                ]);
                return 'Jawaban simulasi chatbot AvaraDesa';
            }
            public function fixCopywriting(string $t, ?string $title = null): ?string { return $t; }
            public function generateSeoMetadata(string $t, string $c): ?array { return []; }
            public function checkHealth(): bool { return true; }
        });

        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
            'https://upload.wikimedia.org/*' => Http::response('logo_data_binary', 200)
        ]);

        // === BAGIAN 1: ALUR CITIZEN (WARGA A) ===

        // 1. WargaA membuka form login app mobile / web
        $loginData = [
            'nik' => $this->wargaA->nik,
            'no_kk' => $this->wargaA->no_kk,
        ];

        $loginResponse = $this->postJson('/api/v1/auth/login/warga', $loginData);
        $loginResponse->assertStatus(200);
        $loginResponse->assertJsonStructure(['token', 'user']);
        
        $token = $loginResponse->json('token');

        // 2. WargaA mengakses profil pribadinya
        $profilResponse = $this->withToken($token)->getJson('/api/v1/auth/profile');
        $profilResponse->assertStatus(200);
        $this->assertEquals('Bapak Warga Sejahtera', $profilResponse->json('user.nama_lengkap'));

        // 3. WargaA memeriksa daftar kategori surat yang tersedia
        $kategoriResponse = $this->withToken($token)->getJson('/api/v1/surat/kategori');
        $kategoriResponse->assertStatus(200);
        $kategoriId = $kategoriResponse->json('data.0.id');

        // 4. WargaA mengisi form pengajuan surat
        $pengajuanData = [
            'kategori_surat_id' => $kategoriId,
            'data_isian' => [
                'keperluan' => 'Pendaftaran Sekolah Anak',
                'lama_tinggal' => '5'
            ],
            'file_syarat' => [
                'KTP' => \Illuminate\Http\UploadedFile::fake()->image('fake_ktp.jpg'),
                'Kartu Keluarga' => \Illuminate\Http\UploadedFile::fake()->create('fake_kk.pdf', 100, 'application/pdf')
            ]
        ];

        $submitResponse = $this->withToken($token)->postJson('/api/v1/surat/pengajuan', $pengajuanData);
        $submitResponse->assertStatus(201);
        
        $pengajuanId = $submitResponse->json('data.id');
        $nomorRegistrasi = $submitResponse->json('data.nomor_registrasi');

        // 5. Verifikasi Integrity: WargaA mengecek riwayat pengajuan (Tracking terbuat otomatis)
        $riwayatResponse = $this->withToken($token)->getJson("/api/v1/surat/pengajuan/{$pengajuanId}");
        $riwayatResponse->assertStatus(200);
        $this->assertEquals('Pending', $riwayatResponse->json('data.status'));
        $this->assertCount(1, $riwayatResponse->json('data.tracking'));

        // 6. WargaA mengajukan Mutasi Penduduk (Kematian Anggota Keluarga)
        $mutasiData = [
            'nik' => $this->wargaA->nik,
            'jenis_mutasi' => 'Kematian',
            'tanggal_mutasi' => now()->format('Y-m-d'),
            'keterangan' => 'Meninggal karena sakit',
            'dokumen_bukti' => 'documents/surat_rs.pdf',
        ];

        $mutasiResponse = $this->withToken($token)->postJson('/api/v1/mutasi', $mutasiData);
        $mutasiResponse->assertStatus(201);
        $mutasiId = $mutasiResponse->json('data.id');

        // 7. Security Header & Audit Log Verification
        $submitResponse->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        
        $this->assertDatabaseHas('audit_logs', [
            'tindakan' => 'create',
            'nama_tabel' => 'pengajuan_surat',
            'user_type' => 'warga',
            'user_id' => $this->wargaA->nik,
        ]);

        // 8. WargaA Logout
        $logoutResponse = $this->withToken($token)->postJson('/api/v1/auth/logout');
        $logoutResponse->assertStatus(200);


        // === BAGIAN 2: ALUR ADMINISTRATOR (KEPALA DESA) ===

        // Clear resolved guards/cache between user switches in the same test method
        auth()->forgetGuards();

        // 1. Admin login ke sistem
        $loginAdminResponse = $this->postJson('/api/v1/auth/login/admin', [
            'username' => 'kades_simulation',
            'password' => 'password_sangat_kuat_123',
        ]);
        
        $loginAdminResponse->assertStatus(200);
        $adminToken = $loginAdminResponse->json('token');

        // 2. Admin membuka antrean pengajuan surat
        $antreanResponse = $this->withToken($adminToken)->getJson('/api/v1/admin/surat/pengajuan');
        if ($antreanResponse->status() !== 200) {
            dump($antreanResponse->json());
        }
        $antreanResponse->assertStatus(200);
        
        // 3. Admin memberikan approval (Menyetujui pengajuan)
        $approveResponse = $this->withToken($adminToken)->postJson("/api/v1/admin/surat/pengajuan/{$pengajuanId}/approve");
        $approveResponse->assertStatus(200);

        // 4. Verifikasi perubahan state dan job trigger di database
        $pengajuan = PengajuanSurat::find($pengajuanId);
        
        // Karena queue menggunakan default sync driver di testing,
        // job GenerateSuratPdfJob langsung tereksekusi secara sinkron
        $this->assertEquals('Selesai', $pengajuan->status);
        $this->assertNotNull($pengajuan->qr_hash);
        $this->assertNotNull($pengajuan->nomor_surat);
        $this->assertNotNull($pengajuan->file_pdf_url);
        
        // Memastikan tracking record bertambah (1 submit + 1 approve + 1 selesai pdf)
        $this->assertDatabaseCount('tracking_pengajuan_surat', 3);
        
        // Memastikan audit log mencatat aksi approval oleh admin
        $this->assertDatabaseHas('audit_logs', [
            'tindakan' => 'approve',
            'nama_tabel' => 'pengajuan_surat',
            'user_type' => 'admin',
            'user_id' => $this->adminDesa->id,
        ]);

        // 5. Admin menyetujui laporan Mutasi
        $approveMutasiResponse = $this->withToken($adminToken)->postJson("/api/v1/admin/mutasi/{$mutasiId}/approve");
        $approveMutasiResponse->assertStatus(200);

        // 6. Verifikasi Mutasi State: Status penduduk berubah secara atomik
        $mutasi = MutasiPenduduk::find($mutasiId);
        $this->assertEquals('Disetujui', $mutasi->status_verifikasi);
        
        $penduduk = Penduduk::find($this->wargaA->nik);
        $this->assertEquals('Meninggal', $penduduk->status_mutasi);

        // 7. Security: Cek proteksi akses public ke panel
        $adminPanelResponse = $this->get('/admin');
        $adminPanelResponse->assertRedirect();


        // === BAGIAN 3: VERIFIKASI QR CODE DIGITAL SECARA PUBLIK ===

        $qrHash = $pengajuan->qr_hash;
        
        // 1. Scan QR / Akses link verifikasi public
        $verifyResponse = $this->getJson("/api/v1/verifikasi/{$qrHash}");
        $verifyResponse->assertStatus(200);

        // 2. Verifikasi Data Surat Sesuai dengan Enkripsi
        $verifyData = $verifyResponse->json('data');
        $this->assertEquals('Bapak Warga Sejahtera', $verifyData['nama_pemohon']);
        $this->assertEquals('Surat Keterangan Domisili', $verifyData['jenis_surat']);
        $this->assertEquals($this->wargaA->nik, $verifyData['nik_pemohon']);
    }

    /**
     * @test
     * SCENARIO 4: Skalabilitas (Skenario Rate Limiting Security)
     */
    public function test_system_rate_limiting_resilience()
    {
        // Simulasi serangan Brute Force Login Warga
        $nik = $this->wargaB->nik;
        
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login/warga', [
                'nik' => $nik,
                'no_kk' => '0000000000000000' // wrong kk
            ]);
        }
        
        // Percobaan ke-6 harus di block oleh sistem (429 Too Many Requests)
        $throttledResponse = $this->postJson('/api/v1/auth/login/warga', [
            'nik' => $nik,
            'no_kk' => '0000000000000000'
        ]);
        
        $throttledResponse->assertStatus(429);
    }
}
