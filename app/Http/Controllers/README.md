# Dokumentasi Controllers - SIG-Udeung

Dokumentasi ini menjelaskan struktur, pembagian, dan tanggung jawab seluruh controller dalam aplikasi SIG-Udeung.

---

## Struktur Direktori

```
app/Http/Controllers/
  Controller.php                    - Base controller (abstract)
  Api/                              - Controller untuk endpoint REST API
    AuthController.php
    InformasiPublikController.php
    MutasiPendudukController.php
    PengajuanSuratController.php
    StatistikController.php
    TelegramWebhookController.php
    VerifikasiController.php
  Web/                              - Controller untuk rute web (Inertia.js)
    CitizenAuthController.php
    CitizenDashboardController.php
    CitizenFamilyController.php
    CitizenProfileController.php
    CitizenSubmissionController.php
    PublicPortalController.php
```

---

## API Controllers (`app/Http/Controllers/Api/`)

### AuthController

Menangani seluruh proses autentikasi pengguna melalui API.

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| `loginWarga()` | `POST /v1/auth/login/warga` | Login warga menggunakan NIK + No KK |
| `loginAdmin()` | `POST /v1/auth/login/admin` | Login admin menggunakan username + password |
| `logout()` | `POST /v1/auth/logout` | Mencabut token akses aktif |
| `profile()` | `GET /v1/auth/profile` | Mengambil data profil pengguna login |
| `bindTelegram()` | `POST /v1/auth/bind-telegram` | Menghubungkan akun warga dengan Telegram Chat ID |

**Keterangan**: Rate limiting diterapkan pada login (5 percobaan per menit). Menggunakan Laravel Sanctum untuk manajemen token.

---

### InformasiPublikController

Menangani CRUD artikel informasi publik (berita, pengumuman, agenda desa).

| Method | Endpoint | Fungsi | Akses |
|--------|----------|--------|-------|
| `index()` | `GET /v1/informasi` | Daftar informasi terbit | Publik |
| `show()` | `GET /v1/informasi/{slug}` | Detail informasi berdasarkan slug | Publik |
| `adminIndex()` | `GET /v1/admin/informasi` | Daftar semua artikel (draft + terbit) | Admin |
| `store()` | `POST /v1/admin/informasi` | Buat artikel baru | Admin |
| `update()` | `PUT /v1/admin/informasi/{id}` | Perbarui artikel | Admin |
| `destroy()` | `DELETE /v1/admin/informasi/{id}` | Hapus artikel | Admin |

---

### PengajuanSuratController

Menangani seluruh alur pengajuan surat mandiri oleh warga dan verifikasi oleh admin.

| Method | Endpoint | Fungsi | Akses |
|--------|----------|--------|-------|
| `kategori()` | `GET /v1/surat/kategori` | Daftar kategori surat aktif | Warga |
| `detailKategori()` | `GET /v1/surat/kategori/{id}` | Detail skema & persyaratan surat | Warga |
| `store()` | `POST /v1/surat/pengajuan` | Buat pengajuan surat baru | Warga |
| `index()` | `GET /v1/surat/pengajuan` | Riwayat pengajuan warga | Warga |
| `show()` | `GET /v1/surat/pengajuan/{id}` | Detail & tracking pengajuan | Warga |
| `adminIndex()` | `GET /v1/admin/surat/pengajuan` | Daftar semua pengajuan masuk | Admin |
| `approve()` | `POST /v1/admin/surat/pengajuan/{id}/approve` | Setujui & generate PDF + TTE | Admin |
| `reject()` | `POST /v1/admin/surat/pengajuan/{id}/reject` | Tolak dengan catatan | Admin |

---

### MutasiPendudukController

Menangani pengajuan mutasi kependudukan (kelahiran, kematian, kedatangan, kepindahan).

| Method | Endpoint | Fungsi | Akses |
|--------|----------|--------|-------|
| `store()` | `POST /v1/mutasi` | Ajukan mutasi baru | Warga |
| `index()` | `GET /v1/mutasi` | Riwayat mutasi warga | Warga |
| `adminIndex()` | `GET /v1/admin/mutasi` | Daftar semua mutasi masuk | Admin |
| `approve()` | `POST /v1/admin/mutasi/{id}/approve` | Setujui & perbarui data kependudukan | Admin |
| `reject()` | `POST /v1/admin/mutasi/{id}/reject` | Tolak pengajuan mutasi | Admin |

---

### StatistikController

Menangani penyajian data statistik desa.

