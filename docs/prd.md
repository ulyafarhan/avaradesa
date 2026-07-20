# PRODUCT REQUIREMENTS DOCUMENT (PRD)

**Sistem Informasi Desa (SIG) Terpadu**

**Informasi Dokumen**

* **Nama Produk:** AvaraDesa — Sistem Informasi Desa Terpadu
* **Lokasi Implementasi:** Umum (dapat dikonfigurasi sesuai desa masing-masing)
* **Versi Dokumen:** 2.0 (Rebrand & Generic Architecture)
* **Fokus Rilis (MVP):** Kependudukan Dinamis, Persuratan Mandiri TTE QR-Code, Portal Real-Time, & Telegram AI Gateway

---

## 1. Ringkasan Eksekutif & Visi Produk

AvaraDesa dirancang untuk menyelesaikan masalah inti administrasi desa melalui pendekatan desentralisasi input. Sistem ini menggeser beban pendataan dari aparatur desa kepada partisipasi aktif warga melalui layanan mandiri (*self-service*). Ekosistem ini menggabungkan layanan birokrasi *paperless* yang sah secara regulasi dengan portal informasi publik yang menarik data secara langsung (*real-time*) dari basis data kependudukan.

---

## 2. Cakupan Ekosistem Aplikasi

Ekosistem AvaraDesa beroperasi di bawah satu domain utama yang dibagi menjadi empat modul antarmuka utama:

1. **Portal Publik Desa (Landing Page):** Pintu depan sistem yang dapat diakses publik. Menampilkan profil desa (termasuk foto perangkat desa yang diatur secara dinamis), berita, dan statistik demografi *real-time*.
2. **Aplikasi Web & Mobile Warga:** Area privat bagi warga (login via NIK, PIN, atau Biometric) untuk mengajukan mutasi kependudukan dan memproses surat menyurat digital. Tersedia dalam tiga platform: web (Inertia.js + Vue 3 + Tailwind CSS v4), mobile Android/iOS (Capacitor 8 + Vue 3 + TypeScript), dan desktop Windows/Linux (Electron 43).
3. **Web Dashboard Admin (SPA):** Panel kontrol terpusat bagi Kepala Desa, Sekdes, dan Operator untuk memverifikasi pengajuan, mengatur konten portal publik secara visual menggunakan input dinamis (Repeater) alih-alih editor JSON mentah, mengelola basis pengetahuan chatbot, memantau log sistem, memantau utilisasi RAM & penyimpanan (disk) server secara riil, serta menganalisis grafik statistik lalu lintas unik warga. (Filament PHP v5).
4. **Telegram Bot & AI Gateway:** Saluran distribusi notifikasi sistem (bebas biaya) dan asisten virtual publik berteknologi AI (Gemini/OpenAI) dengan pemrosesan basis pengetahuan dinamis dari database dan kebijakan penulisan formal (Zero-Emoji).

---

## 3. Tumpukan Teknologi & Infrastruktur

| Komponen | Teknologi Terpilih | Tujuan Penggunaan |
| --- | --- | --- |
| **Frontend Publik** | Vue 3 / Inertia.js / Tailwind CSS v4 / Vite 8 | Reaktivitas antarmuka klien, integrasi seamless dengan backend Laravel, dan pemuatan data statistik cepat. |
| **Frontend Web Warga** | Inertia.js + Vue 3 / Tailwind CSS v4 | Antarmuka dinamis dan responsif untuk layanan mandiri warga (pengajuan surat, mutasi, profil). |
| **Mobile App (Android/iOS)** | Capacitor 8 + Vue 3 + TypeScript / Tailwind CSS v4 | Aplikasi native mobile yang me-reuse komponen Vue 3, mendukung kamera, geolokasi, haptics, status bar, dan sinkronisasi offline via SQLite. |
| **Desktop App** | Electron 43 + Vue 3 + TypeScript | Aplikasi desktop untuk lingkungan dengan koneksi internet terbatas atau kebutuhan akses offline. |
| **Dashboard Admin** | Filament PHP v5 (Admin) | Panel backoffice admin dengan widget bawaan, server monitoring (RAM & disk), traffic analytics grafik, notifikasi database real-time, dan dark mode. |
| **Backend Core API** | Laravel 13 / PHP 8.3 | Pemrosesan logika bisnis, RESTful API, kompilasi PDF surat via DOMPDF, job queue async, dan manajemen state. |
| **AI Engine** | Laravel AI SDK / Multi-Provider (Gemini, OpenAI, DeepSeek, Ollama, Bedrock, Anthropic) | AI multi-provider dengan logika prioritas dan fallback otomatis antar provider. Mendukung chatbot RAG, copywriting artikel, SEO metadata, dan health check. |
| **Database Engine** | MySQL 8.0+ / MariaDB / SQLite (testing) | Relasional data kependudukan dengan tipe data `JSON` untuk skema formulir surat dinamis, referensi wilayah, dan dynamic tags. |
| **Queue & Cache** | array/file (dev) / Redis (production) / Database Queue | Menangani antrean tugas asinkron (rendering PDF, notifikasi Telegram & WhatsApp berantai, broadcast). |
| **Testing & API Docs** | PHPUnit 12 (Unit & Feature) + Vitest 4 (Component) + Scribe 5 (API Documentation) | Cakupan pengujian terintegrasi meliputi unit model, service, request, job, policy, feature journey, keamanan, serta dokumentasi API otomatis. |
| **Integrasi Eksternal** | Telegram API, WhatsApp Webhook, Multi-Provider AI | Penanganan broadcasting pesan otomatis gratis, gerbang komunikasi warga multi-channel, dan mesin pemrosesan bahasa alami dengan semantic caching. |
| **Deployment** | Docker di VPS Linux (Ubuntu), Nginx (Reverse Proxy), Supervisor | Isolasi environment aplikasi, efisiensi resource server desa, dan manajemen SSL (HTTPS) via Cloudflare. |

