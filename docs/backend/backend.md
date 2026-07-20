# Dokumentasi Backend - AvaraDesa

Dokumentasi ini merinci struktur arsitektur, spesifikasi teknis, serta panduan pengujian untuk sisi server (backend) AvaraDesa.

---

## 1. Tumpukan Teknologi Backend
* **Runtime**: PHP 8.3+ (Strictly Typed)
* **Framework**: Laravel 13.x (API Engine & Antrean Job)
* **AI SDK**: Laravel AI (`^0.9.1`) dengan 15+ provider driver
* **Cloud SDK**: AWS SDK PHP (`^3.388`)
* **API Docs**: Scribe (`^5.10`)
* **Admin Backoffice**: Filament PHP v5.6.5
* **Cache & Antrean**: Array/Redis
* **Document Processor**: Barryvdh DomPDF (`^3.0`) & Simple QrCode (`^4.2`)

---

## 2. Struktur Direktori Utama
* `app/Http/Controllers/Api/`: Controller yang menangani request/response 50+ endpoint API (`routes/api.php`).
* `app/Services/`: Lapisan logika bisnis terisolasi (TelegramService, AiService, StatistikService, dll).
* `app/Services/AiProviders/`: 6 kelas provider AI (GeminiProvider, OpenAiProvider, DeepSeekProvider, OllamaProvider, BedrockProvider, BaseAiProvider).
* `app/Jobs/`: Eksekusi tugas asinkronus (Pembuatan PDF Surat & Broadcast Telegram).
* `app/Models/`: Definisi entitas model Eloquent beserta relasi database.

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
* **Feature Testing** (17 file): Memvalidasi siklus hidup otentikasi, CRUD informasi publik, pengajuan surat, mutasi penduduk, sinkronisasi, webhook Telegram, audit log, performa halaman frontend, simulasi alur end-to-end warga-admin, dan keamanan (SQL injection, XSS, path traversal, rate limiting, mass assignment, CORS).
* **Cakupan**: ±270 test methods, ±400 assertions, 0 failures (per konstribusi terbaru).
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

### 5.3. Caching AI (Exact Match + Semantic Cache)

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

## 6. Fitur Pemantauan Server & Lalu Lintas (Traffic)

Untuk membantu administrator desa dalam memantau kesehatan server secara real-time dan melacak aktivitas kunjungan warga, backend AvaraDesa dilengkapi dengan sistem pemantauan terintegrasi:

### 6.1. Middleware Pemantau Lalu Lintas (`TrackTraffic`)
* Terdaftar secara global dalam grup middleware `web` di `bootstrap/app.php`.
* Memfilter request internal admin (`/admin*`), request AJAX Livewire (`/livewire*`), dan request telemetri agar data lalu lintas murni berasal dari warga/publik.
* Menganalisis *User Agent* secara dinamis untuk mengidentifikasi apakah request berasal dari bot/crawler mesin pencari.
* Menyimpan log kunjungan ke dalam tabel `traffic_logs` (IP address, user agent, URL path, HTTP method, referer, dan status bot).

### 6.2. Widget Pemantau Performa Server (`ServerPerformanceWidget`)
* Membaca kapasitas ruang penyimpanan (disk space) secara real-time menggunakan fungsi `disk_free_space` & `disk_total_space`.
* Mendeteksi penggunaan memori (RAM) fisik server. Mendukung pembacaan Windows OS menggunakan perintah `wmic` dan Linux OS dengan mem-parsing berkas `/proc/meminfo`.
* Menampilkan informasi sistem operasi, versi PHP, versi Laravel, dan alamat IP server secara dinamis pada dasbor backoffice admin.

### 6.3. Widget Dasbor Kustom Lainnya
* **`TrafficChartWidget`**: Menyajikan grafik garis (line chart) interaktif 7 hari terakhir yang menghitung statistik kunjungan harian unik warga (mengecualikan search engine bot).
* **`RecentSubmissionsWidget`**: Menampilkan tabel 5 permohonan pengajuan surat terbaru dari warga secara langsung di dasbor backoffice dengan badge warna-warni dinamis (Pending, Proses, Selesai, Ditolak).
