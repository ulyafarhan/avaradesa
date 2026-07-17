# Dokumentasi API - AvaraDesa

Dokumentasi ini merinci spesifikasi lengkap endpoint API, metode autentikasi, parameter request, serta contoh respons untuk platform AvaraDesa.

---

## 1. Metode Autentikasi API
AvaraDesa menggunakan **Laravel Sanctum** untuk pengelolaan sesi API:
* **Token Sesi**: Permintaan yang membutuhkan autentikasi wajib menyertakan header berikut:
  ```http
  Authorization: Bearer {token_akses_anda}
  Accept: application/json
  ```
* **Masa Berlaku**: Token dapat dicabut sewaktu-waktu melalui endpoint `/v1/auth/logout`.

---

## 2. Daftar Lengkap Endpoint API

### 2.1. Modul Autentikasi (Authentication)
* **`POST /v1/auth/login/warga`**
  * **Fungsi**: Login warga desa menggunakan NIK dan No KK.
  * **Body Parameter**: `nik` (string, 16 digit), `no_kk` (string, 16 digit).
* **`POST /v1/auth/login/admin`**
  * **Fungsi**: Login perangkat desa/administrator menggunakan username dan password.
  * **Body Parameter**: `username` (string), `password` (string).
* **`POST /v1/auth/logout`**
  * **Fungsi**: Mencabut token akses aktif. (Memerlukan Token)
* **`GET /v1/auth/profile`**
  * **Fungsi**: Mengambil data profil detail pengguna yang sedang masuk. (Memerlukan Token)
* **`POST /v1/auth/bind-telegram`**
  * **Fungsi**: Menghubungkan akun warga dengan ID Chat Telegram untuk notifikasi bot. (Memerlukan Token)
  * **Body Parameter**: `telegram_chat_id` (string).

### 2.2. Modul Pengajuan Surat (Submission)
* **`GET /v1/surat/kategori`**
  * **Fungsi**: Mengambil daftar seluruh jenis/kategori surat aktif (SKTM, SKU, Domisili, dll). (Memerlukan Token)
* **`GET /v1/surat/kategori/{id}`**
  * **Fungsi**: Mengambil skema isian dinamis (JSONB) dan berkas persyaratan dari satu kategori surat. `id` menggunakan tipe string (ULID). (Memerlukan Token)
* **`POST /v1/surat/pengajuan`**
  * **Fungsi**: Mengirimkan pengajuan surat baru beserta unggahan file prasyarat. (Memerlukan Token)
  * **Body Parameter**: `kategori_surat_id` (string, ULID), `data_isian` (array), `file_syarat` (array).
* **`GET /v1/surat/pengajuan`**
  * **Fungsi**: Melihat daftar seluruh riwayat pengajuan surat milik warga bersangkutan. (Memerlukan Token)
* **`GET /v1/surat/pengajuan/{id}`**
  * **Fungsi**: Melihat status pelacakan (tracking) detail pengajuan surat tertentu. `id` menggunakan tipe string (ULID). (Memerlukan Token)
* **`GET /v1/admin/surat/pengajuan`**
  * **Fungsi**: [Khusus Admin] Mengambil daftar semua surat masuk yang menunggu verifikasi. (Memerlukan Token Admin)
* **`POST /v1/admin/surat/pengajuan/{id}/approve`**
  * **Fungsi**: [Khusus Admin] Menyetujui pengajuan surat, otomatis men-generate QR Code TTE dan mengirim PDF ke Telegram. `id` menggunakan tipe string (ULID). (Memerlukan Token Admin)
* **`POST /v1/admin/surat/pengajuan/{id}/reject`**
  * **Fungsi**: [Khusus Admin] Menolak surat dengan memberikan alasan penolakan. `id` menggunakan tipe string (ULID). (Memerlukan Token Admin)
  * **Body Parameter**: `catatan_penolakan` (string).

### 2.3. Modul Mutasi Penduduk
* **`POST /v1/mutasi`**
  * **Fungsi**: Mengajukan peristiwa mutasi kependudukan (Kelahiran, Kematian, Pindah Masuk/Keluar). (Memerlukan Token)
* **`GET /v1/mutasi`**
  * **Fungsi**: Mengambil daftar riwayat permohonan mutasi milik warga bersangkutan. (Memerlukan Token)