---

## 4. Kebutuhan Fungsional (Functional Requirements)

### 4.1. Modul Kependudukan Dinamis & Mutasi (Core Data)

* Sistem harus memfasilitasi pembuatan basis data *Single Source of Truth* berdasarkan NIK dan Nomor KK.
* Warga dapat mengajukan 4 jenis mutasi mandiri via aplikasi web/mobile: Kelahiran, Kematian, Kedatangan, dan Kepindahan.
* Admin harus memverifikasi bukti dokumen mutasi sebelum sistem secara otomatis memperbarui status kependudukan dan menyinkronkan data statistik di Portal Publik.

### 4.2. Modul Pelayanan Surat Mandiri (Self-Service) & TTE

* Sistem memuat *form* spesifik berdasarkan kategori surat yang ditarik dari *schema* `JSONB`.
* Data identitas warga (Nama, NIK, TTL) otomatis terisi (*auto-fill*) dan dikunci berdasarkan sesi *login*.
* Admin dapat menolak pengajuan dengan memberikan catatan (opsi perbaikan) atau menyetujuinya.
* Persetujuan admin akan memicu *backend* untuk men-*generate* *Hash* SHA-256 yang diubah menjadi QR Code Tanda Tangan Elektronik (TTE).
* Sistem menggabungkan QR Code dan data warga ke dalam format PDF final yang tidak dapat diedit (*read-only*).

### 4.3. Modul Verifikasi Dokumen Eksternal

* Sistem menyediakan *endpoint* publik untuk memvalidasi QR Code TTE yang terdapat pada PDF/kertas cetakan surat.
* Halaman verifikasi menampilkan status keaslian dokumen, tanggal rilis, dan identitas pemohon.

### 4.4. Modul Telegram & WhatsApp Gateway dan AI Chatbot (Dynamic FAQ, RAG & Semantic Cache)

* Sistem harus menyediakan mekanisme *binding* akun antara aplikasi warga dan *Chat ID* Telegram.
* Setiap transisi status pengajuan surat atau mutasi otomatis memicu notifikasi *push* ke Telegram warga.
* Sistem *Chatbot* memproses pesan masuk menggunakan mesin AI multi-provider yang mendukung fallback otomatis (prioritas: Gemini, OpenAI, DeepSeek, Ollama, Bedrock) — jika provider utama gagal, sistem akan beralih ke provider berikutnya secara otomatis tanpa intervensi admin.
* AI *engine* menggunakan **Laravel AI SDK** yang menyediakan antarmuka terpadu untuk berbagai provider AI, serta logika fallback kustom (`FallbackAiService`) dengan konfigurasi prioritas provider via database (`pengaturan_desa`).
* Admin dapat mengelola dynamic FAQ dan data basis pengetahuan AI secara dinamis melalui dashboard admin (tabel `bot_knowledges`), yang otomatis di-cache 24 jam untuk meminimalisasi overhead token AI.
* **Mekanisme Caching Cerdas**:
  * **Exact Match Cache**: Pencarian cepat berbasis MD5 hash pesan ter-normalisasi di cache (durasi 24 jam).
  * **Semantic Cache**: Tokenisasi berbasis *stopword* bahasa Indonesia dan pencocokan menggunakan Jaccard Similarity serta Levenshtein Distance (untuk kueri pendek) dengan threshold minimal **80% (0.80)** terhadap 100 log interaksi terakhir.
  * Jawaban yang diambil dari cache tidak akan memicu pemanggilan API AI eksternal, melainkan langsung dibalas dengan konsumsi token `0` (*zero cost*).
