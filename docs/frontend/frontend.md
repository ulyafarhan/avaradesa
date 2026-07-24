# Dokumentasi Frontend - AvaraDesa

Dokumentasi ini merinci spesifikasi teknis, arsitektur antarmuka klien, integrasi UI, dan panduan pengujian frontend untuk AvaraDesa.

---

## 1. Tumpukan Teknologi Frontend
* **Konektor SPA**: Inertia Laravel (`^3.1`) dengan `@inertiajs/vue3` (`^3.4.0`)
* **Framework Klien**: Vue 3 (`^3.5.38`) dengan Composition API
* **Pre-processor & Styling**: Tailwind CSS v4.3.0
* **Build Tool**: Vite 8 (`^8.0.16`)
* **Desain Ikon**: `@lucide/vue` (`^1.17.0`)
* **Notifikasi**: Toast.vue (komponen kustom) + DOMPurify (`^3.4.11`) untuk sanitasi HTML
* **Testing Tool**: Vitest (`^4.1.8`) & `@vue/test-utils`

---

## 2. Struktur Direktori Utama
* `resources/js/Pages/`: Komponen halaman (Portal Publik, Portal Warga, Admin, Autentikasi).
* `resources/js/Components/`: Komponen UI reusable (13 komponen).
* `resources/js/Layouts/`: Layout utama — **PublicLayout** (publik), **CitizenLayout** (warga terotentikasi), dan Partials/ (PageProgressBar).
* `resources/js/Utils/`: Berkas utilitas pembantu (imageCompressor, alert).
* `resources/js/app.js`: Entry point Inertia — mendaftarkan `pageProgress` sebagai provide (injectable ke layout untuk skeleton loading).

### 2.1. Komponen UI Reusable (13)
| Komponen | Fungsi |
|----------|--------|
| `AppButton.vue` | Tombol interaktif dengan loading state |
| `AppCard.vue` | Container kartu dengan padding & shadow |
| `EmptyState.vue` | Tampilan kosong dengan ilustrasi |
| `FormInput.vue` | Input form dengan validasi & error message |
| `FormSelect.vue` | Select dropdown dengan search |
| `LoadingSpinner.vue` | Indikator loading |
| `Pagination.vue` | Navigasi halaman data |
| `SkeletonLoader.vue` | Placeholder animasi (text, card, table-row, avatar) |
| `StatusBadge.vue` | Badge warna status dinamis |
| `StepIndicator.vue` | Indikator langkah alur |
| `Toast.vue` | Notifikasi ringan + progress bar |
| `TelegramCard.vue` | Kartu integrasi Telegram (status + instruksi bind) |
| `WAGatewayCard.vue` | Kartu integrasi WhatsApp Gateway (status + QR code) |

---

## 3. Navigasi & Layout

### 3.0. Layout Architecture
Kedua layout utama menggunakan **PageProgressBar** (`Layouts/Partials/PageProgressBar.vue`) di bagian atas halaman sebagai indikator navigasi Inertia, plus **Toast.vue** untuk notifikasi global.

### 3.0.1. PublicLayout (Portal Publik)
- **Header**: Sticky, backdrop-blur, logo + nama desa + menu navigasi (Beranda, Profil, Informasi, Statistik, Fasilitas, Verifikasi) — link Inertia.
- **Mobile Menu**: Tombol hamburger dengan `Transition` slide + backdrop.
- **Kotak Aspirasi**: Form input inline di section terpisah, POST via fetch langsung ke `/aspirasi`.
- **Footer**: Informasi kontak, jam operasional, tautan resmi, peta lokasi.

### 3.0.2. CitizenLayout (Portal Warga)
- **Desktop**: Sticky header dengan logo Portal Warga + nav pills (Beranda, Layanan, Keluarga, Profil) + user dropdown (profil, keluarga, logout).
- **Mobile**: Header ringkas + **bottom navigation bar** fixed (4 tab: Beranda, Layanan, Keluarga, Profil) dengan icon dan label.
- **Logout**: Menggunakan `alert.confirm()` sebelum POST `/logout`.
- **Skeleton**: Layout-level skeleton dengan 3 tipe placeholder (text, stat cards, content cards).

---

## 4. Fitur Utama & Logika Frontend

### 4.1. Single File Component (SFC)
Seluruh halaman dikembangkan berbasis SFC (menggabungkan `<script setup>`, `<template>`, dan `<style>` dalam satu berkas `.vue`) untuk reaktivitas yang terisolasi dan modularitas yang tinggi.

### 4.2. Form Isian Dinamis (Dynamic Form Rendering)
Pada halaman pengajuan surat (`Create.vue`), elemen input di-render secara dinamis berdasarkan data skema JSONB (`schema_isian`) yang dikirimkan oleh backend. Hal ini memungkinkan admin menambahkan tipe surat baru tanpa perlu memodifikasi kode frontend.

### 4.3. Komponen Notifikasi Toast & Dialog
Sistem notifikasi menggunakan [Toast.vue](resources/js/Components/Toast.vue) untuk notifikasi ringan instan di pojok kanan atas, dan [alert.js](resources/js/Utils/alert.js) untuk dialog konfirmasi (keluar sesi, tindakan penting):
- **Toast.vue**: Komponen notifikasi kustom dengan progress bar reaktif, mendukung tipe sukses/error/info. Di-render **langsung di layout** (PublicLayout & CitizenLayout) agar tersedia di seluruh halaman tanpa import ulang.
- **alert.js**: Membungkus SweetAlert2 dengan Tailwind CSS kustom — tombol bulat Teal (setuju) dan Slate (batal), modal *rounded-3xl* modern.
- **DOMPurify**: Digunakan untuk sanitasi HTML pada konten yang di-render dari input pengguna, mencegah serangan XSS.