* **`GET /v1/admin/mutasi`**
  * **Fungsi**: [Khusus Admin] Mengambil daftar semua mutasi masuk yang menunggu verifikasi. (Memerlukan Token Admin)
* **`POST /v1/admin/mutasi/{id}/approve`**
  * **Fungsi**: [Khusus Admin] Menyetujui pengajuan mutasi dan secara otomatis memperbarui status kependudukan warga terkait. `id` menggunakan tipe string (ULID). (Memerlukan Token Admin)
* **`POST /v1/admin/mutasi/{id}/reject`**
  * **Fungsi**: [Khusus Admin] Menolak pengajuan mutasi kependudukan. `id` menggunakan tipe string (ULID). (Memerlukan Token Admin)

### 2.4. Modul Informasi Publik (Berita)
* **`GET /v1/informasi`**
  * **Fungsi**: Mengambil daftar artikel pengumuman atau berita desa yang sudah terbit. (Terbuka untuk Umum)
* **`GET /v1/informasi/{slug}`**
  * **Fungsi**: Mengambil konten detail berita berdasarkan parameter slug. (Terbuka untuk Umum)
  * **Catatan Respons**: Kolom `cover_image` dikembalikan sebagai URL gambar lengkap dan dinamis (misalnya `http://127.0.0.1:8000/storage/informasi/nama-file.webp` atau URL eksternal) sesuai dengan domain host aktif. Jika artikel tidak memiliki cover kustom, properti ini bernilai `null` (aplikasi klien dapat menggunakan fallback gambar default).
* **`GET /v1/admin/informasi`**
  * **Fungsi**: [Khusus Admin] Mengambil semua draf maupun berita terbit. (Memerlukan Token Admin)
* **`POST /v1/admin/informasi`**
  * **Fungsi**: [Khusus Admin] Membuat artikel berita baru. (Memerlukan Token Admin)
* **`PUT /v1/admin/informasi/{id}`**
  * **Fungsi**: [Khusus Admin] Memperbarui isi artikel berita. `id` menggunakan tipe string (ULID). (Memerlukan Token Admin)
* **`DELETE /v1/admin/informasi/{id}`**
  * **Fungsi**: [Khusus Admin] Menghapus berita dari database. `id` menggunakan tipe string (ULID). (Memerlukan Token Admin)

### 2.5. Modul Statistik Desa
* **`GET /v1/statistik/demografi`**
  * **Fungsi**: Mengambil data kompilasi kependudukan desa secara real-time (di-cache, berlaku 30 menit). (Terbuka untuk Umum)
* **`GET /v1/statistik/layanan`**
  * **Fungsi**: Mengambil data statistik pengajuan pelayanan surat menyurat. (Terbuka untuk Umum)
* **`POST /v1/admin/statistik/clear-cache`**
  * **Fungsi**: [Khusus Admin] Memaksa pembersihan cache statistik pada Redis. (Memerlukan Token Admin)

### 2.6. Modul Verifikasi QR & Telegram Webhook
* **`GET /v1/verifikasi/{hash}`**
  * **Fungsi**: Validasi dokumen fisik melalui hash SHA-256 QR Code TTE untuk menampilkan keaslian isi surat. (Terbuka untuk Umum)
* **`POST /v1/telegram/webhook`**
  * **Fungsi**: Endpoint penangkap webhook pesan masuk Telegram Bot untuk dihubungkan ke asisten AI (Gemini/OpenAI). (Terbuka untuk Umum / Telegram IP)

### 2.7. Format Respons Terstruktur
Seluruh endpoint API Resource menyertakan *_formatted fields untuk memudahkan rendering di sisi klien. Contoh:
* **`tanggal_lahir_formatted`**: Format tanggal lokal Indonesia (dd/mm/yyyy).
* **`nama_lengkap_formatted`**: Nama dengan kapitalisasi yang telah diformat.
* **`created_at_formatted`**, **`updated_at_formatted`**: Timestamp dalam format manusiawi.
* Field ini otomatis disertakan oleh API Resource Layer Laravel dan tidak perlu dihitung ulang oleh klien.

---

## 3. Integrasi & Pengujian API
* **Koleksi Postman**: Tersedia di direktori `storage/app/private/scribe/collection.json` untuk diimpor langsung ke Postman Desktop.
* **Spesifikasi OpenAPI**: File spesifikasi kontrak standar YAML berada di `docs/api-contract.yaml` (dapat diimpor ke Swagger Editor).