* Sistem juga menyediakan endpoint webhook untuk **WhatsApp Gateway** yang siap diintegrasikan dengan layanan WhatsApp Business API untuk pengembangan notifikasi multi-channel di masa depan.
* Seluruh notifikasi status surat dan jawaban chatbot harus mematuhi kebijakan bebas emoji (Zero-Emoji) dan menggunakan template pesan formal.

### 4.5. Modul Autentikasi Multi-Metode

* Sistem menyediakan tiga metode autentikasi warga:
  * **Login NIK+KK** (standar): Verifikasi dua faktor menggunakan Nomor Induk Kependudukan dan Nomor Kartu Keluarga.
  * **Login PIN 6-Digit** (cepat): Autentikasi cepat setelah registrasi PIN awal, dengan sistem lockout otomatis selama 15 menit setelah 5 kali percobaan PIN salah.
  * **Login Biometric** (sidik jari/FaceID): Autentikasi instan dari perangkat mobile menggunakan kunci biometrik yang telah diregistrasikan sebelumnya.
* Registrasi PIN 6-digit dilakukan sekali dengan verifikasi ulang NIK+KK. PIN disimpan dalam bentuk *hash* bcrypt bersama penghitung percobaan (`pin_attempts`) dan waktu kunci (`locked_until`).
* Biometric key disimpan sebagai string token unik hasil enkripsi perangkat, diverifikasi secara *server-side* dengan pencocokan eksak.
* Seluruh aktivitas autentikasi (login, registrasi PIN, bind Telegram, registrasi biometric) dicatat dalam tabel `audit_logs` untuk kepentingan *audit trail*.
* Login admin menggunakan *username* dan *password* dengan proteksi *throttle* via middleware Laravel.

### 4.6. Modul Sinkronisasi Offline

* Sistem menyediakan REST API sinkronisasi delta berbasis *timestamp* (`pull` dan `push`) untuk mendukung operasi offline pada perangkat mobile.
* **Pull endpoint** (`GET /api/v1/sync/pull?since=...`): Menarik perubahan data pengajuan surat, mutasi penduduk, dan data penduduk sejak *timestamp* tertentu (ISO8601). Hanya data milik warga yang bersangkutan yang dikembalikan.
* **Push endpoint** (`POST /api/v1/sync/push`): Menerima operasi offline (pengajuan surat, mutasi) yang dibuat saat perangkat tidak terhubung internet, dengan mekanisme idempotensi berbasis *client UUID* untuk mencegah duplikasi data.
* Perangkat mobile menyimpan database lokal berbasis SQLite yang mereplikasi data inti pengguna untuk akses offline penuh.
* Antrean sinkronisasi (*sync queue*) pada perangkat mengelola operasi yang tertunda, dengan mekanisme *retry* otomatis saat koneksi pulih.

---

## 5. Alur Kerja Pengguna (User Flows)

### 5.1. Alur Pengajuan Surat Berbasis State Machine

1. **[Warga]** Login Aplikasi -> Pilih Kategori Surat -> Isi Data Variabel Tambahan -> Unggah Syarat -> Submit. *(Status: `PENDING`)*
2. **[Sistem]** Mengirim notifikasi ke grup Telegram Operator Desa.
3. **[Admin]** Buka Dashboard -> Cek Dokumen.
   * Jika Syarat Tidak Valid -> Klik Tolak -> Isi Alasan. *(Status: `REJECTED`, Warga mendapat notifikasi)*
   * Jika Syarat Valid -> Klik Setujui. *(Status: `APPROVED`)*
4. **[Sistem]** Mengeksekusi *Job Queue: Generate Hash -> Render PDF + QR TTE -> Simpan ke Server.*
5. **[Sistem]** Mengirim *file* PDF final via Telegram *Direct Message* ke warga bersangkutan dengan format pesan teks bersih tanpa emoji.

---

## 6. Desain Database & Entity Relationship Diagram (ERD)

Skema basis data berikut telah dinormalisasi hingga 3NF dan dioptimasi menggunakan indeks.

