# Dokumentasi Sistem AvaraDesa

Selamat datang di pusat dokumentasi resmi AvaraDesa - Sistem Informasi Desa Terpadu. Seluruh dokumentasi telah disederhanakan dan dikelompokkan berdasarkan modul peran utama secara rapi dan profesional.

---

## Berkas Dokumentasi Utama

Untuk memudahkan pemahaman sistem secara cepat dan profesional, dokumentasi dibagi menjadi subfolder terstruktur berikut (setiap subfolder berisi **satu hingga dua berkas utama**):

1. **[docs/backend/backend.md](backend/backend.md)**
   * Berisi spesifikasi backend, arsitektur core API (Laravel 13 & Filament PHP v5.x), optimasi hibrida Exact Match + Semantic Caching AI, WhatsApp multi-provider (wa-gateway + Fonnte), sistem notifikasi dual-channel (Telegram + WhatsApp), optimasi query (0 N+1, 19+9 indexes), log pengujian (PHPUnit), dan instruksi pengembangan backend.
2. **[docs/frontend/frontend.md](frontend/frontend.md)**
   * Berisi spesifikasi frontend (Vue 3, Inertia.js, Vite 8, Tailwind CSS v4), detail Single File Component (SFC), form dinamis, penanganan avatar, komponen notifikasi (TelegramCard, WAGatewayCard), optimasi SEO & GEO menggunakan JSON-LD, dan pengujian unit (Vitest).
3. **[docs/architecture/plan-app-capacitor.md](architecture/plan-app-capacitor.md)**
   * Berisi blueprint spesifikasi teknis untuk pengembangan aplikasi Warga dan Admin (Android, iOS, Windows, Desktop) menggunakan Capacitor, Electron, dan Vue 3.
4. **[docs/database/database.md](database/database.md)** & **[docs/database/erd.md](database/erd.md)**
   * Berisi skema database MySQL/MariaDB (27+ tabel: 16 relasional + 2 konfigurasi + 2 AI conversations), diagram kardinalitas relasi, pemetaan lengkap ERD rinci, penempatan indexing kueri, dan sistem logging audit data (audit trail).
5. **[docs/api/api.md](api/api.md)**
   * Berisi panduan lengkap integrasi 60+ endpoint API, alur autentikasi token Sanctum, parameter data isian dinamis, integrasi webhook Telegram & WhatsApp, dan endpoint sinkronisasi gateway.
6. **[docs/api-contract.yaml](api-contract.yaml)**
   * Kontrak standar OpenAPI v3 untuk pengujian endpoints dan integrasi client SDK secara otomatis.
7. **[docs/proposal-wa-gateway-v2.md](proposal-wa-gateway-v2.md)**
   * Proposal fitur WhatsApp Gateway v2 untuk pengembang wa-gateway (9 fitur: FAQ sync, document validation, notifikasi, dll).

---

## Fitur Utama Sistem

| Modul | Deskripsi |
|-------|-----------|
| **Autentikasi Multi-Metode** | Login NIK+KK, PIN 6-digit, Biometric (FaceID/Fingerprint) dengan lockout protection |
| **Pengajuan Surat** | Form dinamis berbasis JSONB, upload dokumen, QR Code TTE, PDF otomatis |
| **Mutasi Penduduk** | Kelahiran, Kematian, Pindah Masuk/Keluar dengan alur verifikasi admin |
| **Chatbot AI** | Gemini/OpenAI + semantic cache, fallback multi-provider, basis pengetahuan FAQ |
| **Notifikasi Dual-Channel** | Telegram Bot + WhatsApp (wa-gateway/Fonnte) dengan template admin-editable |
| **Dashboard Admin** | Filament PHP v5.6, statistik real-time, traffic monitoring, server performance |
| **Offline Sync** | Push/pull data offline dengan conflict resolution |
| **Mobile App** | Capacitor (Android/iOS/Windows) dengan native biometric |

---

## Panduan Ringkas Memulai

* **Sebagai Pengembang Baru**:
  1. Mulai dengan membaca berkas [docs/backend/backend.md](backend/backend.md) bagian **Panduan Instalasi**.
  2. Jalankan perintah `composer install` dan `npm install` di root proyek.
  3. Lakukan migrasi database dengan `php artisan migrate:fresh --seed`.
  4. Setup webhook Telegram: `php artisan telegram:setup-webhook`.
* **Sebagai Pengembang Mobile/Desktop (Capacitor/Electron)**:
  1. Masuk ke direktori `apps/`.
  2. Pahami blueprint di `docs/architecture/plan-app-capacitor.md`.
  3. Jalankan `npm install` kemudian `npm run dev`.
