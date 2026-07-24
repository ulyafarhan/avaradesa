# Dokumentasi Backend - AvaraDesa

Dokumentasi ini merinci struktur arsitektur, spesifikasi teknis, serta panduan pengujian untuk sisi server (backend) AvaraDesa.

---

## 1. Tumpukan Teknologi Backend
* **Runtime**: PHP 8.3+ (Strictly Typed)
* **Framework**: Laravel 13.x (API Engine & Antrean Job)
* **AI SDK**: Laravel AI (`^0.9.1`) dengan 15+ provider driver
* **Cloud SDK**: AWS SDK PHP (`^3.388`)
* **Admin Backoffice**: Filament PHP v5.6.5
* **Cache & Antrean**: Array/Redis
* **Document Processor**: Barryvdh DomPDF (`^3.0`) & Simple QrCode (`^4.2`)
* **Audit & Activity Log**: spatie/laravel-activitylog (tabel `activity_log`)
* **WhatsApp Gateway**: Multi-provider (wa-gateway self-hosted + Fonnte cloud)

---

## 2. Struktur Direktori Utama
* `app/Console/Commands/`: Perintah Artisan terjadwal (`system:cleanup`, `log:migrate-from-audit`, `telegram:setup-webhook`).
* `app/Http/Controllers/Api/`: Controller yang menangani request/response 50+ endpoint API (`routes/api.php`).
* `app/Services/`: Lapisan logika bisnis terisolasi (SystemLogger, WhatsAppService, TelegramService, AiService, StatistikService, dll).
* `app/Services/AiProviders/`: 6 kelas provider AI (GeminiProvider, OpenAiProvider, DeepSeekProvider, OllamaProvider, BedrockProvider, BaseAiProvider).
* `app/Jobs/`: Eksekusi tugas asinkronus (Pembuatan PDF Surat & Broadcast Telegram).
* `app/Models/`: Definisi entitas model Eloquent beserta relasi database.
* `app/Filament/Resources/`: Resources Filament untuk panel admin (ActivityLogResource, AspirasiResource, dll).

---

## 3. Panduan Instalasi Lokal
1. Pastikan Composer, PHP 8.3+, MySQL/MariaDB, dan Redis sudah aktif di server lokal Anda.
2. Jalankan perintah instalasi dependensi backend:
   ```bash
   composer install
   ```
