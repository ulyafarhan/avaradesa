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
* `resources/js/Pages/`: Komponen halaman (Portal Publik, Portal Warga, Autentikasi).
* `resources/js/Components/`: Komponen UI reusable (AppButton, AppCard, FormInput, FormSelect, Pagination, StatusBadge, StepIndicator, SkeletonLoader, Toast, EmptyState).
* `resources/js/Layouts/`: Layout utama (PublicLayout untuk halaman publik, CitizenLayout untuk portal warga terotentikasi).
* `resources/js/Utils/`: Berkas utilitas pembantu (imageCompressor, alert).

---

## 3. Fitur Utama & Logika Frontend

### 3.1. Single File Component (SFC)
Seluruh halaman dikembangkan berbasis SFC (menggabungkan `<script setup>`, `<template>`, dan `<style>` dalam satu berkas `.vue`) untuk reaktivitas yang terisolasi dan modularitas yang tinggi.

### 3.2. Form Isian Dinamis (Dynamic Form Rendering)
Pada halaman pengajuan surat (`Create.vue`), elemen input di-render secara dinamis berdasarkan data skema JSONB (`schema_isian`) yang dikirimkan oleh backend. Hal ini memungkinkan admin menambahkan tipe surat baru tanpa perlu memodifikasi kode frontend.

### 3.3. Komponen Notifikasi Toast & Dialog
Sistem notifikasi menggunakan [Toast.vue](resources/js/Components/Toast.vue) untuk notifikasi ringan instan di pojok kanan atas, dan [alert.js](resources/js/Utils/alert.js) untuk dialog konfirmasi (keluar sesi, tindakan penting):
- **Toast.vue**: Komponen notifikasi kustom dengan progress bar reaktif, mendukung tipe sukses/error/info.
- **alert.js**: Membungkus SweetAlert2 dengan Tailwind CSS kustom — tombol bulat Teal (setuju) dan Slate (batal), modal *rounded-3xl* modern.
- **DOMPurify**: Digunakan untuk sanitasi HTML pada konten yang di-render dari input pengguna, mencegah serangan XSS.

### 3.4. Skeleton Loader & Transisi Gambar
Komponen [SkeletonLoader.vue](resources/js/Components/SkeletonLoader.vue) menyediakan placeholder animasi untuk berbagai tipe konten:
- **Variant**: Mendukung 4 varian — `text`, `card`, `table-row`, dan `avatar` — masing-masing dengan `animate-pulse` Tailwind.
- **Prop `count`**: Mengatur jumlah baris/elemen placeholder yang dirender.
- **Penggunaan**: Dipakai di halaman publik (`Home.vue`, halaman informasi) dan portal warga untuk memberikan umpan balik visual selama data dimuat.
- **Fade-in Gambar**: Gambar sampul berita di Home.vue menggunakan skeleton `animate-pulse bg-slate-200/60` selama unduhan, lalu transisi CSS `opacity-100 duration-300` untuk menghindari *popping effect*.

---

## 4. Panduan Menjalankan Frontend & Testing

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

## 5. Optimasi SEO & GEO (Search Engine & Generative Engine Optimization)
Aplikasi frontend AvaraDesa dilengkapi dengan sistem meta-tag dinamis dan structured data JSON-LD untuk mempermudah indeksasi oleh mesin pencari konvensional (Google) maupun Generative AI Search Engine (Gemini, SearchGPT, Perplexity).

### 5.1. Komponen `<Head>` Dinamis
Setiap halaman publik menggunakan komponen `<Head>` dari Inertia.js untuk menyematkan meta tag berikut:
* `title` & `description`: Diperbarui secara dinamis sesuai konten halaman.
* `og:title`, `og:description`, `og:image`: Menunjang optimalisasi visual saat halaman dibagikan di media sosial.
* `keywords`: Tag kata kunci yang dihasilkan secara otomatis oleh AI untuk memperkuat keterkaitan konten di search engine.

### 5.2. Skema Data Terstruktur (JSON-LD Schemas)
* **`GovernmentOrganization` (Home.vue & Profile.vue)**: Menyediakan data formal desa, seperti koordinat geografis (latitude & longitude), alamat kantor, kontak resmi, serta relasi organisasi pemerintahan desa.
* **`NewsArticle` (Information/Show.vue)**: Disematkan pada detail berita/pengumuman desa, memuat data penulis (author), tanggal diterbitkan (datePublished), tanggal diperbarui (dateModified), gambar utama (cover image), dan informasi penerbit (publisher).
* **`WebSite` & `BreadcrumbList`**: Membantu memetakan navigasi situs secara hierarkis bagi crawler AI dan search engine.
* **Penyematan Komponen Dinamis**: Script JSON-LD di dalam `<template>` dibungkus menggunakan `<component :is="'script'"` untuk mematuhi aturan strict dari Vue compiler dan mencegah *side-effect warnings* saat build/test.
