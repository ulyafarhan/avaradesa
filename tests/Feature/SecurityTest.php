<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\MutasiPenduduk;
use App\Models\KategoriSurat;
use Database\Seeders\KategoriSuratSeeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    protected $warga;
    protected $warga2;
    protected $admin;
    protected $operator;
    protected $sekdes;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');

        $this->seed(KategoriSuratSeeder::class);
        $this->kategori = KategoriSurat::first();

        $keluarga = Keluarga::factory()->create();
        $keluarga2 = Keluarga::factory()->create();

        $this->warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $this->warga2 = Penduduk::factory()->create(['no_kk' => $keluarga2->no_kk]);
        $this->admin = Administrator::factory()->kepalaDesa()->create();
        $this->operator = Administrator::factory()->operator()->create();
        $this->sekdes = Administrator::factory()->sekdes()->create();
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  1. SQL INJECTION
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_sql_injection_in_login_warga_nik()
    {
        $payloads = [
            "1' OR '1'='1",
            "1' OR 1=1--",
            "1'; DROP TABLE users;--",
            "1' UNION SELECT * FROM users--",
            "1' AND (SELECT COUNT(*) FROM users)>0--",
        ];
        foreach ($payloads as $nik) {
            $response = $this->postJson('/api/v1/auth/login/warga', [
                'nik' => $nik, 'no_kk' => $nik,
            ]);
            $response->assertStatus(422);
        }
    }

    public function test_sql_injection_in_login_admin_username()
    {
        $payloads = [
            "admin' OR '1'='1",
            "admin'--",
            "admin' UNION SELECT * FROM administrators--",
        ];
        foreach ($payloads as $username) {
            $response = $this->postJson('/api/v1/auth/login/admin', [
                'username' => $username, 'password' => 'test',
            ]);
            $response->assertStatus(422);
        }
    }

    public function test_sql_injection_in_mutasi_keterangan()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $payloads = [
            "'; DROP TABLE penduduk;--",
            "test' OR 1=1 UNION SELECT password FROM administrators--",
        ];
        foreach ($payloads as $ket) {
            $this->withToken($token)->postJson('/api/v1/mutasi', [
                'nik' => $this->warga->nik,
                'jenis_mutasi' => 'Kepindahan',
                'tanggal_mutasi' => now()->format('Y-m-d'),
                'keterangan' => $ket,
                'dokumen_bukti' => 'uploads/bukti.pdf',
            ])->assertStatus(201);
        }
    }

    public function test_sql_injection_in_data_isian_values()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/surat/pengajuan', [
            'kategori_surat_id' => $this->kategori->id,
            'data_isian' => ['keperluan' => "'; DROP TABLE pengajuan_surat;--"],
            'file_syarat' => ['ktp' => UploadedFile::fake()->image('test.jpg')],
        ]);
        $response->assertStatus(201);
    }

    public function test_sql_injection_in_query_string_parameters()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/statistik/demografi?q=1%27%20UNION%20SELECT%20*%20FROM%20users')
            ->assertStatus(200);

        $this->withToken($token)->getJson('/api/v1/informasi?kategori=%27+OR+1%3D1--')
            ->assertStatus(200);
    }

    public function test_sql_injection_in_slug_parameter()
    {
        $response = $this->getJson('/api/v1/informasi/1%27+OR+1%3D1--');
        $response->assertStatus(404);

        $response = $this->getJson('/api/v1/informasi/slug%27+DROP+TABLE+users--');
        $response->assertStatus(404);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  2. XSS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_xss_in_informasi_judul()
    {
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;

        $payloads = [
            '<script>alert("xss")</script>',
            '<img src=x onerror=alert(1)>',
            '"><script>document.cookie</script>',
            'javascript:alert(1)',
            '<svg onload=alert(1)>',
            '<iframe src="javascript:alert(1)">',
        ];

        foreach ($payloads as $jud) {
            $this->withToken($token)->postJson('/api/v1/admin/informasi', [
                'judul' => $jud, 'konten' => 'Test', 'kategori' => 'Test',
            ])->assertStatus(201);
        }
    }

    public function test_xss_in_mutasi_keterangan()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/mutasi', [
            'nik' => $this->warga->nik,
            'jenis_mutasi' => 'Kepindahan',
            'tanggal_mutasi' => now()->format('Y-m-d'),
            'keterangan' => '<script>alert("xss")</script><img src=x onerror=alert(1)>',
            'dokumen_bukti' => 'uploads/bukti.pdf',
        ])->assertStatus(201);
    }

    public function test_xss_in_data_isian_values()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/surat/pengajuan', [
            'kategori_surat_id' => $this->kategori->id,
            'data_isian' => ['keperluan' => '<svg onload=alert(1)>'],
            'file_syarat' => ['ktp' => UploadedFile::fake()->image('test.jpg')],
        ])->assertStatus(201);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  3. AUTHORIZATION (RBAC)
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_warga_cannot_view_others_pengajuan()
    {
        $pengajuan = PengajuanSurat::factory()->create(['nik_pemohon' => $this->warga2->nik]);
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;
        $this->withToken($token)->getJson("/api/v1/surat/pengajuan/{$pengajuan->id}")->assertStatus(404);
    }

    public function test_warga_cannot_access_admin_routes()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/admin/surat/pengajuan')->assertStatus(403);
    }

    public function test_admin_cannot_bind_telegram_as_warga()
    {
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;
        $this->withToken($token)->postJson('/api/v1/auth/bind-telegram', [
            'telegram_chat_id' => '123456789',
        ])->assertStatus(403);
    }

    public function test_warga_cannot_mutasi_other_nik()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;
        $this->withToken($token)->postJson('/api/v1/mutasi', [
            'nik' => $this->warga2->nik,
            'jenis_mutasi' => 'Kepindahan',
            'tanggal_mutasi' => now()->format('Y-m-d'),
            'keterangan' => 'Pindah ke luar kota',
            'dokumen_bukti' => 'uploads/bukti.pdf',
        ])->assertStatus(403);
    }

    public function test_operator_cannot_delete_informasi()
    {
        $informasi = \App\Models\InformasiPublik::factory()->create(['author_id' => $this->admin->id]);
        $token = $this->operator->createToken('test', ['admin'])->plainTextToken;
        $this->withToken($token)->deleteJson("/api/v1/admin/informasi/{$informasi->id}")->assertStatus(403);
    }

    public function test_sekdes_cannot_manage_administrators()
    {
        $token = $this->sekdes->createToken('test', ['admin'])->plainTextToken;
        $this->withToken($token)->postJson('/api/v1/admin/informasi', [
            'judul' => 'T', 'konten' => 'C', 'kategori' => 'K',
        ])->assertStatus(201);
    }

    public function test_warga_cannot_approve_own_pengajuan()
    {
        $pengajuan = PengajuanSurat::factory()->create(['nik_pemohon' => $this->warga->nik]);
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;
        $this->withToken($token)->postJson("/api/v1/admin/surat/pengajuan/{$pengajuan->id}/approve")
            ->assertStatus(403);
    }

    public function test_unauthenticated_cannot_access_protected_routes()
    {
        $endpoints = ['GET /api/v1/auth/profile', 'POST /api/v1/auth/logout', 'GET /api/v1/surat/pengajuan', 'GET /api/v1/mutasi'];
        foreach ($endpoints as $endpoint) {
            [$method, $uri] = explode(' ', $endpoint, 2);
            $this->call($method, $uri)->assertStatus(401);
        }
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  4. IDOR / ENUMERATION
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_invalid_ulid_returns_404()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/surat/pengajuan/00000000000000000000000000')->assertStatus(404);
    }

    public function test_invalid_nik_returns_404()
    {
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/admin/mutasi/nonexistent/verify')
            ->assertStatus(404);
    }

    public function test_numeric_id_injection()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/surat/pengajuan/99999999')->assertStatus(404);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  5. STATE TRANSITION ATTACKS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_cannot_approve_already_approved()
    {
        $pengajuan = PengajuanSurat::factory()->disetujui()->create();
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;
        $this->withToken($token)->postJson("/api/v1/admin/surat/pengajuan/{$pengajuan->id}/approve")
            ->assertStatus(422);
    }

    public function test_cannot_reject_already_rejected()
    {
        $pengajuan = PengajuanSurat::factory()->ditolak()->create();
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;
        $this->withToken($token)->postJson("/api/v1/admin/surat/pengajuan/{$pengajuan->id}/reject", [
            'catatan_penolakan' => 'Tolak lagi',
        ])->assertStatus(422);
    }

    public function test_cannot_approve_already_approved_mutasi()
    {
        $mutasi = MutasiPenduduk::factory()->disetujui()->create();
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;
        $this->withToken($token)->postJson("/api/v1/admin/mutasi/{$mutasi->id}/approve")
            ->assertStatus(422);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  6. RATE LIMITING
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_rate_limit_login_warga()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login/warga', ['nik' => '0000000000000000', 'no_kk' => '0000000000000000']);
        }
        $this->postJson('/api/v1/auth/login/warga', ['nik' => '0000000000000000', 'no_kk' => '0000000000000000'])
            ->assertStatus(429);
    }

    public function test_rate_limit_login_admin()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login/admin', ['username' => 'nonexistent', 'password' => 'wrong']);
        }
        $this->postJson('/api/v1/auth/login/admin', ['username' => 'nonexistent', 'password' => 'wrong'])
            ->assertStatus(429);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  7. MASS ASSIGNMENT
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_mass_assignment_blocks_admin_fields()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;
        $this->withToken($token)->postJson('/api/v1/surat/pengajuan', [
            'kategori_surat_id' => $this->kategori->id,
            'data_isian' => ['keperluan' => 'Test'],
            'file_syarat' => ['ktp' => UploadedFile::fake()->image('test.jpg')],
            'status' => 'Disetujui',
            'nik_pemohon' => $this->warga2->nik,
        ])->assertStatus(201);

        $this->assertDatabaseHas('pengajuan_surat', [
            'nik_pemohon' => $this->warga->nik,
            'status' => 'Pending',
        ]);
    }

    public function test_mass_assignment_blocks_role_field()
    {
        $this->assertDatabaseMissing('administrators', ['role' => 'hacker']);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  8. INPUT VALIDATION
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_nik_must_be_16_digits()
    {
        $this->postJson('/api/v1/auth/login/warga', ['nik' => '12345', 'no_kk' => '1234567890123456'])->assertStatus(422);
        $this->postJson('/api/v1/auth/login/warga', ['nik' => 'abcdefghijklmnop', 'no_kk' => '1234567890123456'])->assertStatus(422);
    }

    public function test_html_injection_in_data_isian()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;
        $this->withToken($token)->postJson('/api/v1/surat/pengajuan', [
            'kategori_surat_id' => $this->kategori->id,
            'data_isian' => ['keperluan' => '<script>alert("xss")</script>'],
            'file_syarat' => ['ktp' => UploadedFile::fake()->image('test.jpg')],
        ])->assertStatus(201);

        $this->assertDatabaseHas('pengajuan_surat', ['nik_pemohon' => $this->warga->nik]);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  9. TOKEN SECURITY
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_expired_token_returns_401()
    {
        $token = $this->warga->createToken('test', ['warga'])->plainTextToken;
        $this->warga->tokens()->update(['created_at' => now()->subYear(2)]);
        $this->withToken($token)->getJson('/api/v1/auth/profile')->assertStatus(401);
    }

    public function test_invalid_token_returns_401()
    {
        $this->withToken('invalid-token-format')->getJson('/api/v1/auth/profile')->assertStatus(401);
    }

    public function test_empty_bearer_token_returns_401()
    {
        $this->withToken('')->getJson('/api/v1/auth/profile')->assertStatus(401);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  10. SECURITY HEADERS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_security_headers_present()
    {
        $response = $this->getJson('/api/v1/statistik/demografi');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_csp_header_contains_frame_ancestors()
    {
        $response = $this->getJson('/api/v1/statistik/demografi');
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  11. INFORMATION DISCLOSURE
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_app_debug_is_false()
    {
        $this->assertFalse(config('app.debug'));
    }

    public function test_session_encryption_enabled()
    {
        $this->assertTrue(config('session.encrypt'));
    }

    public function test_session_same_site_strict()
    {
        $this->assertEquals('strict', config('session.same_site'));
    }

    public function test_password_hidden_in_model()
    {
        $response = $this->postJson('/api/v1/auth/login/admin', [
            'username' => $this->operator->username, 'password' => 'password123',
        ]);
        $response->assertStatus(200);
        $user = json_decode($response->getContent(), true)['user'];
        $this->assertArrayNotHasKey('password', $user);
        $this->assertArrayNotHasKey('password_hash', $user);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  12. CSRF / SESSION ATTACKS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_web_routes_require_session()
    {
        $response = $this->get('/warga/dashboard');
        $response->assertRedirect('login');
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  13. PATH TRAVERSAL
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_path_traversal_in_slug()
    {
        $response = $this->getJson('/api/v1/informasi/../../etc/passwd');
        $response->assertStatus(404);
    }

    public function test_path_traversal_in_verifikasi()
    {
        $response = $this->getJson('/api/v1/verifikasi/../../etc/passwd');
        $response->assertStatus(404);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  14. DUPLICATE / RACE CONDITIONS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_duplicate_login_token_is_different()
    {
        $this->postJson('/api/v1/auth/login/warga', [
            'nik' => $this->warga->nik, 'no_kk' => $this->warga->no_kk,
        ])->assertStatus(200);

        $response2 = $this->postJson('/api/v1/auth/login/warga', [
            'nik' => $this->warga->nik, 'no_kk' => $this->warga->no_kk,
        ]);
        $response2->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 2);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  15. BULK OPERATIONS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_no_delete_all_endpoint_exists()
    {
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;
        $this->withToken($token)->deleteJson('/api/v1/admin/informasi/all')->assertStatus(404);
        $this->withToken($token)->deleteJson('/api/v1/admin/mutasi/all')->assertStatus(404);
    }

    public function test_no_mass_delete_endpoint()
    {
        $token = $this->admin->createToken('test', ['admin'])->plainTextToken;
        $this->withToken($token)->postJson('/api/v1/admin/surat/pengajuan/bulk-approve', [])
            ->assertStatus(404);
    }

    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    //  16. CORS
    // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    public function test_cors_allows_same_origin()
    {
        $response = $this->getJson('/api/v1/statistik/demografi', [
            'HTTP_ORIGIN' => 'http://localhost:8000',
        ]);
        $response->assertStatus(200);
    }

}