3. Salin file konfigurasi lingkungan:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Konfigurasikan kredensial database di `.env`, lalu jalankan migrasi beserta pengisian data awal:
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   ```
5. Build aset frontend untuk produksi:
   ```bash
   npm install && npm run build
   ```
6. Setup Webhook Bot Telegram:
   ```bash
   php artisan telegram:setup-webhook
   ```
7. Jalankan Server Backend & Queue Worker:
   * Server: `php artisan serve`
   * Queue Worker: `php artisan queue:work`

---

## 4. Pengujian & Penjaminan Kualitas (QA)
Sistem backend dilengkapi dengan 40 file test otomatis berbasis PHPUnit 12:
* **Unit Testing** (23 file): Memvalidasi model, policy, request, job, service, dan provider AI.
* **Feature Testing** (17 file): Memvalidasi siklus hidup otentikasi, CRUD informasi publik, pengajuan surat, mutasi penduduk, sinkronisasi, webhook Telegram & WhatsApp, audit log, performa halaman frontend, simulasi alur end-to-end warga-admin, dan keamanan (SQL injection, XSS, path traversal, rate limiting, mass assignment, CORS).
* **Cakupan**: ±270 test methods, ±400 assertions, 0 failures (per konstribusi terbaru).
* **Catatan**: Test untuk endpoint baru (gateway sync, WhatsApp notification, phone auto-format) belum ditambahkan — lihat todo di `docs/api/api.md`.
* **Benchmark**: API demografi 107ms/5query, API layanan 21ms/4query.

### Cara Menjalankan Tes:
```bash
composer run test
```

### Optimasi Query (Bebas N+1 Query)
* Eager loading di 6 titik (Widget, Resource, Controller, Job) untuk menumpas N+1 query.
* **AuditLogResource** menggunakan batched lookup (single query untuk seluruh baris per halaman) alih-alih lazy loading per baris.
* **AdminStatsOverview** menerapkan cache 5 menit untuk agregat dashboard, menghemat puluhan query tiap render.
* 19 + 9 database indexes baru ditambahkan pada kolom yang sering difilter (`status`, `tanggal`, `jenis_kelamin`, `kode_surat`, dll).
* **Catatan**: PengaturanDesa saat ini belum menggunakan cache all-in-one — setiap pemanggilan `PengaturanDesa::get()` menghasilkan query terpisah (15+ query per request di `HandleInertiaRequests` — lihat isu #TODO).

## 5. Arsitektur AI & Optimasi Performa

Sistem AI AvaraDesa menggunakan arsitektur **dual-layer** yang menggabungkan Laravel AI SDK dengan class provider kustom untuk menangani berbagai kebutuhan: chatbot warga, generate SEO metadata, dan copywriting konten.

### 5.1. Dual-Layer Architecture AI

**Layer 1 — Laravel AI SDK (`laravel/ai` ^0.9.1)**
SDK resmi Laravel yang menyediakan antarmuka seragam untuk 15+ provider driver:
`anthropic`, `azure`, `bedrock`, `cohere`, `deepseek`, `gemini`, `groq`, `jina`, `mistral`, `ollama`, `openai`, `openai-compatible`, `openrouter`, `voyageai`, `xai`.

Digunakan untuk integrasi chatbot, embedding vektor, dan generasi konten ringan.

**Layer 2 — Custom AiProviders (6 file)**
Kelas provider kustom di `app/Services/AiProviders/` untuk kontrol granular dan fallback cerdas:
| File | Provider | Fungsi Utama |
|------|----------|-------------|
| `BaseAiProvider.php` | — | Abstract class dengan method bantu (call API, parse response, hitung token) |
| `GeminiProvider.php` | Google Gemini | Generate SEO metadata, copywriting, dan chatbot |
| `OpenAiProvider.php` | OpenAI (GPT) | Generate SEO, copywriting, dan jawaban chatbot |
| `DeepSeekProvider.php` | DeepSeek | Alternatif hemat biaya untuk chatbot |
| `OllamaProvider.php` | Ollama (lokal) | Inference offline tanpa koneksi internet |
| `BedrockProvider.php` | AWS Bedrock | Integrated via AWS SDK, untuk deployment enterprise |

### 5.2. Mekanisme Multi-AI Fallback & Prioritas Dinamis

Failover berantai diimplementasikan oleh `FallbackAiService` yang membungkus `AiProviderInterface`:
* **Penyimpanan Konfigurasi**: Rantai penyedia cadangan disimpan dalam format JSON (`ai_providers_list`) di tabel `pengaturan_desa`.
* **Pengurutan Prioritas**: Setiap penyedia punya kolom `priority` — sistem memanggil dari prioritas terkecil ke terbesar.
* **resolveProviderInstance()**: Mendukung 2 provider aktif saat ini (Gemini dan OpenAI) dengan fallback otomatis jika salah satu gagal (HTTP 429/401/timeout).
* **Kompatibilitas Mundur**: Jika konfigurasi dinamis kosong, sistem fallback ke kredensial `.env` (Gemini atau OpenAI).

### 5.4. WhatsApp Multi-Provider Service

**`WhatsAppService.php`** — Mendukung dua provider dengan switching otomatis:

| Provider | Tipe | Config Key | Endpoint |
|----------|------|-----------|----------|
| wa-gateway | Self-hosted (Baileys) | `wa-gateway` | `http://localhost:2785/api/...` |
| fonnte | Cloud Fonnte | `fonnte` | `https://api.fonnte.com/send` |

**Konfigurasi dinamis** (dibaca dari `pengaturan_desa` → fallback ke `config/services.php`):
- `wa_provider`: `wa-gateway` atau `fonnte`
- `wa_gateway_url`: URL gateway (default: `http://localhost:2785`)
- `wa_api_key`: API key untuk wa-gateway
- `wa_session_id`: ID sesi Baileys (default: `default`)
- `wa_fonnte_token`: Token Fonnte

**Fitur utama:**
- `sendMessage()`: Kirim teks dengan auto-provider switching
- `sendImage()`: Kirim gambar + caption
- `broadcast()`: Kirim ke banyak nomor berurutan (100ms delay)
- `notifyPengajuanStatus()`: Notifikasi status surat via WhatsApp
- `notifyMutasiStatus()`: Notifikasi status mutasi via WhatsApp
- `checkHealth()`: Health check gateway status
- `getQrCode()`: Ambil QR code dari gateway

