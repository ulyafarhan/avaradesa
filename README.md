# AvaraDesa — Sistem Informasi Desa Terpadu

> **Platform Digital Administrasi Desa Berbasis Cloud dengan Integrasi Multi-AI, Aplikasi Mobile/Desktop (Vue 3 + Capacitor + Electron), dan Arsitektur Production-Grade**

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
- [10. Aplikasi Mobile & Desktop (Capacitor + Electron)](#10-aplikasi-mobile--desktop-capacitor--electron)
- [11. Docker dan DevOps](#11-docker-dan-devops)
- [12. Pengujian](#12-pengujian)
- [13. Peta Jalan](#13-peta-jalan)
- [14. Kontribusi](#14-kontribusi)
- [15. Lisensi](#15-lisensi)

---

## 1. Pendahuluan

### 1.1 Tentang AvaraDesa

AvaraDesa merupakan platform sistem informasi desa terpadu yang mendigitalisasi seluruh tata kelola administrasi pada tingkat Desa. Melalui pendekatan *self-service*, beban kerja administratif yang sebelumnya berpusat pada aparatur desa dialihkan secara aman kepada partisipasi aktif masyarakat melalui portal layanan mandiri berbasis web (PWA) dan aplikasi multiplatform (Vue 3 + Capacitor untuk Android/iOS + Electron untuk Windows/Mac/Linux).

### 1.2 Visi dan Misi

**Visi**: Mewujudkan ekosistem administrasi desa yang transparan, akuntabel, dan efisien berbasis teknologi cloud guna mengoptimalkan kualitas pelayanan publik.

**Misi**:
- Digitalisasi menyeluruh terhadap administrasi kependudukan dan pencatatan sipil
- Penyediaan sistem pengajuan surat mandiri untuk memangkas birokrasi fisik
- Implementasi kecerdasan buatan (AI) multi-provider untuk asisten virtual pelayanan 24 jam
- Penjaminan keamanan data sensitif warga melalui audit trail, kriptografi SHA-256, dan security headers berlapis
- Penyediaan aplikasi multiplatform (Android, iOS, Windows, Mac, Linux) untuk aksesibilitas warga di berbagai perangkat

### 1.3 Target Pengguna

| Peran | Deskripsi |
|:------|:----------|
| **Warga Desa** | Masyarakat yang membutuhkan layanan administrasi desa secara daring via PWA atau aplikasi mobile/desktop |
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
| Laravel Sanctum | ^4.3 | Otentikasi token berbasis SHA-256 (expiry 1440 menit / 24 jam) |
| Laravel AI | ^0.9.1 | SDK AI multi-provider resmi Laravel (16 driver) |
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
| Vitest | ^4.1.8 | Framework pengujian frontend |
| Axios | ^1.17.0 | HTTP client |

### 2.3 Stack Mobile & Desktop (Capacitor + Electron)

| Komponen | Versi | Keterangan |
|:---------|:------|:-----------|
| Vue 3 | ^3.5.39 | Framework UI dengan Composition API + TypeScript |
| Pinia | ^4.0.2 | State management reaktif |
| Vue Router | ^5.2.0 | Routing deklaratif |
| Tailwind CSS | ^4.3.3 | Utility-first CSS framework |
| Capacitor | ^8.4.2 | Native runtime untuk Android & iOS |
| Electron | ^43.1.1 | Desktop runtime untuk Windows, Mac & Linux |
| TypeScript | ~6.0.2 | Type safety |
| Vite | ^8.1.1 | Build tool |

### 2.4 Stack Pengujian

| Komponen | Versi | Keterangan |
|:---------|:------|:-----------|
| PHPUnit | ^12.5.12 | ~200 test methods backend (unit + feature) |
| Vitest | ^4.1.8 | 14 test methods frontend Vue (7 spec files) |
| @vue/test-utils | ^2.4.11 | Utilitas pengujian komponen Vue |
| @vitest/coverage-v8 | ^4.1.8 | Laporan cakupan pengujian |
| Flutter Test | — | 20 test methods (10 file di test/) — widget & unit |

---

## 3. Arsitektur Sistem

### 3.1 Diagram Arsitektur

Sistem dibangun menggunakan arsitektur monolit modern 4 layer yang menggabungkan backend, frontend web, aplikasi multiplatform, dan integrasi layanan eksternal dalam satu repositori terpadu.

```
┌─────────────────────────────────────────────────────────────────┐
│                    Frontend Client Layer                         │
├─────────────────────────────────────────────────────────────────┤
│  Portal Publik & PWA Warga (Inertia.js + Vue 3 + Tailwind v4)  │
│  Aplikasi Mobile/Desktop (Vue 3 + Pinia + Capacitor/Electron)   │
│  Build Engine: Vite 8 (Web) / Vite 8 + Capacitor 8 (Mobile)    │
│                          / Electron 43 (Desktop)                 │
│  Sanitasi: DOMPurify (Web)                                      │
└──────────────────────────┬──────────────────────────────────────┘
                           │ (Protokol Inertia / REST API)
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│           Backend & Admin Platform (Laravel 13)                  │
├─────────────────────────────────────────────────────────────────┤
│  Dashboard Admin & Operator (Filament PHP v5)                    │
│  RESTful API Engine (Sanctum Auth, 57 Endpoint, 20 Web Routes)  │
│  Document Processor (DomPDF + QR Code SHA-256)                   │
│  Security Layer (CSP, HSTS, Rate Limiting, Audit Trail)          │
│  5 Authorization Policies (RBAC per Model)                       │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                  Data & External Service Layer                    │
├─────────────────────────────────────────────────────────────────┤
│  Database: MySQL 8.0+ / MariaDB (26+ tabel)                     │
│  Cache & Queue: Redis 6+ (session, cache, queue worker)         │
│  Cloud Storage: S3 / Cloudflare R2 (auto-switch)                │
│  AI Multi-Provider (16 Laravel AI SDK + 6 Custom Class):         │
│    ├── Gemini (gemini-pro) — aktif di fallback chain             │
│    ├── OpenAI (GPT-4o-mini) — aktif di fallback chain            │
│    ├── DeepSeek — class siap, tinggal aktifkan                   │
│    ├── Ollama — class siap (Llama, Mistral, dll)                 │
│    ├── AWS Bedrock — class siap (Claude/Titan)                   │
│    └── 11 provider lain via Laravel AI SDK                       │
│  Notifikasi & Bot: Telegram Bot API + WhatsApp API               │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Penjelasan Layer

- **Frontend Client Layer**: Menangani antarmuka pengguna melalui tiga kanal: PWA/SPA berbasis Vue 3 + Inertia.js untuk web, aplikasi Capacitor untuk Android/iOS, dan aplikasi Electron untuk Windows/Mac/Linux. Dilengkapi DOMPurify untuk sanitasi XSS.
- **Backend & Admin Platform**: Mesin pemrosesan utama berbasis Laravel 13 dengan panel Filament v5. Menyediakan REST API dengan Sanctum auth, 5 authorization policies, security headers berlapis (CSP, HSTS, X-Frame-Options), dan rate limiting.
- **Data & External Service Layer**: Penyimpanan data via MySQL dengan 26+ tabel relasional, caching via Redis, cloud storage S3/R2, integrasi 16 AI provider via Laravel AI SDK ditambah 6 custom class provider dengan fallback chain otomatis, notifikasi via Telegram Bot API, dan webhook WhatsApp.

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
| Node.js | 20+ (untuk build frontend & apps) |

### 4.2 Instalasi Lokal (Development)

**1. Clone Repositori**

```bash
git clone <repository-url> avaradesa
cd avaradesa
```

**2. Instal Dependensi Backend**

```bash
composer install
```

**3. Instal Dependensi Frontend Web**

```bash
npm install
```

**4. Instal Dependensi Aplikasi Mobile/Desktop**

```bash
cd apps
npm install
cd ..
```

**5. Konfigurasi Environment**

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

# AI Provider (pilih salah satu atau semua)
AI_DEFAULT_PROVIDER=gemini
GEMINI_API_KEY=your_key
OPENAI_API_KEY=your_key
DEEPSEEK_API_KEY=your_key
OLLAMA_URL=http://localhost:11434
```

**6. Migrasi Database & Seeding**

```bash
php artisan migrate --seed
php artisan storage:link
```

**7. Jalankan Aplikasi**

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
│   │   ├── Controllers/Api/       # Controller API RESTful (13 controller)
│   │   ├── Controllers/Web/       # Controller web Inertia (6 controller)
│   │   ├── Middleware/            # Security headers, cache, traffic, Inertia
│   │   ├── Requests/             # Form Request validasi (Api/ + Web/ + AppApi/)
│   │   └── Resources/            # API Resource transformer (8 + 2 AppApi)
│   ├── Jobs/                      # Job antrean (6 job: PDF, Telegram, Broadcast, WhatsApp)
│   ├── Models/                    # 26 model Eloquent
│   ├── Policies/                  # 5 authorization policies (RBAC)
│   ├── Providers/                 # Service provider
│   └── Services/
│       ├── AiProviders/           # 6 class: BaseAiProvider + 5 provider (Gemini, OpenAI, DeepSeek, Ollama, Bedrock)
│       ├── FallbackAiService.php  # Fallback chain multi-provider
│       ├── TelegramService.php    # Telegram Bot API wrapper
│       ├── TelegramKnowledgeService.php # Knowledge base + RAG
│       ├── WhatsAppService.php    # WhatsApp API wrapper
│       ├── PdfGeneratorService.php# Generator PDF + QR Code
│       ├── StatistikService.php   # Agregasi statistik kependudukan
│       ├── ImageService.php       # Kompresi gambar ke WebP
│       └── GeminiAiService.php    # Gemini AI service (legacy)
├── apps/                          # Aplikasi Multiplatform (Vue 3 + Capacitor + Electron)
│   ├── src/                       # Source code Vue 3 + TypeScript
│   ├── capacitor.config.ts        # Konfigurasi Capacitor
│   ├── electron/                  # Main process Electron
│   ├── electron-builder.yml       # Build config Electron
│   ├── android/                   # Native Android project
│   ├── ios/                       # Native iOS project
│   └── package.json               # Dependensi Vue 3, Pinia, Capacitor, Electron
├── config/                        # Konfigurasi Laravel (sanctum, ai, services, dll)
├── database/
│   ├── factories/                 # 10 factory untuk testing
│   ├── migrations/                # 40 migration file
│   └── seeders/                   # 12 seeder (data awal + dummy)
├── resources/
│   ├── css/                       # Tailwind CSS dengan design tokens
│   ├── js/
│   │   ├── Components/            # 10 komponen Vue reusable
│   │   ├── Layouts/               # CitizenLayout, PublicLayout
│   │   ├── Pages/                 # Halaman Vue (Auth, Citizen, Public)
│   │   └── Utils/                 # alert, imageCompressor, sanitize
│   └── views/                     # Blade templates (app, PDF surat)
├── test/                          # Test Flutter (legacy widget/unit test — 10 file)
├── tests/
│   ├── Feature/                   # 17 file feature test
│   └── Unit/                      # 23 file unit test
├── .docker/                       # Konfigurasi Nginx + Supervisor
├── .github/workflows/             # CI/CD pipeline (Laravel + Vue)
├── Dockerfile                     # PHP-FPM 8.3 + Nginx + Supervisor
├── docker-compose.yml             # 3 services (app + db + redis)
└── DEPLOY.md                      # Panduan deployment produksi (1GB RAM)
```

---

## 6. Skema Database

Sistem didukung oleh **26+ tabel** yang saling terintegrasi, terdiri dari tabel utama, tabel referensi, dan tabel sistem:

### 6.1 Tabel Utama (19+)

| Tabel | Deskripsi | Relasi Kunci |
|:------|:----------|:-------------|
| `administrators` | Kredensial admin (Kepala Desa, Sekdes, Operator) | SoftDeletes |
| `penduduk` | Data kependudukan, NIK 16 digit sebagai PK | FK → keluarga, 5 FK → ref_* |
| `keluarga` | Manajemen Kartu Keluarga (KK) | FK → penduduk (kepala) |
| `pengajuan_surat` | Pengajuan dokumen dari warga (ULID PK) | FK → penduduk, kategori_surat |
| `tracking_pengajuan_surat` | Riwayat status pengajuan | FK → pengajuan_surat |
| `kategori_surat` | Template jenis surat (schema JSON dinamis) | SoftDeletes |
| `mutasi_penduduk` | Pencatatan kelahiran, kematian, kedatangan, kepindahan | FK → penduduk |
| `informasi_publik` | Berita dan pengumuman desa + SEO fields | FK → administrators, SoftDeletes |
| `bot_knowledges` | Basis pengetahuan chatbot (FAQ + RAG context) | SoftDeletes |
| `chatbot_logs` | Log interaksi warga dengan bot AI | — |
| `knowledge_keywords` | Kata kunci untuk pencocokan knowledge base | FK → bot_knowledges |
| `audit_logs` | Jejak audit seluruh aksi mutasi database | Polymorphic user |
| `traffic_logs` | Statistik kunjungan publik | — |
| `telegram_broadcast_queue` | Antrean notifikasi massal | FK → administrators |
| `inventaris_fasilitas` | Data fasilitas publik desa | SoftDeletes |
| `pengaturan_desa` | Konfigurasi dinamis (Key-Value) | — |
| `pengaturan_frontend` | Konfigurasi identitas frontend | — |
| `referensi_wilayah` | Hierarki wilayah (provinsi→desa) | Self-referencing FK |
| `agent_conversations` | Riwayat percakapan AI agent | — |
| `personal_access_tokens` | Token Sanctum (expiry 24 jam) | Polymorphic |
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

Seluruh endpoint API berada di bawah prefix `/api/v1/`. **Total: 57 Endpoint API + 20 Rute Web**.

### 7.1 Autentikasi

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| POST | `/v1/auth/login/warga` | Login warga (NIK + No KK) | Public (throttle:5,1) |
| POST | `/v1/auth/login/admin` | Login admin (username + password) | Public (throttle:5,1) |
| POST | `/v1/auth/register-pin` | Daftarkan PIN untuk login cepat | Public (throttle:5,1) |
| POST | `/v1/auth/login-pin` | Login menggunakan PIN | Public (throttle:5,1) |
| POST | `/v1/auth/login-biometric` | Login menggunakan biometrik | Public (throttle:5,1) |
| POST | `/v1/auth/reset-pin` | Reset PIN (dengan verifikasi) | Public (throttle:3,1) |
| POST | `/v1/auth/logout` | Logout dan hapus token | Bearer Token |
| GET | `/v1/auth/profile` | Profil pengguna yang login | Bearer Token |
| POST | `/v1/auth/bind-telegram` | Hubungkan akun dengan Telegram | Bearer (Warga) |
| POST | `/v1/auth/register-biometric` | Daftarkan perangkat biometrik | Bearer (Warga) |

### 7.2 Portal Publik

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| GET | `/v1/informasi` | Daftar informasi publik (published) | Public |
| GET | `/v1/informasi/{slug}` | Detail informasi berdasarkan slug | Public |
| GET | `/v1/statistik/demografi` | Data agregat demografi kependudukan | Public |
| GET | `/v1/statistik/layanan` | Data agregat layanan surat & mutasi | Public |
| GET | `/v1/verifikasi/{hash}` | Validasi keaslian dokumen via QR hash | Public |

### 7.3 Dashboard & Layanan Warga

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| GET | `/v1/dashboard` | Dashboard warga (ringkasan data) | Bearer (Warga) |
| GET | `/v1/surat/kategori` | Daftar kategori surat aktif | Bearer (Warga) |
| GET | `/v1/surat/kategori/{id}` | Detail persyaratan kategori surat | Bearer (Warga) |
| POST | `/v1/surat/pengajuan` | Ajukan pembuatan surat baru | Bearer (Warga) |
| GET | `/v1/surat/pengajuan` | Riwayat pengajuan surat warga | Bearer (Warga) |
| GET | `/v1/surat/pengajuan/{id}` | Detail & tracking pengajuan | Bearer (Warga) |
| GET | `/v1/surat/pengajuan/{id}/download` | Download PDF surat | Bearer (Warga) |
| POST | `/v1/mutasi` | Ajukan mutasi kependudukan | Bearer (Warga) |
| GET | `/v1/mutasi` | Riwayat mutasi warga | Bearer (Warga) |
| POST | `/v1/chat` | Chat dengan asisten AI | Bearer (throttle:10,1) |

### 7.4 Sinkronisasi Offline

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| POST | `/v1/sync/push` | Sinkronisasi data dari perangkat ke server | Bearer (Warga) |
| GET | `/v1/sync/pull` | Tarik data terbaru dari server | Bearer (Warga) |

### 7.5 Administrasi — Manajemen Penduduk

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| GET | `/v1/admin/penduduk` | Daftar seluruh penduduk | Bearer (Admin) |
| GET | `/v1/admin/penduduk/{id}` | Detail penduduk | Bearer (Admin) |
| POST | `/v1/admin/penduduk` | Tambah penduduk baru | Bearer (Admin) |
| PUT | `/v1/admin/penduduk/{id}` | Update data penduduk | Bearer (Admin) |
| DELETE | `/v1/admin/penduduk/{id}` | Hapus penduduk | Bearer (Admin) |

### 7.6 Administrasi — Surat, Mutasi & Informasi

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| GET | `/v1/admin/surat/pengajuan` | Semua pengajuan surat | Bearer (Admin) |
| POST | `/v1/admin/surat/pengajuan/{id}/approve` | Setujui pengajuan | Bearer (Admin) |
| POST | `/v1/admin/surat/pengajuan/{id}/reject` | Tolak pengajuan | Bearer (Admin) |
| GET | `/v1/admin/mutasi` | Semua pengajuan mutasi | Bearer (Admin) |
| POST | `/v1/admin/mutasi/{id}/approve` | Setujui mutasi | Bearer (Admin) |
| POST | `/v1/admin/mutasi/{id}/reject` | Tolak mutasi | Bearer (Admin) |
| GET | `/v1/admin/informasi` | Semua informasi (termasuk draft) | Bearer (Admin) |
| GET | `/v1/admin/informasi/{id}` | Detail informasi | Bearer (Admin) |
| POST | `/v1/admin/informasi` | Buat informasi baru | Bearer (Admin) |
| PUT | `/v1/admin/informasi/{id}` | Update informasi | Bearer (Admin) |
| DELETE | `/v1/admin/informasi/{id}` | Hapus informasi | Bearer (Admin) |
| POST | `/v1/admin/statistik/clear-cache` | Bersihkan cache statistik | Bearer (Admin) |

### 7.7 Administrasi — Resource Management

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| GET | `/v1/admin/keluarga` | Daftar keluarga | Bearer (Admin) |
| POST | `/v1/admin/keluarga` | Tambah keluarga | Bearer (Admin) |
| DELETE | `/v1/admin/keluarga/{no_kk}` | Hapus keluarga | Bearer (Admin) |
| GET | `/v1/admin/kategori-surat` | Daftar kategori surat | Bearer (Admin) |
| POST | `/v1/admin/kategori-surat` | Tambah kategori surat | Bearer (Admin) |
| PUT | `/v1/admin/kategori-surat/{id}` | Update kategori surat | Bearer (Admin) |
| DELETE | `/v1/admin/kategori-surat/{id}` | Hapus kategori surat | Bearer (Admin) |
| GET | `/v1/admin/fasilitas` | Daftar fasilitas desa | Bearer (Admin) |
| POST | `/v1/admin/fasilitas` | Tambah fasilitas | Bearer (Admin) |
| DELETE | `/v1/admin/fasilitas/{id}` | Hapus fasilitas | Bearer (Admin) |
| GET | `/v1/admin/audit-log` | Log aktivitas sistem | Bearer (Admin) |

### 7.8 Webhook

| Metode | Endpoint | Deskripsi | Otorisasi |
|:-------|:---------|:----------|:----------|
| POST | `/v1/telegram/webhook` | Menangani pesan masuk dari Telegram | IP Restrict (throttle:60,1) |
| POST | `/v1/whatsapp/webhook` | Menangani pesan masuk dari WhatsApp | IP Restrict (throttle:60,1) |

### 7.9 Rute Web (20 Rute)

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
| GET | `/warga/pengajuan/{pengajuan}` | Detail pengajuan + tracking | Auth:penduduk |
| GET | `/warga/pengajuan/{pengajuan}/print` | Cetak surat (QR Code + A4) | Auth:penduduk |

---

## 8. Integrasi AI dan Telegram

### 8.1 Arsitektur AI — Dual Layer

Sistem menggunakan arsitektur AI dua lapis:

**Layer 1 — Laravel AI SDK (16 Provider)**
Lapisan resmi Laravel AI (`laravel/ai ^0.9.1`) menyediakan 16 driver AI yang dapat digunakan langsung:

| Provider | Driver | Keterangan |
|:---------|:-------|:-----------|
| Anthropic | `anthropic` | Claude API |
| Azure | `azure` | Azure OpenAI Service |
| AWS Bedrock | `bedrock` | Claude/Titan via AWS |
| Cohere | `cohere` | Embedding & Reranking |
| DeepSeek | `deepseek` | China AI |
| ElevenLabs | `eleven` | Text-to-Speech |
| Google Gemini | `gemini` | Default provider untuk teks & gambar |
| Groq | `groq` | High-speed inference |
| Jina | `jina` | Embeddings & RAG |
| Mistral | `mistral` | Model Prancis |
| Ollama | `ollama` | Lokal (Llama, Mistral, dll) |
| OpenAI | `openai` | GPT-4o-mini, embeddings, audio |
| OpenAI Compatible | `openai-compatible` | Provider pihak ketiga |
| OpenRouter | `openrouter` | Unified API |
| VoyageAI | `voyageai` | Embeddings |
| xAI | `xai` | Grok API |

**Layer 2 — Custom AI Provider Classes (6 File)**
Lapisan kustom di `app/Services/AiProviders/` untuk fallback chain yang lebih terstruktur:

| Class | Provider | Status |
|:------|:---------|:-------|
| `BaseAiProvider` | Abstract | Class dasar untuk semua provider |
| `GeminiProvider` | Google Gemini | **Aktif** di fallback chain (default) |
| `OpenAiProvider` | OpenAI | **Aktif** di fallback chain (cadangan) |
| `DeepSeekProvider` | DeepSeek | Class siap, tinggal aktifkan |
| `OllamaProvider` | Ollama lokal | Class siap, tinggal aktifkan |
| `BedrockProvider` | AWS Bedrock | Class siap, tinggal aktifkan |

**Fallback Chain**: `GeminiProvider` → `OpenAiProvider` → (error). Jika Gemini gagal, fallback otomatis ke OpenAI.

### 8.2 Telegram Bot

- **Knowledge Base**: FAQ statis + basis pengetahuan dinamis dari tabel `bot_knowledges`
- **RAG Context**: Retrieval-Augmented Generation untuk jawaban kontekstual
- **Semantic Cache**: Exact match + Jaccard similarity + Levenshtein distance (threshold 80%)
- **Rate Limit AI**: 10 query/hari per chat ID (persistent via cache key + tanggal)
- **PII Protection**: Log webhook di-sanitasi (hanya `update_id`, `type`, `chat_type`)
- **Broadcasting**: Antrean asinkronus via Redis untuk notifikasi massal
- **Perintah**: `/start`, `/bind <NIK>`, `/help`, pertanyaan bebas (diarahkan ke AI)

> **Catatan**: Webhook Telegram tidak memverifikasi `X-Telegram-Bot-Api-Secret-Token` — keamanan mengandalkan IP restrict dan throttle.

### 8.3 WhatsApp Bot

- Webhook endpoint `/v1/whatsapp/webhook`
- Job antrean: `SendNewsWhatsappNotificationJob`, `SendStatusWhatsappJob`

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

> **Catatan**: CSP menggunakan `'unsafe-inline'` untuk kompatibilitas dengan Inertia.js dan SweetAlert2 yang menyuntikkan style secara dinamis. Upgrade ke nonce/hash membutuhkan refactor frontend.

### 9.2 Autentikasi & Otorisasi

- **Sanctum Token**: Expiry 1440 menit (24 jam), ability-based (`warga`/`admin`)
- **5 Authorization Policies**: PengajuanSurat, MutasiPenduduk, InformasiPublik, Penduduk, Administrator
- **Role-Based Access**: Kepala Desa, Sekdes, Operator — masing-masing dengan hak akses berbeda
- **Login PIN & Biometrik**: Autentikasi alternatif untuk perangkat mobile

### 9.3 Rate Limiting

| Endpoint | Limit |
|:---------|:------|
| Login Warga/Admin | 5 percobaan per menit per IP |
| Register/Reset PIN | 5/3 percobaan per menit |
| Telegram Webhook | 60 request per menit |
| API Global | 60 request per menit per user/IP |
| AI Chatbot | 10 query per hari per chat ID |
| Pengajuan Surat | 5 per menit |
| Download PDF | 30 per menit |

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

Aplikasi native untuk Android, iOS, Windows, Mac, dan Linux dibangun dari satu codebase Vue 3 + TypeScript di folder `apps/`:

### 10.1 Fitur Mobile/Desktop

- **Login**: NIK + No KK, PIN, dan biometrik (sidik jari / FaceID)
- **Dashboard**: Ringkasan data warga, statistik, notifikasi
- **Pengajuan Surat**: Form dinamis dengan wizard multi-step
- **Mutasi Penduduk**: Ajukan kelahiran, kematian, kedatangan, kepindahan
- **Riwayat & Tracking**: Status pengajuan surat dan mutasi
- **Profil**: Data warga, anggota keluarga, foto KTP/KK
- **Admin**: Kelola surat, mutasi, penduduk, informasi, fasilitas
- **Sinkronisasi Offline**: Push/pull data untuk daerah tanpa sinyal

### 10.2 Arsitektur Apps (`apps/`)

- **Framework**: Vue 3 (Composition API) + TypeScript + Tailwind v4
- **State Management**: Pinia
- **Routing**: Vue Router
- **Offline Data**: IndexedDB (localForage) / SQLite via Capacitor Plugin
- **Native**: Capacitor 8 (Android/iOS) + Electron 43 (Windows/Mac/Linux)
- **API**: Mengkonsumsi langsung `/api/v1/` yang sama dengan portal web, meminimalisir duplikasi kode

### 10.3 Build & Run

```bash
cd apps
npm install

# Development Web
npm run dev

# Android (via Capacitor)
npx cap sync android
npx cap open android

# iOS (via Capacitor)
npx cap sync ios
npx cap open ios

# Windows / Mac / Linux (via Electron)
npm run electron:dev

# Build Production Electron
npm run electron:build
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

| Layer | File Test | Test Methods |
|:------|:---------|:------------|
| Feature (Backend) | 17 | ~140 |
| Unit (Backend) | 23 | ~60 |
| Vue.js (Frontend) | 7 spec | 14 |
| Flutter Widget/Unit (legacy) | 10 | ~20 |
| **Total** | **57** | **~230+** |

### 12.2 Area yang Diuji

- **Autentikasi**: Login warga/admin, logout, token lifecycle, PIN, biometrik, bind telegram
- **Pengajuan Surat**: Submit, approve, reject, tracking, nomor registrasi, download PDF
- **Mutasi Penduduk**: 4 jenis mutasi (lahir, mati, datang, pindah), status update atomik
- **Informasi Publik**: CRUD, publish/draft, slug lookup
- **Statistik**: Demografi, layanan, cache clearing
- **Keamanan**: SQL injection (6 skenario), XSS (3), RBAC (7), IDOR (3), rate limiting (2), mass assignment (2), path traversal (2), CSRF, token security, security headers
- **E2E Simulation**: Guest → Citizen → Admin → QR Verification (unified journey)
- **Policies**: PengajuanSurat, MutasiPenduduk (approve/reject per role)
- **Jobs**: GenerateSuratPdf, ProcessTelegramMessage, SendNewsTelegram, SendNewsWhatsapp, SendStatusWhatsapp
- **Services**: PdfGenerator, Telegram, TelegramKnowledge, AI fallback, StatistikService, ImageService, WhatsApp
- **Sync**: Push/pull data untuk offline sync
- **Web Controllers**: Submission, Profile, Family, Public Portal, Dashboard
- **Vue Components**: FormInput, FormSelect, StatusBadge, Toast, SkeletonLoader, login behavior
- **Webhook**: Telegram webhook handling, rate limit, callback query

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

- Database relasional 26+ tabel + 7 referensi
- 57 endpoint API + 20 rute web
- Integrasi multi-AI dual layer (16 SDK + 6 custom class)
- Fallback chain Gemini → OpenAI dengan provider siap pakai: DeepSeek, Ollama, Bedrock
- Telegram Bot dengan RAG, semantic cache, dan knowledge base
- WhatsApp webhook
- Sistem QR Code SHA-256 untuk tanda tangan digital
- Security headers, rate limiting, audit trail
- Sinkronisasi offline (push/pull)
- Login PIN & biometrik

### Fase 2: Frontend & Admin Panel — **Selesai**

- SPA dengan Inertia.js + Vue 3 + Tailwind CSS v4
- Dashboard admin Filament PHP v5
- Multi-step wizard pengajuan surat (schema-driven)
- DOMPurify sanitasi XSS
- Kompresi gambar WebP client-side
- Aplikasi Multiplatform (Capacitor/Electron) untuk Warga & Admin

### Fase 3: Testing & Deployment — **Selesai**

- 230+ automated tests (PHPUnit + Vitest + Flutter)
- Docker containerization (Dockerfile + docker-compose)
- CI/CD pipeline (GitHub Actions)
- Panduan deployment production (DEPLOY.md, 1GB RAM tuning)

### Fase 4: Pengembangan Lanjutan — **Dalam Pengerjaan**

- Aktivasi penuh DeepSeek, Ollama, dan Bedrock di fallback chain
- Peningkatan cakupan pengujian (target 300+ test methods)
- Notifikasi push via Firebase (Capacitor)
- Dashboard real-time dengan WebSocket
- Integrasi Siskeudes / sistem desa lainnya

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