| Method | Endpoint | Fungsi | Akses |
|--------|----------|--------|-------|
| `demografi()` | `GET /v1/statistik/demografi` | Statistik demografi kependudukan | Publik |
| `layanan()` | `GET /v1/statistik/layanan` | Statistik pengajuan surat & mutasi | Publik |
| `clearCache()` | `POST /v1/admin/statistik/clear-cache` | Bersihkan cache statistik | Admin |

---

### VerifikasiController

Menangani verifikasi keabsahan dokumen melalui QR Code TTE.

| Method | Endpoint | Fungsi | Akses |
|--------|----------|--------|-------|
| `verify()` | `GET /v1/verifikasi/{hash}` | Verifikasi dokumen berdasarkan hash | Publik |

---

### TelegramWebhookController

Menangani pesan masuk dari Telegram Bot API untuk chatbot AI.

| Method | Endpoint | Fungsi | Akses |
|--------|----------|--------|-------|
| `handle()` | `POST /v1/telegram/webhook` | Proses webhook Telegram | Publik (throttle 60/1) |

---

## Web Controllers (`app/Http/Controllers/Web/`)

### PublicPortalController

Menangani halaman-halaman portal publik yang dapat diakses tanpa autentikasi.

| Method | Rute | Nama Rute | Fungsi |
|--------|------|-----------|--------|
| `home()` | `GET /` | `home` | Beranda portal desa |
| `profile()` | `GET /profil` | `profile` | Profil desa |
| `information()` | `GET /informasi` | `information.index` | Daftar informasi |
| `informationShow()` | `GET /informasi/{slug}` | `information.show` | Detail informasi |
| `verifyIndex()` | `GET /verifikasi` | `verify.index` | Form verifikasi QR |
| `verify()` | `GET /verifikasi/{hash}` | `verify` | Hasil verifikasi |
| `statistik()` | `GET /statistik` | `statistik` | Statistik desa |
| `storeAspirasi()` | `POST /aspirasi` | `aspirasi.store` | Kirim aspirasi |

---

### CitizenAuthController

Menangani autentikasi warga melalui web (session-based).

| Method | Rute | Nama Rute | Fungsi |
|--------|------|-----------|--------|
| `create()` | `GET /login` | `login` | Tampilkan form login |
| `store()` | `POST /login` | `login.store` | Proses login warga |
| `destroy()` | `POST /logout` | `logout` | Proses logout warga |

**Keterangan**: Menggunakan guard `penduduk` dengan autentikasi NIK + No KK.

---

### CitizenDashboardController

Menampilkan dashboard utama untuk warga yang sudah login.

| Method | Rute | Nama Rute | Fungsi |
|--------|------|-----------|--------|
| `__invoke()` | `GET /warga/dashboard` | `warga.dashboard` | Dashboard warga (single action) |

---

### CitizenProfileController

Menangani pengelolaan profil warga.

| Method | Rute | Nama Rute | Fungsi |
|--------|------|-----------|--------|
| `show()` | `GET /warga/profil` | `warga.profil.show` | Lihat profil |
| `update()` | `POST /warga/profil` | `warga.profil.update` | Perbarui profil |

---

### CitizenFamilyController

Menangani pengelolaan data keluarga warga.

| Method | Rute | Nama Rute | Fungsi |
|--------|------|-----------|--------|
| `index()` | `GET /warga/keluarga` | `warga.keluarga.index` | Lihat data keluarga |
| `update()` | `PUT /warga/keluarga/{nik}` | `warga.keluarga.update` | Perbarui data anggota keluarga |

---

### CitizenSubmissionController

Menangani pengajuan surat mandiri oleh warga melalui web.

| Method | Rute | Nama Rute | Fungsi |
|--------|------|-----------|--------|
| `create()` | `GET /warga/surat/ajukan/{kategori}` | `warga.surat.create` | Form pengajuan surat |
| `store()` | `POST /warga/surat/pengajuan` | `warga.surat.store` | Kirim pengajuan |
| `show()` | `GET /warga/pengajuan/{pengajuan}` | `warga.pengajuan.show` | Detail pengajuan |
| `print()` | `GET /warga/pengajuan/{pengajuan}/print` | `warga.pengajuan.print` | Cetak PDF surat |

---

## Catatan Penting

1. **Middleware**: Seluruh rute warga menggunakan middleware `auth:penduduk`. Rute publik tidak memerlukan autentikasi.
2. **Rate Limiting**: Login warga dan admin diterapkan rate limit 5 percobaan per menit. Aspirasi diterapkan rate limit 5 per menit. Webhook Telegram diterapkan rate limit 60 per menit.
3. **Inertia.js**: Web controllers mengembalikan respons Inertia yang dirender oleh Vue 3 di sisi klien.
4. **API Controllers**: Mengembalikan respons JSON standar untuk konsumsi API eksternal (mobile app, integrasi pihak ketiga).