```sql
CREATE TABLE administrators (
    id CHAR(26) PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL COMMENT 'kepala_desa, sekdes, operator',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pengaturan_desa (
    id CHAR(26) PRIMARY KEY,
    kunci VARCHAR(50) UNIQUE NOT NULL,
    nilai TEXT NOT NULL,
    tipe_data VARCHAR(20) DEFAULT 'string',
    deskripsi VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_pengaturan_kunci ON pengaturan_desa(kunci);

CREATE TABLE referensi_wilayah (
    kode_wilayah VARCHAR(15) PRIMARY KEY,
    nama_wilayah VARCHAR(100) NOT NULL,
    level VARCHAR(20) NOT NULL,
    parent_kode VARCHAR(15),
    CONSTRAINT fk_wilayah_parent FOREIGN KEY (parent_kode) REFERENCES referensi_wilayah(kode_wilayah) ON DELETE RESTRICT
);
CREATE INDEX idx_wilayah_parent ON referensi_wilayah(parent_kode);

CREATE TABLE keluarga (
    no_kk VARCHAR(16) PRIMARY KEY,
    alamat TEXT NOT NULL,
    dusun VARCHAR(50) NOT NULL,
    rt_rw VARCHAR(10)
);

CREATE TABLE penduduk (
    nik VARCHAR(16) PRIMARY KEY,
    no_kk VARCHAR(16) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin CHAR(1) NOT NULL,
    agama VARCHAR(20) NOT NULL,
    pendidikan VARCHAR(50) NOT NULL,
    pekerjaan VARCHAR(50) NOT NULL,
    status_perkawinan VARCHAR(20) NOT NULL,
    status_keluarga VARCHAR(30) NOT NULL,
    status_mutasi VARCHAR(20) DEFAULT 'Tetap',
    telegram_chat_id VARCHAR(50) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_keluarga FOREIGN KEY (no_kk) REFERENCES keluarga(no_kk) ON DELETE RESTRICT
);
CREATE INDEX idx_penduduk_nama ON penduduk(nama_lengkap);
CREATE INDEX idx_penduduk_no_kk ON penduduk(no_kk);

ALTER TABLE keluarga 
ADD COLUMN kepala_keluarga_nik VARCHAR(16),
ADD CONSTRAINT fk_kepala_keluarga FOREIGN KEY (kepala_keluarga_nik) REFERENCES penduduk(nik);

CREATE TABLE mutasi_penduduk (
    id CHAR(26) PRIMARY KEY,
    nik VARCHAR(16) NOT NULL,
    jenis_mutasi VARCHAR(20) NOT NULL,
    tanggal_mutasi DATE NOT NULL,
    keterangan TEXT NOT NULL,
    dokumen_bukti VARCHAR(255) NOT NULL,
    status_verifikasi VARCHAR(20) DEFAULT 'Pending',
    diverifikasi_oleh CHAR(26),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mutasi_penduduk FOREIGN KEY (nik) REFERENCES penduduk(nik) ON DELETE CASCADE,
    CONSTRAINT fk_mutasi_admin FOREIGN KEY (diverifikasi_oleh) REFERENCES administrators(id) ON DELETE SET NULL
);
CREATE INDEX idx_mutasi_status ON mutasi_penduduk(status_verifikasi);

CREATE TABLE kategori_surat (
    id CHAR(26) PRIMARY KEY,
    kode_surat VARCHAR(20) UNIQUE NOT NULL,
    nama_surat VARCHAR(100) NOT NULL,
    template_view VARCHAR(100) NOT NULL,
    schema_isian JSON NOT NULL,
    syarat_dokumen JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE TABLE pengajuan_surat (
    id CHAR(26) PRIMARY KEY,
    nomor_registrasi VARCHAR(30) UNIQUE NOT NULL,
    nik_pemohon VARCHAR(16) NOT NULL,
    kategori_surat_id CHAR(26) NOT NULL,
    data_isian JSON NOT NULL,
    file_syarat JSON NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',
    catatan_penolakan TEXT,
    qr_hash VARCHAR(64) UNIQUE,
    file_pdf_url VARCHAR(255),
    diverifikasi_oleh CHAR(26),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pengajuan_penduduk FOREIGN KEY (nik_pemohon) REFERENCES penduduk(nik) ON DELETE CASCADE,
    CONSTRAINT fk_pengajuan_kategori FOREIGN KEY (kategori_surat_id) REFERENCES kategori_surat(id) ON DELETE RESTRICT,
    CONSTRAINT fk_pengajuan_admin FOREIGN KEY (diverifikasi_oleh) REFERENCES administrators(id) ON DELETE SET NULL
);
CREATE INDEX idx_pengajuan_status ON pengajuan_surat(status);
CREATE INDEX idx_pengajuan_nik ON pengajuan_surat(nik_pemohon);

CREATE TABLE tracking_pengajuan_surat (
    id CHAR(26) PRIMARY KEY,
    pengajuan_surat_id CHAR(26) NOT NULL,
    status_sebelumnya VARCHAR(20),
    status_baru VARCHAR(20) NOT NULL,
    keterangan_update TEXT,
    diupdate_oleh CHAR(26),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tracking_pengajuan FOREIGN KEY (pengajuan_surat_id) REFERENCES pengajuan_surat(id) ON DELETE CASCADE,
    CONSTRAINT fk_tracking_admin FOREIGN KEY (diupdate_oleh) REFERENCES administrators(id) ON DELETE SET NULL
);
CREATE INDEX idx_tracking_pengajuan_id ON tracking_pengajuan_surat(pengajuan_surat_id);

CREATE TABLE informasi_publik (
    id CHAR(26) PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    konten TEXT NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    cover_image VARCHAR(255),
    is_published BOOLEAN DEFAULT FALSE,
    author_id CHAR(26),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_informasi_author FOREIGN KEY (author_id) REFERENCES administrators(id) ON DELETE SET NULL
);
CREATE INDEX idx_informasi_kategori ON informasi_publik(kategori);
CREATE INDEX idx_informasi_slug ON informasi_publik(slug);

CREATE TABLE bot_knowledges (
    id CHAR(26) PRIMARY KEY,
    kunci VARCHAR(50) UNIQUE NOT NULL,
    tipe VARCHAR(20) NOT NULL,
    pertanyaan_atau_topik VARCHAR(255) NOT NULL,
    kata_kunci JSON NOT NULL,
    jawaban_atau_konten TEXT NOT NULL,
    is_aktif BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_bot_knowledges_kunci ON bot_knowledges(kunci);

CREATE TABLE telegram_broadcast_queue (
    id CHAR(26) PRIMARY KEY,
    pesan TEXT NOT NULL,
    kategori_target VARCHAR(50) NOT NULL,
    status VARCHAR(20) DEFAULT 'Queued',
    jadwal_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    waktu_selesai TIMESTAMP,
    created_by CHAR(26),
    CONSTRAINT fk_broadcast_admin FOREIGN KEY (created_by) REFERENCES administrators(id) ON DELETE SET NULL
);
CREATE INDEX idx_broadcast_status ON telegram_broadcast_queue(status);
CREATE INDEX idx_broadcast_jadwal ON telegram_broadcast_queue(jadwal_kirim);

CREATE TABLE chatbot_logs (
    id CHAR(26) PRIMARY KEY,
    telegram_chat_id VARCHAR(50) NOT NULL,
    pesan_masuk TEXT NOT NULL,
    balasan_ai TEXT NOT NULL,
    tokens_used INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_chatbot_chat_id ON chatbot_logs(telegram_chat_id);

CREATE TABLE audit_logs (
    id CHAR(26) PRIMARY KEY,
    user_type VARCHAR(20) NOT NULL,
    user_id VARCHAR(50),
    tindakan VARCHAR(50) NOT NULL,
    nama_tabel VARCHAR(50) NOT NULL,
    record_id VARCHAR(50),
    data_lama JSON,
    data_baru JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_audit_tabel_record ON audit_logs(nama_tabel, record_id);
CREATE INDEX idx_audit_waktu ON audit_logs(created_at);

CREATE TABLE pengaturan_frontend (
    kunci VARCHAR(50) PRIMARY KEY,
    nilai TEXT,
    tipe_data VARCHAR(20) DEFAULT 'string',
    deskripsi VARCHAR(255)
);

CREATE TABLE traffic_logs (
    id CHAR(26) PRIMARY KEY,
    ip_address VARCHAR(45),
    user_agent TEXT,
    path VARCHAR(255),
    method VARCHAR(10),
    referer VARCHAR(255),
    is_bot BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_traffic_logs_created_at ON traffic_logs(created_at);
```