### 4.4. Skeleton Loader — Layout-Level Page Transitions
AvaraDesa menggunakan strategi skeleton **layout-level** yang dikendalikan oleh Inertia page progress:

- **Provide `pageProgress`** (dari `app.js`): Setiap layout meng-inject `const isLoading = inject('pageProgress')` untuk mengetahui status navigasi Inertia.
- **PublicLayout.vue**: Saat `isLoading` aktif, menampilkan skeleton placeholder (judul, deskripsi, 6 kartu) di dalam `<Transition name="skeleton-fade">` sebelum me-render konten asli (`<slot />`). Menggunakan CSS skeleton shimmer murni (tanpa dependency) dengan `@keyframes skeleton-shimmer`.
- **CitizenLayout.vue**: Pola serupa dengan skeleton untuk statistik, kartu layanan, dan daftar berita.
- **Komponen [SkeletonLoader.vue](resources/js/Components/SkeletonLoader.vue)**: Komponen reusable untuk skeleton partial (non-layout):
  - **Variant**: 4 varian — `text`, `card`, `table-row`, `avatar` — masing-masing dengan `animate-pulse` Tailwind.
  - **Prop `count`**: Jumlah baris/elemen placeholder.
  - **Penggunaan**: Di halaman publik (`Home.vue`, halaman informasi).
- **Fade-in Gambar**: Gambar sampul berita di Home.vue menggunakan skeleton `animate-pulse bg-slate-200/60` selama unduhan, lalu transisi CSS `opacity-100 duration-300` untuk menghindari *popping effect*.

### 4.5. Integrasi Kanal Notifikasi (Portal Warga)
Pada halaman Dashboard → BiodataTab, warga dapat melihat status integrasi kanal notifikasi:
- **TelegramCard.vue**: Menampilkan status koneksi Telegram Bot + instruksi bind (chat_id, /start).
- **WAGatewayCard.vue**: Menampilkan status koneksi WhatsApp Gateway + QR code pairing.

### 4.6. Notifikasi Template Editor (Admin)
Di halaman Filament `PengaturanSistem`, terdapat tab "Template Notifikasi" dengan 12 textarea untuk mengedit template pesan:
- 6 template Telegram (surat: pending/diproses/disetujui/ditolak/selesai + mutasi)
- 6 template WhatsApp (surat: pending/diproses/disetujui/ditolak/selesai + mutasi)
- Placeholder: `{nomor}`, `{catatan}`, `{link}`, `{jenis}`, `{status}`

### 4.7. Phone Auto-Format

Input nomor HP di formulir warga otomatis diformat: `08xxx` / `+62xxx` / `8xxx` / `628xxx` → `628xxx` (format internasional). Field `no_hp` tersedia di halaman Profile.vue portal warga.

---

## 5. Panduan Menjalankan Frontend & Testing

### Pemasangan & Menjalankan Development Server
1. Instal dependensi NPM:
   ```bash
   npm install
   ```
2. Jalankan server kompilasi Vite (hot-reloading):
   ```bash
   npm run dev
   ```
3. Kompilasi untuk mode produksi (production build):
   ```bash
   npm run build
   ```

### Menjalankan Pengujian Unit Frontend
Terdapat **7 file spec** untuk pengujian komponen Vue menggunakan **Vitest** dan **jsdom**:
- `Login.spec.js`, `Home.spec.js`, `Statistik.spec.js` — halaman publik
- `Profile.spec.js`, `Dashboard.spec.js`, `Create.spec.js` — portal warga
- `imageCompressor.spec.js` — utilitas

```bash
npx vitest run
```

---

## 6. Optimasi SEO & GEO (Search Engine & Generative Engine Optimization)
Aplikasi frontend AvaraDesa dilengkapi dengan sistem meta-tag dinamis dan structured data JSON-LD untuk mempermudah indeksasi oleh mesin pencari konvensional (Google) maupun Generative AI Search Engine (Gemini, SearchGPT, Perplexity).

### 6.1. Komponen `<Head>` Dinamis
Setiap halaman publik menggunakan komponen `<Head>` dari Inertia.js untuk menyematkan meta tag berikut:
* `title` & `description`: Diperbarui secara dinamis sesuai konten halaman.
* `og:title`, `og:description`, `og:image`: Menunjang optimalisasi visual saat halaman dibagikan di media sosial.
* `keywords`: Tag kata kunci yang dihasilkan secara otomatis oleh AI untuk memperkuat keterkaitan konten di search engine.

### 6.2. Skema Data Terstruktur (JSON-LD Schemas)
* **`GovernmentOrganization` (Home.vue & Profile.vue)**: Menyediakan data formal desa, seperti koordinat geografis (latitude & longitude), alamat kantor, kontak resmi, serta relasi organisasi pemerintahan desa.
* **`NewsArticle` (Information/Show.vue)**: Disematkan pada detail berita/pengumuman desa, memuat data penulis (author), tanggal diterbitkan (datePublished), tanggal diperbarui (dateModified), gambar utama (cover image), dan informasi penerbit (publisher).
* **`WebSite` & `BreadcrumbList`**: Membantu memetakan navigasi situs secara hierarkis bagi crawler AI dan search engine.
* **Penyematan Komponen Dinamis**: Script JSON-LD di dalam `<template>` dibungkus menggunakan `<component :is="'script'"` untuk mematuhi aturan strict dari Vue compiler dan mencegah *side-effect warnings* saat build/test.
