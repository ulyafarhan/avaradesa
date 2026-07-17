# Dokumentasi Sistem AvaraDesa

Selamat datang di pusat dokumentasi resmi AvaraDesa - Sistem Informasi Desa Terpadu. Seluruh dokumentasi telah disederhanakan dan dikelompokkan berdasarkan modul peran utama secara rapi dan profesional.

---

## Berkas Dokumentasi Utama

Untuk memudahkan pemahaman sistem secara cepat dan profesional, dokumentasi dibagi menjadi subfolder terstruktur berikut (setiap subfolder berisi **satu hingga dua berkas utama**):

1. **[docs/backend/backend.md](backend/backend.md)**
   * Berisi spesifikasi backend, arsitektur core API (Laravel 13 & Filament PHP v5.x), optimasi hibrida Exact Match + Semantic Caching AI, optimasi query (0 N+1, 19+9 indexes), log pengujian (PHPUnit), dan instruksi pengembangan backend.
2. **[docs/frontend/frontend.md](frontend/frontend.md)**
   * Berisi spesifikasi frontend (Vue 3, Inertia.js, Vite 8, Tailwind CSS v4), detail Single File Component (SFC), form dinamis, penanganan avatar, optimasi SEO & GEO menggunakan JSON-LD, dan pengujian unit (Vitest).
3. **[docs/architecture/plan-app-capacitor.md](architecture/plan-app-capacitor.md)**
   * Berisi blueprint spesifikasi teknis untuk pengembangan aplikasi Warga dan Admin (Android, iOS, Windows, Desktop) menggunakan Capacitor, Electron, dan Vue 3.
4. **[docs/database/database.md](database/database.md)** & **[docs/database/erd.md](database/erd.md)**
   * Berisi skema database MySQL/MariaDB (18 tabel: 16 relasional + 2 konfigurasi), diagram kardinalitas relasi, pemetaan lengkap ERD rinci, penempatan indexing kueri, dan sistem logging audit data (audit trail).
5. **[docs/api/api.md](api/api.md)**
   * Berisi panduan lengkap integrasi 25 endpoint API, alur autentikasi token Sanctum, parameter data isian dinamis, dan integrasi webhook bot Telegram.
6. **[docs/api-contract.yaml](api-contract.yaml)**
   * Kontrak standar OpenAPI v3 untuk pengujian endpoints dan integrasi client SDK secara otomatis.

---

## Panduan Ringkas Memulai

* **Sebagai Pengembang Baru**:
  1. Mulai dengan membaca berkas [docs/backend/backend.md](backend/backend.md) bagian **Panduan Instalasi**.
  2. Jalankan perintah `composer install` dan `npm install` di root proyek.
  3. Lakukan migrasi database dengan `php artisan migrate:fresh --seed`.
* **Sebagai Pengembang Mobile/Desktop (Capacitor/Electron)**:
  1. Masuk ke direktori `apps/`.
  2. Pahami blueprint di `docs/architecture/plan-app-capacitor.md`.
  3. Jalankan `npm install` kemudian `npm run dev`.