**Normalisasi nomor**: `08xxx` → `628xxx` → `628xxx@s.whatsapp.net`

**Notifikasi otomatis** terhubung ke:
- `PengajuanSuratController` (store, approve, reject)
- `PengajuanSuratService` (approve, reject)
- `GenerateSuratPdfJob` (after PDF generation)
- `MutasiPendudukController` (approve, reject)

**Template notifikasi** — 12 template yang dapat diedit admin via PengaturanSistem:
- `notif_telegram_surat_pending/diproses/disetujui/ditolak/selesai`
- `notif_telegram_mutasi_disetujui/ditolak`
- `notif_wa_surat_pending/diproses/disetujui/ditolak/selesai`
- `notif_wa_mutasi_disetujui/ditolak`

**Gateway Sync** — Endpoint `GET /api/v1/gateway/sync` dikirim wa-gateway tiap 5 menit:
- Return FAQ + Knowledge Base (BotKnowledge)
- Return Kategori Surat (syarat dokumen)
- Return Template Notifikasi (pengaturan_desa)
- Auth: `X-API-Key` header

### 5.5. Notifikasi Telegram + WhatsApp

Sistem notifikasi dual-channel mengirim status perubahan ke warga:

| Event | Channel | Method |
|-------|---------|--------|
| Pengajuan surat dibuat | Telegram + WhatsApp | `notifyPengajuanStatus()` |
| Pengajuan surat disetujui | Telegram + WhatsApp | `notifyPengajuanStatus()` |
| Pengajuan surat ditolak | Telegram + WhatsApp | `notifyPengajuanStatus()` |
| PDF surat selesai generate | Telegram (dokumen) + WhatsApp (pesan) | `GenerateSuratPdfJob` |
| Mutasi disetujui | Telegram + WhatsApp | `notifyMutasiStatus()` |
| Mutasi ditolak | Telegram + WhatsApp | `notifyMutasiStatus()` |

**Template engine**: Cari dari `pengaturan_desa` (key: `notif_*`) → fallback ke hardcoded defaults.

### 5.6. Caching AI (Exact Match + Semantic Cache)

Untuk menghemat token API dan mempercepat respons chatbot (<2ms jika cache hit):
1. **Normalisasi**: Pesan dipangkas spasi, di-lowercase.
2. **Exact Match**: Cek Redis `ai_exact_[md5(pesan)]` → jika tidak ada, cek tabel `chatbot_logs` 24 jam terakhir. Jika cocok, cache ke Redis 24 jam.
3. **Semantic Cache**: Load 100 log unik 48 jam terakhir dari Redis `ai_recent_logs_semantic` (5 menit TTL).
   * **Tokenisasi & Stopwords**: Buang non-alfanumerik dan stopwords bahasa Indonesia.
   * **Jaccard Similarity**: Rasio irisan token / gabungan token.
   * **Levenshtein Distance**: Untuk pesan <20 karakter.
   * **Threshold ≥80%**: Kembalikan jawaban ter-cache dengan zero token cost.
   * **<80%**: Teruskan ke API provider.

---

## 6. Audit & Activity Logging

AvaraDesa menggunakan **spatie/laravel-activitylog** sebagai sistem audit terpusat, menggantikan tabel `audit_logs` legacy secara bertahap.

### 6.1. SystemLogger (Wrapper)

`app/Services/SystemLogger.php` — Facade-style wrapper yang menyederhanakan logging aktivitas:

```php
SystemLogger::log(
    event: 'aspirasi.kirim',
    description: 'Aspirasi dari publik',
    subject: $penduduk,          // optional, model object
    properties: ['pesan' => $request->pesan, 'ip' => request()->ip()],
    logName: 'system'            // default: 'system'
);
```

**Fitur:**
- Otomatis mendeteksi guard (`admin` atau `web`) untuk mengisi `causer`
- Merekam IP address dan user agent secara otomatis
- Subject binding untuk relasi morphs ke model terkait
- Digunakan di: `bootstrap/app.php` (exception handler), `WhatsAppWebhookController`, `TelegramWebhookController`, `SystemCleanupCommand`, `PublicPortalController` (aspirasi)

### 6.2. ActivityLogResource (Filament — Read-Only)