---

## 7. Kebutuhan Non-Fungsional (Non-Functional Requirements)

* **Keamanan & Enkripsi:** Koneksi terenkripsi TLS 1.3 (HTTPS). Payload API yang dimutasi dilindungi token Sanctum. Hash QR Code dibangkitkan dari kombinasi data esensial menggunakan SHA-256.
* **Performa & Caching**:
  * Kecepatan respon web maksimal 500ms pada load normal.
  * **Benchmark aktual (SQLite):**
    * Statistik demografi: 1 aggregate query (dari 9 terpisah)
    * Statistik layanan: 1 aggregate query (dari 5 terpisah)
    * PengaturanDesa: all-in-one cached, 0 queries per PDF
    * Audit log: cached pluck, 0 N+1
    * Database: 19 indexes + 9 composite indexes
    * Admin dashboard: cached 5 menit
    * Home page public: 14 queries (first load), cached thereafter
  * Cache Redis pada statistik demografi dan layanan desa (durasi 1 hari) dengan pembersihan instan via admin command.
  * Caching cerdas hibrida (Exact Match & Semantic) untuk asisten AI guna memangkas latensi chatbot hingga <2ms dan menghemat alokasi biaya token API.
* **SEO & GEO (Generative Engine Optimization)**:
  * Wajib mengimplementasikan structured data JSON-LD standard global (`GovernmentOrganization` dan `NewsArticle`) pada visual frontend.
  * Meta-tag dinamis (title, description, keywords, Open Graph card) untuk mengoptimalkan indexability pada search engine konvensional dan generative engine.
