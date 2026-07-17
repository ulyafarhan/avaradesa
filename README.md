# AvaraDesa — Sistem Informasi Desa Terpadu

> **Platform Digital Administrasi Desa Berbasis Cloud dengan Integrasi Multi-AI, Aplikasi Mobile Flutter, dan Arsitektur Production-Grade**

---

## Daftar Isi

- [1. Pendahuluan](#1-pendahuluan)
- [2. Spesifikasi Teknis](#2-spesifikasi-teknis)
- [3. Arsitektur Sistem](#3-arsitektur-sistem)
- [4. Instalasi dan Setup](#4-instalasi-dan-setup)
- [5. Struktur Proyek](#5-struktur-proyek)
- [6. Skema Database](#6-skema-database)
- [7. Spesifikasi API](#7-spesifikasi-api)
- [8. Integrasi AI dan Telegram](#8-integrasi-ai-dan-telegram)
- [9. Keamanan Sistem](#9-keamanan-sistem)
- [10. Aplikasi Mobile Flutter](#10-aplikasi-mobile-flutter)
- [11. Docker dan DevOps](#11-docker-dan-devops)
- [12. Pengujian](#12-pengujian)
- [13. Peta Jalan](#13-peta-jalan)
- [14. Kontribusi](#14-kontribusi)
- [15. Lisensi](#15-lisensi)

---

## 1. Pendahuluan

### 1.1 Tentang AvaraDesa

AvaraDesa merupakan platform sistem informasi desa terpadu yang mendigitalisasi seluruh tata kelola administrasi pada tingkat Desa. Melalui pendekatan *self-service*, beban kerja administratif yang sebelumnya berpusat pada aparatur desa dialihkan secara aman kepada partisipasi aktif masyarakat melalui portal layanan mandiri berbasis web (PWA) dan aplikasi mobile native (Flutter).

### 1.2 Visi dan Misi

**Visi**: Mewujudkan ekosistem administrasi desa yang transparan, akuntabel, dan efisien berbasis teknologi cloud guna mengoptimalkan kualitas pelayanan publik.

**Misi**:
- Digitalisasi menyeluruh terhadap administrasi kependudukan dan pencatatan sipil
- Penyediaan sistem pengajuan surat mandiri untuk memangkas birokrasi fisik
- Implementasi kecerdasan buatan (AI) multi-provider untuk asisten virtual pelayanan 24 jam
- Penjaminan keamanan data sensitif warga melalui audit trail, kriptografi SHA-256, dan security headers berlapis
- Penyediaan aplikasi mobile native untuk aksesibilitas warga di daerah terpencil

### 1.3 Target Pengguna

| Peran | Deskripsi |
|:------|:----------|
| **Warga Desa** | Masyarakat yang membutuhkan layanan administrasi desa secara daring via PWA atau aplikasi mobile |
| **Kepala Desa** | Pejabat tertinggi desa yang memverifikasi dan menandatangani dokumen surat |
| **Sekretaris Desa** | Mengelola data kependudukan, informasi publik, dan konfigurasi sistem |
| **Operator** | Staf yang memproses pengajuan surat dan mutasi kependudukan |
| **Bot Telegram** | Asisten virtual berbasis AI yang melayani pertanyaan warga secara otomatis 24/7 |

---

## 2. Spesifikasi Teknis

### 2.1 Stack Backend (Server)

| Komponen | Versi | Keterangan |
|:---------|:------|:-----------|
| PHP | ^8.3 | Runtime dengan optimasi strictly typed |
| Laravel Framework | ^13.8 | Mesin API, manajemen antrean, dan sesi |
| Filament PHP | 5.6.5 | Panel administrasi untuk Kepala Desa, Sekdes, dan Operator |
| Laravel Sanctum | ^4.3 | Otentikasi token berbasis SHA-256 (expiry 7 hari) |
| MySQL / MariaDB | 8.0+ / 10.6+ | Database produksi (SQLite untuk testing) |
| Redis | 6+ | Caching, queue manager, dan session driver |
| Barryvdh DomPDF | ^3.0 | Generator dokumen administrasi format PDF |
| Simple QR Code | ^4.2 | Tanda tangan elektronik (TTE) berbasis hash SHA-256 |
| AWS SDK PHP | ^3.388 | Integrasi AWS Bedrock untuk AI provider |
| Scribe | ^5.10 | Dokumentasi API interaktif |

### 2.2 Stack Frontend (Klien Web)

| Komponen | Versi | Keterangan |
|:---------|:------|:-----------|
| Vue 3 | ^3.5.38 | Framework UI dengan Composition API |
| Inertia.js | ^3.4.0 | Konektor SPA tanpa overhead API REST eksternal |
| Tailwind CSS | ^4.3.0 | Utility-first CSS framework |
| Vite | ^8.0.16 | Build tool dengan plugin Laravel |
| DOMPurify | ^3.4.11 | Sanitasi XSS pada konten HTML dinamis |
| SweetAlert2 | ^11.26.25 | Modal dan notifikasi interaktif |
| ESLint | ^10.6.0 | Linter kode JavaScript/Vue |

### 2.3 Stack Mobile (Flutter)

| Komponen | Versi | Keterangan |
|:---------|:------|:-----------|
| Dart SDK | ^3.11.0 | Runtime Flutter |
| flutter_riverpod | ^2.6.1 | State management reaktif |
| go_router | ^14.8.1 | Navigasi deklaratif dengan route guard |
| dio | ^5.7.0 | HTTP client dengan interceptor |
| drift | ^2.22.1 | SQLite ORM untuk database lokal |
| flutter_secure_storage | ^9.2.4 | Penyimpanan token terenkripsi |
| fl_chart | ^0.69.2 | Visualisasi statistik (pie & bar chart) |

### 2.4 Stack Pengujian

| Komponen | Versi | Keterangan |
|:---------|:------|:-----------|
| PHPUnit | ^12.5.12 | 261 test methods backend (unit + feature + E2E) |
| Vitest | ^4.1.8 | 36 test methods frontend Vue |
| @vue/test-utils | ^2.4.11 | Utilitas pengujian komponen Vue |
| @vitest/coverage-v8 | ^4.1.8 | Laporan cakupan pengujian |

---

## 3. Arsitektur Sistem

### 3.1 Diagram Arsitektur

Sistem dibangun menggunakan arsitektur monolit modern 4 layer yang menggabungkan backend, frontend web, aplikasi mobile native, dan integrasi layanan eksternal dalam satu repositori terpadu.

```
┌─────────────────────────────────────────────────────────────────┐
│                    Frontend Client Layer                         │
├─────────────────────────────────────────────────────────────────┤
│  Portal Publik & PWA Warga (Inertia.js + Vue 3 + Tailwind v4)  │
│  Aplikasi Mobile (Flutter + Riverpod + GoRouter + Drift)        │
│  Build Engine: Vite 8 (Web) / Flutter SDK 3.11 (Mobile)        │
│  Sanitasi: DOMPurify (Web) / Debounce Composable (Search)      │
└──────────────────────────┬──────────────────────────────────────┘
                           │ (Protokol Inertia / REST API)
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│           Backend & Admin Platform (Laravel 13)                  │
├─────────────────────────────────────────────────────────────────┤
│  Dashboard Admin & Operator (Filament PHP v5)                    │
│  RESTful API Engine (Sanctum Auth, 29 Endpoint, 20 Web Routes)  │
│  Document Processor (DomPDF + QR Code SHA-256)                   │
│  Security Layer (CSP, HSTS, Rate Limiting, Audit Trail)          │
│  5 Authorization Policies (RBAC per Model)                       │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                  Data & External Service Layer                    │
├─────────────────────────────────────────────────────────────────┤
│  Database: MySQL 8.0+ / MariaDB (19 tabel + 7 referensi)       │
│  Cache & Queue: Redis 6+ (session, cache, queue worker)         │
│  Cloud Storage: S3 / Cloudflare R2 (auto-switch)                │
│  AI Multi-Provider (5):                                          │
│    ├── OpenAI (GPT-4o-mini)                                      │
│    ├── Google Gemini (gemini-pro)                                 │
│    ├── DeepSeek (deepseek-v4-flash)                              │
│    ├── AWS Bedrock (Claude/Titan)                                 │
│    └── Ollama (lokal: Llama, Mistral, dll)                       │
│  Notifikasi & Bot: Telegram Bot API (Webhook + RAG)              │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Penjelasan Layer

- **Frontend Client Layer**: Menangani antarmuka pengguna melalui dua kanal: PWA/SPA berbasis Vue 3 + Inertia.js untuk web, dan aplikasi Flutter native untuk Android/Windows. Dilengkapi DOMPurify untuk sanitasi XSS dan debounce composable untuk efisiensi search.
- **Backend & Admin Platform**: Mesin pemrosesan utama berbasis Laravel 13 dengan panel Filament v5. Menyediakan REST API dengan Sanctum auth, 5 authorization policies, security headers berlapis (CSP, HSTS, X-Frame-Options), dan rate limiting (60 req/min).
- **Data & External Service Layer**: Penyimpanan data via MySQL dengan 19+ tabel relasional, caching via Redis, cloud storage S3/R2, integrasi 5 AI provider dengan fallback chain otomatis, dan notifikasi via Telegram Bot API.

---

## 4. Instalasi dan Setup

### 4.1 Persyaratan Sistem

| Komponen | Versi Minimum |
|:---------|:-------------|
| PHP | 8.3+ (Extensions: pdo_mysql, mbstring, exif, pcntl, bcmath, gd, zip, redis) |
| Node.js | 20+ |
| MySQL / MariaDB | 8.0+ / 10.6+ |
| Redis Server | 6+ (direkomendasikan) |
| Composer | 2.x |
| Flutter SDK | 3.11+ (untuk mobile) |

### 4.2 Instalasi Lokal (Development)

**1. Clone Repositori**

```bash
git clone <repository-url> avaradesa
cd avaradesa
```

**2. Instal Dependensi**

```bash
composer install
npm install
```

**3. Konfigurasi Environment**

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi pada `.env`:

```env
DB_CONNECTION=mysql
DB_DATABASE=avaradesa
DB_USERNAME=root
DB_PASSWORD=

TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_WEBHOOK_SECRET=your_webhook_secret

# AI Provider (pilih salah satu atau semua)
AI_PROVIDER=openai
OPENAI_API_KEY=your_key
GEMINI_API_KEY=your_key
DEEPSEEK_API_KEY=your_key
OLLAMA_BASE_URL=http://localhost:11434/v1
```

**4. Migrasi Database & Seeding**

```bash
php artisan migrate --seed
php artisan storage:link
```

**5. Jalankan Aplikasi**

```bash
php artisan serve
npm run dev
php artisan queue:work  # opsional
```

Aplikasi dapat diakses di `http://localhost:8000`.

**Kredensial Default:**
| Role | Username | Password |
|:-----|:---------|:---------|
| Kepala Desa | `kepala_desa` | (lihat `ADMIN_DEFAULT_PASSWORD` di .env) |
| Sekretaris Desa | `sekdes` | (sama) |
| Operator | `operator` | (sama) |

---

## 5. Struktur Proyek

```
avaradesa/
├── app/
│   ├── Filament/                  # Panel administrasi Filament PHP
│   ├── Http/
│   │   ├── Controllers/Api/       # Controller API RESTful (7 controller)
│   │   ├── Controllers/Web/       # Controller web Inertia (5 controller)
│   │   ├── Middleware/            # Security headers, cache, traffic, Inertia
│   │   ├── Requests/             # Form Request validasi (Api/ + Web/)
│   │   └── Resources/            # API Resource transformer (7 resource)
│   ├── Jobs/                      # Job antrean (PDF, Telegram, Broadcast)
│   ├── Models/                    # 17 model Eloquent + 7 referensi
│   ├── Policies/                  # 5 authorization policies (RBAC)
│   ├── Providers/                 # Service provider (AI, auth, storage)
│   └── Services/
│       ├── AiProviders/           # 5 AI provider + BaseAiProvider
│       ├── Contracts/             # AiProviderInterface
│       ├── FallbackAiService.php  # Fallback chain multi-provider
│       ├── TelegramService.php    # Telegram Bot API wrapper
│       ├── PdfGeneratorService.php# Generator PDF + QR Code
│       ├── StatistikService.php   # Agregasi statistik kependudukan
│       └── ImageService.php       # Kompresi gambar ke WebP
├── config/                        # Konfigurasi Laravel (sanctum, services, dll)
├── database/
│   ├── factories/                 # 10 factory untuk testing
│   ├── migrations/                # 34 migration file
│   └── seeders/                   # 7 seeder (data awal)
├── lib/                           # Aplikasi Flutter Mobile
│   ├── core/                      # API client, database, theme, sync
│   ├── features/                  # auth, home, surat, mutasi, admin, profil
│   ├── router/                    # GoRouter dengan auth guard
│   └── shared/                    # Constants, widgets
├── resources/
│   ├── css/                       # Tailwind CSS dengan design tokens
│   ├── js/
│   │   ├── Components/            # 10 komponen Vue reusable
│   │   ├── Composables/           # useDebounce (400ms)
│   │   ├── Layouts/               # CitizenLayout, PublicLayout
│   │   ├── Pages/                 # 18 halaman Vue (Auth, Citizen, Public)
│   │   └── Utils/                 # sanitize, formatters, imageCompressor
│   └── views/                     # Blade templates (app, PDF surat)
├── tests/
│   ├── Feature/                   # 16 file feature test
│   └── Unit/                      # 24 file unit test
├── .docker/                       # Konfigurasi Nginx + Supervisor
├── .github/workflows/             # CI/CD pipeline (Laravel + Vue)
├── Dockerfile                     # PHP-FPM 8.3 + Nginx + Supervisor
├── docker-compose.yml             # 3 services (app + db + redis)
└── DEPLOY.md                      # Panduan deployment produksi (1GB RAM)
```

---

## 6. Skema Database

Sistem didukung oleh **19 tabel relasional utama** dan **7 tabel referensi** yang saling terintegrasi:

### 6.1 Tabel Utama

| Tabel | Deskripsi | Relasi Kunci |
|:------|:----------|:-------------|
| `administrators` | Kredensial admin (Kepala Desa, Sekdes, Operator) | SoftDeletes |
| `penduduk` | Data kependudukan, NIK 16 digit sebagai PK | FK → keluarga, 5 FK → ref_* |
| `keluarga` | Manajemen Kartu Keluarga (KK) | FK → penduduk (kepala) |
| `pengajuan_surat` | Pengajuan dokumen dari warga (ULID PK) | FK → penduduk, kategori_surat |
| `tracking_pengajuan_surat` | Riwayat status pengajuan | FK → pengajuan_surat |
| `kategori_surat` | Template jenis surat (schema JSON dinamis) | SoftDeletes |
| `mutasi_penduduk` | Pencatatan kelahiran, kematian, kedatangan, kepindahan | FK → penduduk |
| `informasi_publik` | Berita dan pengumuman desa | FK → administrators, SoftDeletes |
| `bot_knowledges` | Basis pengetahuan chatbot (FAQ + RAG context) | SoftDeletes |
| `chatbot_logs` | Log interaksi warga dengan bot AI | - |
| `audit_logs` | Jejak audit seluruh aksi mutasi database | Polymorphic user |
| `traffic_logs` | Statistik kunjungan publik | - |
| `telegram_broadcast_queue` | Antrean notifikasi massal | FK → administrators |
| `inventaris_fasilitas` | Data fasilitas publik desa | SoftDeletes |
| `pengaturan_desa` | Konfigurasi dinamis (Key-Value) | - |
| `pengaturan_frontend` | Konfigurasi identitas frontend | - |
| `referensi_wilayah` | Hierarki wilayah (provinsi→desa) | Self-referencing FK |
| `personal_access_tokens` | Token Sanctum (expiry 7 hari) | Polymorphic |
| `notifications` | Notifikasi Laravel | Polymorphic |

### 6.2 Tabel Referensi (Lookup)

| Tabel | Data |
|:------|:-----|
| `ref_agama` | Islam, Kristen, Katolik, Hindu, Buddha, Konghucu, Lainnya |
| `ref_pendidikan` | Tidak/Belum Sekolah, SD, SMP, SMA, D1-D3, D4/S1, S2, S3 |
| `ref_pekerjaan` | 12 jenis pekerjaan |
| `ref_status_perkawinan` | Belum Kawin, Kawin, Cerai Hidup, Cerai Mati |
| `ref_status_keluarga` | 7 status dalam keluarga |
| `kategori_informasi` | Berita, Pengumuman, Agenda, Artikel, Kegiatan |
| `ref_jenis_fasilitas` | 11 jenis fasilitas |

---

## 7. Spesifikasi API

Seluruh endpoint API berada di bawah prefix `/api/v1/`. **Total: 49 Rute (29 API + 20 Web)**.

### 7.1 Autentikasi

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| POST | `/v1/auth/login/warga` | Login warga (NIK + No KK) | Public (throttle:5,1) |
| POST | `/v1/auth/login/admin` | Login admin (username + password) | Public (throttle:5,1) |
| POST | `/v1/auth/logout` | Logout dan hapus token | Bearer Token |
| GET | `/v1/auth/profile` | Profil pengguna yang login | Bearer Token |
| POST | `/v1/auth/bind-telegram` | Hubungkan akun dengan Telegram | Bearer Token (Warga) |

### 7.2 Portal Publik

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| GET | `/v1/informasi` | Daftar informasi publik (published) | Public |
| GET | `/v1/informasi/{slug}` | Detail informasi berdasarkan slug | Public |
| GET | `/v1/statistik/demografi` | Data agregat demografi kependudukan | Public |
| GET | `/v1/statistik/layanan` | Data agregat layanan surat & mutasi | Public |
| GET | `/v1/verifikasi/{hash}` | Validasi keaslian dokumen via QR hash | Public |

### 7.3 Layanan Warga

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| GET | `/v1/surat/kategori` | Daftar kategori surat aktif | Bearer (Warga) |
| GET | `/v1/surat/kategori/{id}` | Detail persyaratan kategori surat | Bearer (Warga) |
| POST | `/v1/surat/pengajuan` | Ajukan pembuatan surat baru | Bearer (Warga) |
| GET | `/v1/surat/pengajuan` | Riwayat pengajuan surat warga | Bearer (Warga) |
| GET | `/v1/surat/pengajuan/{id}` | Detail & tracking pengajuan | Bearer (Warga) |
| POST | `/v1/mutasi` | Ajukan mutasi kependudukan | Bearer (Warga) |
| GET | `/v1/mutasi` | Riwayat mutasi warga | Bearer (Warga) |

### 7.4 Administrasi

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| GET | `/v1/admin/surat/pengajuan` | Semua pengajuan surat | Bearer (Admin) |
| POST | `/v1/admin/surat/pengajuan/{id}/approve` | Setujui pengajuan | Bearer (Admin) |
| POST | `/v1/admin/surat/pengajuan/{id}/reject` | Tolak pengajuan | Bearer (Admin) |
| GET | `/v1/admin/mutasi` | Semua pengajuan mutasi | Bearer (Admin) |
| POST | `/v1/admin/mutasi/{id}/approve` | Setujui mutasi | Bearer (Admin) |
| POST | `/v1/admin/mutasi/{id}/reject` | Tolak mutasi | Bearer (Admin) |
| GET | `/v1/admin/informasi` | Semua informasi (termasuk draft) | Bearer (Admin) |
| POST | `/v1/admin/informasi` | Buat informasi baru | Bearer (Admin) |
| PUT | `/v1/admin/informasi/{id}` | Update informasi | Bearer (Admin) |
| DELETE | `/v1/admin/informasi/{id}` | Hapus informasi | Bearer (Admin) |
| POST | `/v1/admin/statistik/clear-cache` | Bersihkan cache statistik | Bearer (Admin) |

### 7.5 Telegram & Webhook

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| POST | `/v1/telegram/webhook` | Menangani pesan masuk dari Telegram | Secret Token (throttle:60,1) |

### 7.6 Rute Web (20 Rute)

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| GET | `/` | Beranda portal publik | Public |
| GET | `/profil` | Profil desa | Public |
| GET | `/informasi` | Daftar informasi (search + filter) | Public |
| GET | `/informasi/{slug}` | Detail informasi | Public |
| GET | `/verifikasi` | Halaman verifikasi dokumen | Public |
| GET | `/verifikasi/{hash}` | Verifikasi dokumen via hash | Public |
| GET | `/fasilitas` | Daftar fasilitas publik | Public |
| GET | `/statistik` | Statistik desa | Public |
| GET | `/login` | Halaman login warga | Guest |
| POST | `/login` | Proses login (NIK + No KK) | Guest (throttle:5,1) |
| POST | `/logout` | Logout warga | Auth:penduduk |
| GET | `/warga/dashboard` | Dashboard warga (4 tab) | Auth:penduduk |
| GET | `/warga/profil` | Profil warga (upload foto) | Auth:penduduk |
| POST | `/warga/profil` | Update profil + foto KTP/KK | Auth:penduduk |
| GET | `/warga/keluarga` | Data anggota keluarga | Auth:penduduk |
| PUT | `/warga/keluarga/{nik}` | Update data anggota (KK only) | Auth:penduduk |
| GET | `/warga/surat/ajukan/{kategori}` | Form pengajuan surat (wizard) | Auth:penduduk |
| POST | `/warga/surat/pengajuan` | Submit pengajuan surat | Auth:penduduk |
| GET | `/warga/pengajuan/{id}` | Detail pengajuan + tracking | Auth:penduduk |
| GET | `/warga/pengajuan/{id}/print` | Cetak surat (QR Code + A4) | Auth:penduduk |

---

## 8. Integrasi AI dan Telegram

### 8.1 Multi-AI Provider (5 Provider)

Sistem mendukung 5 provider AI dengan arsitektur fallback chain otomatis:

| Provider | Model Default | Base URL | Keterangan |
|:---------|:-------------|:---------|:-----------|
| OpenAI | gpt-4o-mini | api.openai.com/v1 | Provider utama |
| Google Gemini | gemini-pro | generativelanguage.googleapis.com | Header auth `x-goog-api-key` |
| DeepSeek | deepseek-chat | api.deepseek.com/v1 | OpenAI-compatible (China AI) |
| AWS Bedrock | anthropic.claude-3 | AWS SDK | Access key + secret + region |
| Ollama | llama3.2 | localhost:11434/v1 | Lokal tanpa internet |

Arsitektur provider menggunakan `BaseAiProvider` abstract class yang menyatukan logika shared (system prompt, semantic cache, tokenizer) untuk menghindari duplikasi kode. Menambah provider baru hanya membutuhkan 1 class baru + 1 case di `FallbackAiService`.

### 8.2 Telegram Bot

- **Webhook Secret Authentication**: Validasi `X-Telegram-Bot-Api-Secret-Token` untuk mencegah request palsu
- **Knowledge Base**: FAQ statis + basis pengetahuan dinamis dari database
- **RAG Context**: Retrieval-Augmented Generation untuk jawaban kontekstual
- **Semantic Cache**: Exact match + Jaccard similarity + Levenshtein distance (threshold 80%)
- **Rate Limit AI**: 10 query/hari per chat ID (persistent via cache key + tanggal)
- **PII Protection**: Log webhook di-sanitasi (hanya `update_id`, `type`, `chat_type`)
- **Broadcasting**: Antrean asinkronus via Redis untuk notifikasi massal

---

## 9. Keamanan Sistem

Platform dirancang dengan standar keamanan berlapis:

### 9.1 Security Headers (6 Header)

| Header | Nilai |
|:-------|:------|
| Content-Security-Policy | `default-src 'self'; script-src 'self' 'unsafe-inline'; frame-ancestors 'none'` |
| X-Frame-Options | `DENY` |
| X-Content-Type-Options | `nosniff` |
| Strict-Transport-Security | `max-age=31536000; includeSubDomains` (production) |
| Permissions-Policy | `camera=(), microphone=(), geolocation=()` |
| Referrer-Policy | `strict-origin-when-cross-origin` |

### 9.2 Autentikasi & Otorisasi

- **Sanctum Token**: Expiry 7 hari (bukan infinite), ability-based (`warga`/`admin`)
- **5 Authorization Policies**: PengajuanSurat, MutasiPenduduk, InformasiPublik, Penduduk, Administrator
- **Role-Based Access**: Kepala Desa, Sekdes, Operator — masing-masing dengan hak akses berbeda

### 9.3 Rate Limiting

| Endpoint | Limit |
|:---------|:------|
| Login Warga/Admin | 5 percobaan per menit per IP |
| Telegram Webhook | 60 request per menit |
| API Global | 60 request per menit per user/IP |
| AI Chatbot | 10 query per hari per chat ID |

### 9.4 Proteksi Data

- **Audit Trail**: Setiap aksi Create/Update/Delete tercatat (aktor, timestamp, data lama/baru)
- **XSS Prevention**: DOMPurify sanitasi pada client, Laravel escaping pada server
- **SQL Injection**: Eloquent ORM prepared statements
- **CSRF**: Token pada semua form web
- **PII di Log**: Payload Telegram di-sanitasi, tidak log data pribadi warga
- **WebP Compression**: Client-side image compression ke WebP (PDF bypass otomatis)
- **Tanda Tangan Digital**: QR Code SHA-256 pada setiap dokumen surat

---

## 10. Aplikasi Mobile & Desktop (Capacitor + Electron)

Aplikasi native untuk Android, iOS, Windows, Mac, dan Linux dibangun dari satu codebase Vue 3 di folder `apps/`:

### 10.1 Fitur Mobile/Desktop

- Login warga (NIK + No KK / PIN) dan admin (Username + Password)
- Satu pintu masuk: routing otomatis ke dashboard Warga (Material Design) atau panel Admin (TailAdmin)
- Pengajuan surat dengan form dinamis & mutasi kependudukan
- Riwayat pengajuan + tracking status
- Profil warga + anggota keluarga + Statistik desa
- Admin: kelola surat, mutasi, informasi
- **Sistem Sinkronisasi Offline** untuk daerah tanpa sinyal

### 10.2 Arsitektur Apps (`apps/`)

- **Framework**: Vue 3 (Composition API) + TypeScript + Tailwind v4
- **State Management**: Pinia
- **Routing**: Vue Router
- **Offline Data**: IndexedDB (localForage) / SQLite Capacitor Plugin
- **API**: Mengkonsumsi langsung `/api/v1/` yang sama dengan portal web, meminimalisir duplikasi kode (YAGNI).

### 10.3 Build & Run

```bash
cd apps
npm install

# Development Web
npm run dev

# Android (via Capacitor)
npx cap sync android
npx cap open android

# Windows / Desktop (via Electron)
npm run electron:dev
```

---

## 11. Docker dan DevOps

### 11.1 Docker (Production)

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed --force
```

Layanan: `app` (PHP-FPM + Nginx + Supervisor), `db` (MySQL 8.0), `redis` (Alpine).

### 11.2 CI/CD Pipeline

GitHub Actions workflow (`.github/workflows/ci.yml`):
- **Laravel**: Setup PHP 8.3 → Composer install → Migrate → PHPUnit
- **Vue**: Setup Node 20 → npm ci → ESLint → Vitest

### 11.3 Deployment Guide

Lihat `DEPLOY.md` untuk panduan lengkap:
- Tuning PHP-FPM, MariaDB, Redis, OpCache untuk **1 GB RAM**
- Supervisor config untuk queue worker (2 proses)
- Crontab untuk scheduler
- Telegram webhook setup

---

## 12. Pengujian

### 12.1 Cakupan Pengujian

| Layer | File Test | Test Methods | Assertions |
|:------|:---------|:------------|:-----------|
| Feature (Backend) | 16 | 172 | 400+ |
| Unit (Backend) | 24 | 89 | 120+ |
| Vue.js (Frontend) | 13 | 36 | 60+ |
| **Total** | **53** | **297** | **580+** |

### 12.2 Area yang Diuji

- **Autentikasi**: Login warga/admin, logout, token lifecycle, telegram bind
- **Pengajuan Surat**: Submit, approve, reject, tracking, nomor registrasi
- **Mutasi Penduduk**: 4 jenis mutasi, status update atomik
- **Keamanan**: SQL injection (6), XSS (3), RBAC (7), IDOR (3), rate limiting (2), mass assignment (2), path traversal (2), CSRF, token security, security headers
- **E2E Simulation**: Guest → Citizen → Admin → QR Verification (unified journey)
- **Policies**: PengajuanSurat, MutasiPenduduk (approve/reject per role)
- **Jobs**: GenerateSuratPdf, ProcessTelegramMessage, SendNewsTelegram
- **Services**: PdfGenerator, Telegram, AI fallback, StatistikService
- **Web Controllers**: Submission, Profile, Family, Public Portal
- **Vue Components**: FormInput, FormSelect, StatusBadge, Toast, Login behavior

### 12.3 Eksekusi

```bash
# Backend (PHPUnit)
php artisan test

# Frontend (Vitest)
npm run test

# Lint
npm run lint
```

---

## 13. Peta Jalan

### Fase 1: Backend API & Core Engine — **Selesai**

- Database relasional 19+ tabel + 7 referensi
- 29 endpoint API + 20 rute web
- Integrasi multi-AI (5 provider) dengan fallback chain
- Telegram Bot dengan RAG, semantic cache, dan knowledge base
- Sistem QR Code SHA-256 untuk tanda tangan digital
- Security headers, rate limiting, audit trail

### Fase 2: Frontend & Admin Panel — **Selesai**

- SPA dengan Inertia.js + Vue 3 + Tailwind CSS v4
- Dashboard admin Filament PHP v5
- Multi-step wizard pengajuan surat (schema-driven)
- DOMPurify sanitasi XSS, debounce search
- Kompresi gambar WebP client-side
- Aplikasi Multiplatform Vue 3 (Capacitor/Electron) untuk Warga & Admin

### Fase 3: Testing & Deployment — **Selesai**

- 297 automated tests (261 PHP + 36 Vue)
- Docker containerization (Dockerfile + docker-compose)
- CI/CD pipeline (GitHub Actions)
- Panduan deployment production (DEPLOY.md, 1GB RAM tuning)

---

## 14. Kontribusi

Kontribusi sangat diterima. Untuk berkontribusi:

1. Fork repositori ini
2. Buat branch baru (`git checkout -b fitur/nama-fitur`)
3. Commit perubahan (`git commit -m 'Tambahkan deskripsi perubahan'`)
4. Push ke branch (`git push origin fitur/nama-fitur`)
5. Buka Pull Request

Pastikan seluruh pengujian lulus sebelum mengirim PR:

```bash
php artisan test
npm run test
npm run lint
```

---

## 15. Lisensi

Proyek ini dilisensikan di bawah **MIT License**. Detail lisensi dapat dibaca di berkas `LICENSE`.

---

**AvaraDesa — Sistem Informasi Desa Terpadu**