`app/Filament/Resources/ActivityLogResource.php` — Panel admin untuk melihat log aktivitas sistem.

**Detail:**
- Model: `Spatie\Activitylog\Models\Activity`
- **Read-only**: `canCreate()` return `false`
- Navigasi: Grup **Pengaturan**, label **Log Aktivitas**, sort 13
- Kolom tabel: Waktu, Jenis (badge berwarna), Deskripsi, Pelaku, IP
- Filter: dropdown event dan causer_type (dinamis dari DB)
- Detail form: menampilkan waktu, event, deskripsi, pelaku (dengan tipe), target subject, IP, user agent, dan diff properties (tabel perbandingan data lama & baru)

### 6.3. AspirasiResource (Filament — Read-Only)

`app/Filament/Resources/AspirasiResource.php` — Panel admin untuk menampilkan aspirasi warga yang masuk.

**Detail:**
- Model: `App\Models\AuditLog` (tabel `audit_logs`) — difilter dengan `where('tindakan', 'aspirasi')`
- **Read-only**: `canCreate()` return `false`
- Navigasi: Grup **Layanan Warga**, label **Aspirasi Warga**, sort 3
- Kolom tabel: Waktu, Pesan Aspirasi, IP
- Searchable pada kolom pesan

### 6.4. Perintah Terjadwal

| Command | Signature | Fungsi |
|---------|-----------|--------|
| System Cleanup | `system:cleanup` | Hapus log >30hr, audit & activity >90hr, prune token expired & failed jobs |
| Migrate Audit Logs | `log:migrate-from-audit` | Migrasi data dari `audit_logs` (legacy) ke `activity_log` (spatie). Opsi `--dry-run` untuk dry run |
| Telegram Webhook | `telegram:setup-webhook` | Setup webhook bot Telegram |

### 6.5. Auth Redirect Fix

di `bootstrap/app.php`:
```php
$guard = !empty($e->guards()) ? $e->guards()[0] : 
    (str_starts_with($request->path(), 'admin/') ? 'admin' : null);
if ($guard === 'admin') {
    return redirect()->guest('/admin/login');
}
return redirect()->guest(route('login'));
```
- Deteksi guard dari request path, bukan dari exception guard kosong
- Admin → redirect ke `/admin/login`, warga → redirect ke halaman login warga

---

## 7. Fitur Pemantauan Server & Lalu Lintas (Traffic)

Untuk membantu administrator desa dalam memantau kesehatan server secara real-time dan melacak aktivitas kunjungan warga, backend AvaraDesa dilengkapi dengan sistem pemantauan terintegrasi:

### 7.1. Middleware Pemantau Lalu Lintas (`TrackTraffic`)
* Terdaftar secara global dalam grup middleware `web` di `bootstrap/app.php`.
* Memfilter request internal admin (`/admin*`), request AJAX Livewire (`/livewire*`), dan request telemetri agar data lalu lintas murni berasal dari warga/publik.
* Menganalisis *User Agent* secara dinamis untuk mengidentifikasi apakah request berasal dari bot/crawler mesin pencari.
* Menyimpan log kunjungan ke dalam tabel `traffic_logs` (IP address, user agent, URL path, HTTP method, referer, dan status bot).

### 7.2. Widget Pemantau Performa Server (`ServerPerformanceWidget`)
* Membaca kapasitas ruang penyimpanan (disk space) secara real-time menggunakan fungsi `disk_free_space` & `disk_total_space`.
* Mendeteksi penggunaan memori (RAM) fisik server. Mendukung pembacaan Windows OS menggunakan perintah `wmic` dan Linux OS dengan mem-parsing berkas `/proc/meminfo`.
* Menampilkan informasi sistem operasi, versi PHP, versi Laravel, dan alamat IP server secara dinamis pada dasbor backoffice admin.

### 7.3. Widget Dasbor Kustom Lainnya
* **`TrafficChartWidget`**: Menyajikan grafik garis (line chart) interaktif 7 hari terakhir yang menghitung statistik kunjungan harian unik warga (mengecualikan search engine bot).
* **`RecentSubmissionsWidget`**: Menampilkan tabel 5 permohonan pengajuan surat terbaru dari warga secara langsung di dasbor backoffice dengan badge warna-warni dinamis (Pending, Proses, Selesai, Ditolak).