* **Ketahanan API Telegram:** Eksekusi broadcast di-handle asinkron oleh Laravel Queue untuk menghindari rate limiting.
* **Auditability:** Segala jenis perubahan status surat, pergantian data kependudukan, dan pengaturan konfigurasi wajib direkam secara berkesinambungan di tabel `audit_logs`.

---

## 8. Panduan Antarmuka & UX (User Experience)

* **Aksesibilitas Multi-Generasi:** Aplikasi web & mobile ditujukan untuk berbagai kelompok umur warga. UI harus bersih, menggunakan jenis font tanpa kait (Sans-Serif), kontras warna rasio tinggi, dan tidak menyertakan elemen animasi berat.
* **Mobile-First Design:** Semua formulir persuratan, daftar berita desa, dan dasbor warga dioptimalkan untuk pengoperasian satu tangan pada layar ponsel standar (rasio potret).
* **Indikator Status Jelas:** Setiap fase pengajuan surat (Pending, Proses, Selesai) divisualisasikan dengan diagram alir berjenjang atau garis waktu interaktif (*timeline*) dengan penanda warna yang intuitif tanpa emoji.

---

## 9. Infrastruktur Sistem & Anggaran Implementasi

Sistem diterapkan menggunakan topologi kluster container tunggal yang menyeimbangkan stabilitas dan biaya anggaran Dana Desa.

* **Server Host:** VPS berbasis Linux (Ubuntu 22.04 LTS), spesifikasi min. 2 Core CPU & 2 GB RAM.
* **Domain & SSL:** Ekstensi resmi instansi (`.desa.id`) diproteksi melalui Cloudflare Free Tier (CDN + DDoS Protection + Edge SSL).
* **Manajemen Biaya Operasional:** Tidak dikenakan biaya langganan untuk model AI dasar (Gemini/OpenAI Free/Pay-as-you-go) maupun gerbang notifikasi (Telegram Bot API). Alokasi anggaran berfokus murni pada keberlangsungan infrastruktur komputasi server tahunan (Estimasi ± Rp 2.000.000 / Tahun).

---

## 10. Peta Jalan Implementasi (Deployment Roadmap)

1. **Fase 1 (Bulan 1): Setup & Impor Kependudukan.** Pembuatan environment, inisialisasi basis data MySQL / MariaDB, dan migrasi rekapan kependudukan yang sudah ada ke dalam skema database AvaraDesa.
2. **Fase 2 (Bulan 2): Digitalisasi Surat & Landing Page.** Penyusunan templat 5 surat administrasi paling krusial, logika generator QR Code TTE, dan integrasi modul statistik Vue 3 / Inertia.js.
3. **Fase 3 (Bulan 3): Interkoneksi Telegram & AI.** Membuka webhook penghubung Telegram, konfigurasi antrean Redis, pembuatan tabel `bot_knowledges`, dan pengujian keakuratan asisten virtual AI.
4. **Fase 4 (Bulan 4): Peluncuran Terbatas & Go-Live.** Uji coba internal bersama perangkat desa, perbaikan bug, diakhiri dengan sosialisasi menyeluruh (*grand launching*) ke seluruh warga.
