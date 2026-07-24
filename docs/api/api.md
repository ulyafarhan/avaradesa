# Dokumentasi API - AvaraDesa

Dokumentasi ini merinci spesifikasi lengkap endpoint API (60+ endpoint), metode autentikasi, parameter request, serta contoh respons untuk platform AvaraDesa.

---

## 1. Metode Autentikasi API

AvaraDesa menggunakan **Laravel Sanctum** untuk pengelolaan sesi API:

* **Token Sesi**: Permintaan yang membutuhkan autentikasi wajib menyertakan header berikut:
  ```http
  Authorization: Bearer {token_akses_anda}
  Accept: application/json
  ```
* **Level Akses**:
  - **Publik**: Tanpa token
  - **Bearer Token (Warga)**: Token dengan ability `warga`
  - **Bearer Token (Admin)**: Token dengan ability `admin`
* **Masa Berlaku**: Token dapat dicabut sewaktu-waktu melalui endpoint `/v1/auth/logout`.
* **Throttle**: Beberapa endpoint memiliki batas rate limit. Jika terlampaui, server mengembalikan `429 Too Many Requests`.

---

## 2. Daftar Lengkap Endpoint API

---

### 2.1. Modul Autentikasi (Authentication)

#### `POST /v1/auth/login/warga`
- **Fungsi**: Login warga desa menggunakan NIK dan No KK.
- **Auth**: Public
- **Throttle**: `login` (5 kali per menit)
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `nik` | string | Ya | NIK warga (16 digit) |
  | `no_kk` | string | Ya | Nomor KK (16 digit) |
- **Response (200)**:
  ```json
  {
    "message": "Login berhasil",
    "user": { "nik": "1118060512900001", "nama_lengkap": "Ahmad Fauzi", ... },
    "token": "1|abc123...",
    "has_pin": false,
    "has_biometric": false
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/auth/login/warga', {
    nik: '1118060512900001',
    no_kk: '1118061208900001'
  });
  const token = response.data.token;
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::post('/v1/auth/login/warga', [
    'nik' => '1118060512900001',
    'no_kk' => '1118061208900001',
  ]);
  $token = $response->json('token');
  ```

#### `POST /v1/auth/login/admin`
- **Fungsi**: Login perangkat desa/administrator menggunakan username dan password.
- **Auth**: Public
- **Throttle**: `login` (5 kali per menit)
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `username` | string | Ya | Username admin |
  | `password` | string | Ya | Password admin |
- **Response (200)**:
  ```json
  {
    "message": "Login berhasil",
    "user": { "id": 1, "username": "admin_desa", ... },
    "token": "1|xyz789..."
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/auth/login/admin', {
    username: 'admin_desa',
    password: '********'
  });
  const token = response.data.token;
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::post('/v1/auth/login/admin', [
    'username' => 'admin_desa',
    'password' => '********',
  ]);
  $token = $response->json('token');
  ```

#### `POST /v1/auth/register-pin`
- **Fungsi**: Mendaftarkan PIN 6-digit untuk warga (aktivasi pertama kali).
- **Auth**: Public
- **Throttle**: 5 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `nik` | string | Ya | NIK warga (16 digit) |
  | `no_kk` | string | Ya | Nomor KK (16 digit) |
  | `pin` | string | Ya | PIN 6 digit |
  | `pin_confirmation` | string | Ya | Konfirmasi PIN |
- **Response (200)**:
  ```json
  {
    "message": "PIN 6-digit berhasil didaftarkan! Anda sekarang dapat login menggunakan NIK + PIN."
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post('/v1/auth/register-pin', {
    nik: '1118060512900001',
    no_kk: '1118061208900001',
    pin: '123456',
    pin_confirmation: '123456'
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::post('/v1/auth/register-pin', [
    'nik' => '1118060512900001',
    'no_kk' => '1118061208900001',
    'pin' => '123456',
    'pin_confirmation' => '123456',
  ]);
  ```

#### `POST /v1/auth/login-pin`
- **Fungsi**: Login cepat warga menggunakan NIK + PIN 6-digit dengan proteksi lockout (5x salah → kunci 15 menit).
- **Auth**: Public
- **Throttle**: 5 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `nik` | string | Ya | NIK warga (16 digit) |
  | `pin` | string | Ya | PIN 6 digit |
- **Response (200)**:
  ```json
  {
    "message": "Login PIN berhasil",
    "user": { "nik": "1118060512900001", ... },
    "token": "2|def456...",
    "has_pin": true,
    "has_biometric": false
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/auth/login-pin', {
    nik: '1118060512900001',
    pin: '123456'
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::post('/v1/auth/login-pin', [
    'nik' => '1118060512900001',
    'pin' => '123456',
  ]);
  ```

#### `POST /v1/auth/login-biometric`
- **Fungsi**: Login instan warga menggunakan kunci biometrik (sidik jari / FaceID).
- **Auth**: Public
- **Throttle**: 5 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `nik` | string | Ya | NIK warga (16 digit) |
  | `biometric_key` | string | Ya | Kunci biometrik dari perangkat |
- **Response (200)**:
  ```json
  {
    "message": "Login Sidik Jari Berhasil",
    "user": { "nik": "1118060512900001", ... },
    "token": "3|ghi012...",
    "has_pin": true,
    "has_biometric": true
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/auth/login-biometric', {
    nik: '1118060512900001',
    biometric_key: 'device_biometric_hash_xxx'
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::post('/v1/auth/login-biometric', [
    'nik' => '1118060512900001',
    'biometric_key' => 'device_biometric_hash_xxx',
  ]);
  ```

#### `POST /v1/auth/reset-pin`
- **Fungsi**: Mereset PIN dengan verifikasi ulang NIK + No KK.
- **Auth**: Public
- **Throttle**: 3 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `nik` | string | Ya | NIK warga (16 digit) |
  | `no_kk` | string | Ya | Nomor KK (16 digit) |
  | `pin` | string | Ya | PIN baru (6 digit) |
  | `pin_confirmation` | string | Ya | Konfirmasi PIN baru |
- **Response (200)**:
  ```json
  {
    "message": "PIN berhasil di-reset"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post('/v1/auth/reset-pin', {
    nik: '1118060512900001',
    no_kk: '1118061208900001',
    pin: '654321',
    pin_confirmation: '654321'
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::post('/v1/auth/reset-pin', [
    'nik' => '1118060512900001',
    'no_kk' => '1118061208900001',
    'pin' => '654321',
    'pin_confirmation' => '654321',
  ]);
  ```

#### `POST /v1/auth/logout`
- **Fungsi**: Mencabut token akses aktif.
- **Auth**: Bearer Token (Warga/Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "message": "Logout berhasil"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post('/v1/auth/logout', {}, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->post('/v1/auth/logout');
  ```

#### `GET /v1/auth/profile`
- **Fungsi**: Mengambil data profil detail pengguna yang sedang login.
- **Auth**: Bearer Token (Warga/Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "user": {
      "nik": "1118060512900001",
      "nama_lengkap": "Ahmad Fauzi",
      "tempat_lahir": "Jakarta",
      "tanggal_lahir": "1990-05-12",
      "jenis_kelamin": "L",
      "agama": "Islam",
      "pendidikan": "SMA",
      "pekerjaan": "Petani",
      "status_perkawinan": "Kawin",
      "no_kk": "1118061208900001",
      "keluarga": { "no_kk": "1118061208900001", "alamat": "Jl. Merdeka No. 10", ... }
    },
    "has_pin": true,
    "has_biometric": false
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/auth/profile', {
    headers: { Authorization: `Bearer ${token}` }
  });
  console.log(response.data.user);
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/auth/profile');
  $user = $response->json('user');
  ```

#### `POST /v1/auth/bind-telegram`
- **Fungsi**: Menghubungkan akun warga dengan ID Chat Telegram untuk notifikasi bot.
- **Auth**: Bearer Token (Warga)
- **Throttle**: 5 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `telegram_chat_id` | string | Ya | ID Chat Telegram |
- **Response (200)**:
  ```json
  {
    "message": "Telegram berhasil terhubung"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post('/v1/auth/bind-telegram', {
    telegram_chat_id: '123456789'
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->post('/v1/auth/bind-telegram', [
    'telegram_chat_id' => '123456789',
  ]);
  ```

#### `POST /v1/auth/register-biometric`
- **Fungsi**: Mendaftarkan kunci biometrik (sidik jari / TouchID / FaceID) dari perangkat warga.
- **Auth**: Bearer Token (Warga)
- **Throttle**: 5 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `biometric_key` | string | Ya | Kunci biometrik dari perangkat (min 16 karakter) |
- **Response (200)**:
  ```json
  {
    "message": "Sidik jari berhasil dihubungkan dengan akun Anda"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post('/v1/auth/register-biometric', {
    biometric_key: 'device_generated_hash_xxxxxxxx'
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->post('/v1/auth/register-biometric', [
    'biometric_key' => 'device_generated_hash_xxxxxxxx',
  ]);
  ```

---

### 2.2. Modul Dashboard

#### `GET /v1/dashboard`
- **Fungsi**: Mengambil data dashboard warga termasuk profil, kelengkapan biodata, status keluarga, dan ringkasan pengajuan surat.
- **Auth**: Bearer Token (Warga)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "warga": { "nik": "1118060512900001", "nama_lengkap": "Ahmad Fauzi", ... },
    "summary": {
      "pending": 2,
      "diproses": 1,
      "selesai": 5
    },
    "biodataComplete": true,
    "biodataCompleteness": 100,
    "isKepalaKeluarga": true,
    "anggotaKeluarga": [
      { "nik": "1118060512900001", "nama_lengkap": "Ahmad Fauzi", "status_keluarga": "KEPALA KELUARGA", "umur": 36 }
    ]
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/dashboard', {
    headers: { Authorization: `Bearer ${token}` }
  });
  const { summary, biodataComplete, anggotaKeluarga } = response.data;
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/dashboard');
  $summary = $response->json('summary');
  ```

---

### 2.3. Modul Informasi Publik (Berita)

#### `GET /v1/informasi`
- **Fungsi**: Mengambil daftar artikel pengumuman atau berita desa yang sudah terbit.
- **Auth**: Public
- **Throttle**: -
- **Query Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `kategori` | string | Tidak | Filter berdasarkan kategori (Pengumuman, Berita, Kegiatan) |
- **Response (200)**:
  ```json
  {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "judul": "Pengumuman Pemilihan Kepala Desa 2026",
        "slug": "pengumuman-pemilihan-kepala-desa-2026",
        "konten": "Diumumkan kepada seluruh warga...",
        "kategori": "Pengumuman",
        "cover_image": "http://127.0.0.1:8000/storage/informasi/cover.webp",
        "author": { "id": 1, "nama_lengkap": "Administrator Desa" }
      }
    ],
    "per_page": 10,
    "total": 5
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/informasi', {
    params: { kategori: 'Pengumuman' }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::get('/v1/informasi', ['kategori' => 'Pengumuman']);
  ```

#### `GET /v1/informasi/{slug}`
- **Fungsi**: Mengambil konten detail berita berdasarkan parameter slug.
- **Auth**: Public
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "data": {
      "id": 1,
      "judul": "Pengumuman Pemilihan Kepala Desa 2026",
      "slug": "pengumuman-pemilihan-kepala-desa-2026",
      "konten": "Diumumkan kepada seluruh warga...",
      "kategori": "Pengumuman",
      "cover_image": "http://127.0.0.1:8000/storage/informasi/cover.webp",
      "is_published": true,
      "author": { "id": 1, "nama_lengkap": "Administrator Desa" }
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get(`/v1/informasi/${slug}`);
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::get("/v1/informasi/{$slug}");
  ```

---

### 2.4. Modul Statistik Desa

#### `GET /v1/statistik/demografi`
- **Fungsi**: Mengambil data kompilasi kependudukan desa secara real-time (di-cache, berlaku 30 menit).
- **Auth**: Public
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "total_penduduk": 1250,
    "total_kk": 340,
    "jenis_kelamin": { "L": 620, "P": 630 },
    "agama": { "Islam": 1200, "Kristen": 30, ... },
    "pendidikan": { "Tidak Sekolah": 50, "SD": 400, ... },
    "pekerjaan": { "Petani": 300, "PNS": 45, ... },
    "golongan_darah": { "A": 100, "B": 150, ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/statistik/demografi');
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::get('/v1/statistik/demografi');
  ```

#### `GET /v1/statistik/layanan`
- **Fungsi**: Mengambil data statistik pengajuan pelayanan surat-menyurat.
- **Auth**: Public
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "total_pengajuan": 250,
    "status": { "pending": 15, "diproses": 10, "disetujui": 200, "ditolak": 25 },
    "per_kategori": [
      { "kategori": "SKTM", "total": 80 },
      { "kategori": "Domisili", "total": 60 }
    ],
    "periode": { "bulan_ini": 20, "tahun_ini": 120 }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/statistik/layanan');
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::get('/v1/statistik/layanan');
  ```

---

### 2.5. Modul Pengajuan Surat

#### `GET /v1/surat/kategori`
- **Fungsi**: Mengambil daftar seluruh jenis/kategori surat aktif (SKTM, SKU, Domisili, dll).
- **Auth**: Bearer Token (Warga)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "data": [
      { "id": 1, "nama_surat": "Surat Keterangan Domisili", "deskripsi": "Surat keterangan tempat tinggal warga", "status": "active" }
    ]
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/surat/kategori', {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/surat/kategori');
  ```

#### `GET /v1/surat/kategori/{id}`
- **Fungsi**: Mengambil skema isian dinamis (JSONB) dan berkas persyaratan dari satu kategori surat.
- **Auth**: Bearer Token (Warga)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "data": {
      "id": 1,
      "nama_surat": "Surat Keterangan Domisili",
      "deskripsi": "Surat keterangan tempat tinggal warga",
      "skema_data": { "alamat": { "type": "text", "label": "Alamat Lengkap" } },
      "syarat": ["Foto KTP", "Foto KK"]
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get(`/v1/surat/kategori/${id}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get("/v1/surat/kategori/{$id}");
  ```

#### `POST /v1/surat/pengajuan`
- **Fungsi**: Mengirimkan pengajuan surat baru beserta unggahan file prasyarat.
- **Auth**: Bearer Token (Warga)
- **Throttle**: 5 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `kategori_surat_id` | integer | Ya | ID kategori surat |
  | `data_isian` | array | Ya | Data isian formulir sesuai skema kategori |
  | `file_syarat` | array | Tidak | Daftar file syarat (file upload atau string path) |
- **Response (201)**:
  ```json
  {
    "message": "Pengajuan surat berhasil dibuat",
    "data": {
      "id": 1,
      "nik_pemohon": "1118060512900001",
      "nomor_registrasi": "REG/2026/0001",
      "status": "Pending",
      "data_isian": { "alamat": "Jl. Merdeka No. 10" },
      "file_syarat": ["submissions/documents/ktp.jpg"],
      "kategori": { "id": 1, "nama_surat": "Surat Keterangan Domisili" }
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const formData = new FormData();
  formData.append('kategori_surat_id', 1);
  formData.append('data_isian[alamat]', 'Jl. Merdeka No. 10');
  formData.append('file_syarat[]', fileInput.files[0]);

  const response = await axios.post('/v1/surat/pengajuan', formData, {
    headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'multipart/form-data' }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->attach('file_syarat[]', fopen('ktp.jpg', 'r'))
    ->post('/v1/surat/pengajuan', [
      'kategori_surat_id' => 1,
      'data_isian' => ['alamat' => 'Jl. Merdeka No. 10'],
    ]);
  ```

#### `GET /v1/surat/pengajuan`
- **Fungsi**: Melihat daftar seluruh riwayat pengajuan surat milik warga bersangkutan.
- **Auth**: Bearer Token (Warga)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "nik_pemohon": "1118060512900001",
        "nomor_registrasi": "REG/2026/0001",
        "status": "Disetujui",
        "kategori": { "id": 1, "nama_surat": "Surat Keterangan Domisili" },
        "tracking": [
          { "status_baru": "Pending", "keterangan_update": "Pengajuan surat dibuat" },
          { "status_baru": "Disetujui", "keterangan_update": "Pengajuan disetujui oleh admin" }
        ]
      }
    ],
    "per_page": 10,
    "total": 5
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/surat/pengajuan', {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/surat/pengajuan');
  ```

#### `GET /v1/surat/pengajuan/{id}`
- **Fungsi**: Melihat status pelacakan (tracking) detail pengajuan surat tertentu.
- **Auth**: Bearer Token (Warga/Admin)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "data": {
      "id": 1,
      "nik_pemohon": "1118060512900001",
      "nomor_registrasi": "REG/2026/0001",
      "status": "Disetujui",
      "pemohon": { "nik": "1118060512900001", "nama_lengkap": "Ahmad Fauzi" },
      "kategori": { "id": 1, "nama_surat": "Surat Keterangan Domisili" },
      "tracking": [
        { "status_sebelumnya": null, "status_baru": "Pending", "keterangan_update": "Pengajuan surat dibuat" }
      ]
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get(`/v1/surat/pengajuan/${id}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get("/v1/surat/pengajuan/{$id}");
  ```

#### `GET /v1/surat/pengajuan/{id}/download`
- **Fungsi**: Mengunduh berkas PDF dari surat yang telah disetujui.
- **Auth**: Bearer Token (Warga/Admin)
- **Throttle**: 30 kali per 1 menit
- **Response**: File download (`application/pdf`)
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get(`/v1/surat/pengajuan/${id}/download`, {
    headers: { Authorization: `Bearer ${token}` },
    responseType: 'blob'
  });
  const url = window.URL.createObjectURL(new Blob([response.data]));
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', `Surat_${id}.pdf`);
  document.body.appendChild(link);
  link.click();
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get("/v1/surat/pengajuan/{$id}/download");
  Storage::put('surat_download.pdf', $response->body());
  ```

---

### 2.6. Modul Mutasi Penduduk

#### `POST /v1/mutasi`
- **Fungsi**: Mengajukan peristiwa mutasi kependudukan (Kelahiran, Kematian, Pindah Masuk/Keluar).
- **Auth**: Bearer Token (Warga)
- **Throttle**: 3 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `jenis_mutasi` | string | Ya | Jenis mutasi (Kelahiran, Kematian, Pindah_Masuk, Pindah_Keluar) |
  | `tanggal_mutasi` | date | Ya | Tanggal kejadian mutasi |
  | `keterangan` | string | Tidak | Keterangan/alasan mutasi |
- **Response (201)**:
  ```json
  {
    "message": "Pengajuan mutasi berhasil dibuat",
    "data": {
      "id": 1,
      "nik": "1118060512900001",
      "jenis_mutasi": "Kelahiran",
      "tanggal_mutasi": "2026-07-15",
      "keterangan": "Anak pertama",
      "status_verifikasi": "Pending"
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/mutasi', {
    jenis_mutasi: 'Kelahiran',
    tanggal_mutasi: '2026-07-15',
    keterangan: 'Anak pertama'
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->post('/v1/mutasi', [
    'jenis_mutasi' => 'Kelahiran',
    'tanggal_mutasi' => '2026-07-15',
    'keterangan' => 'Anak pertama',
  ]);
  ```

#### `GET /v1/mutasi`
- **Fungsi**: Mengambil daftar riwayat permohonan mutasi milik warga bersangkutan.
- **Auth**: Bearer Token (Warga)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "data": [
      {
        "id": 1,
        "nik": "1118060512900001",
        "jenis_mutasi": "Kelahiran",
        "tanggal_mutasi": "2026-07-15",
        "status_verifikasi": "Pending"
      }
    ]
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/mutasi', {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/mutasi');
  ```

---

### 2.7. Modul Chatbot

#### `POST /v1/chat`
- **Fungsi**: Mengirim pesan ke asisten AI desa (Gemini/OpenAI) untuk bertanya seputar layanan desa.
- **Auth**: Bearer Token (Warga/Admin)
- **Throttle**: 10 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `message` | string | Ya | Teks pertanyaan (1-2000 karakter) |
  | `session_id` | string | Tidak | ID sesi percakapan (opsional) |
- **Response (200)**:
  ```json
  {
    "status": "success",
    "data": {
      "response": "Untuk membuat Surat Keterangan Domisili, Anda perlu menyiapkan: 1) Foto KTP, 2) Foto KK. Silakan datang ke kantor desa atau ajukan via aplikasi.",
      "session_id": "chat_abc123"
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/chat', {
    message: 'Bagaimana cara buat SKTM?',
    session_id: 'user-session-001'
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  console.log(response.data.data.response);
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->post('/v1/chat', [
    'message' => 'Bagaimana cara buat SKTM?',
    'session_id' => 'user-session-001',
  ]);
  echo $response->json('data.response');
  ```

---

### 2.8. Modul Sinkronisasi (Sync)

#### `POST /v1/sync/push`
- **Fungsi**: Mengirim operasi offline dari perangkat warga ke server (sinkronisasi data saat jaringan tersedia).
- **Auth**: Bearer Token (Warga)
- **Throttle**: 10 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `operations` | array | Ya | Daftar operasi offline yang dikirim |
  | `operations.*.client_id` | string | Ya | UUID unik operasi dari klien |
  | `operations.*.type` | string | Ya | Tipe data: `pengajuan_surat` atau `mutasi` |
  | `operations.*.action` | string | Ya | Aksi: `create` |
  | `operations.*.data` | object | Ya | Data operasi |
  | `operations.*.created_at` | string | Ya | Timestamp ISO8601 |
- **Response (200)**:
  ```json
  {
    "status": "processed",
    "results": [
      { "client_id": "550e8400-e29b-41d4-a716-446655440000", "status": "success", "server_id": 42 }
    ],
    "server_sync_token": "2026-07-14T10:00:00Z"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/sync/push', {
    operations: [
      {
        client_id: '550e8400-e29b-41d4-a716-446655440000',
        type: 'pengajuan_surat',
        action: 'create',
        data: { kategori_surat_id: 1, data_isian: { alamat: 'Jl. Merdeka' } },
        created_at: '2026-07-14T10:00:00Z'
      }
    ]
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->post('/v1/sync/push', [
    'operations' => [
      [
        'client_id' => '550e8400-e29b-41d4-a716-446655440000',
        'type' => 'pengajuan_surat',
        'action' => 'create',
        'data' => ['kategori_surat_id' => 1],
        'created_at' => '2026-07-14T10:00:00Z',
      ],
    ],
  ]);
  ```

#### `GET /v1/sync/pull`
- **Fungsi**: Mengambil perubahan data sejak timestamp terakhir sinkronisasi.
- **Auth**: Bearer Token (Warga)
- **Throttle**: 30 kali per 1 menit
- **Query Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `since` | string | Ya | Timestamp ISO8601 sinkronisasi terakhir (format: `Y-m-d\TH:i:s\Z`) |
- **Response (200)**:
  ```json
  {
    "data": {
      "pengajuan_surat": { "updated": [...], "deleted": [] },
      "mutasi": { "updated": [...], "deleted": [] },
      "penduduk": { "nik": "1118060512900001", "nama_lengkap": "Ahmad Fauzi", ... }
    },
    "meta": { "sync_token": "2026-07-14T10:30:00Z" }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/sync/pull', {
    params: { since: '2026-07-13T10:00:00Z' },
    headers: { Authorization: `Bearer ${token}` }
  });
  const { meta } = response.data;
  // Simpan meta.sync_token untuk pull berikutnya
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/sync/pull', [
    'since' => '2026-07-13T10:00:00Z',
  ]);
  $syncToken = $response->json('meta.sync_token');
  ```

---

### 2.9. Modul Verifikasi QR

#### `GET /v1/verifikasi/{hash}`
- **Fungsi**: Validasi dokumen fisik melalui hash SHA-256 QR Code TTE untuk menampilkan keaslian isi surat.
- **Auth**: Public
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "valid": true,
    "data": {
      "nomor_registrasi": "REG/2026/0001",
      "nik_pemohon": "1118060512900001",
      "nama_pemohon": "Ahmad Fauzi",
      "jenis_surat": "Surat Keterangan Domisili",
      "status": "Disetujui",
      "diterbitkan": "2026-07-14T09:00:00Z"
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get(`/v1/verifikasi/${hash}`);
  if (response.data.valid) {
    console.log('Dokumen asli:', response.data.data);
  }
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::get("/v1/verifikasi/{$hash}");
  $valid = $response->json('valid');
  ```

---

### 2.10. Modul Webhook

#### `POST /v1/telegram/webhook`
- **Fungsi**: Endpoint penangkap webhook pesan masuk Telegram Bot untuk dihubungkan ke asisten AI.
- **Auth**: Public (IP Telegram)
- **Throttle**: 60 kali per 1 menit
- **Body Parameter**: (payload dari Telegram API)
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `message` | object | Tidak | Data pesan dari Telegram |
  | `message.chat.id` | integer | Ya | ID chat Telegram pengguna |
  | `message.text` | string | Ya | Teks pesan dari pengguna |
  | `callback_query` | object | Tidak | Data callback dari tombol inline |
- **Response (200)**:
  ```json
  {
    "ok": true
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  // Untuk testing webhook:
  await axios.post('/v1/telegram/webhook', {
    message: {
      chat: { id: 123456789 },
      text: '/start'
    }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::post('/v1/telegram/webhook', [
    'message' => [
      'chat' => ['id' => 123456789],
      'text' => '/start',
    ],
  ]);
  ```

#### `POST /v1/whatsapp/webhook`
- **Fungsi**: Endpoint penangkap webhook pesan masuk dari WhatsApp Gateway.
- **Auth**: Public
- **Throttle**: 60 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `data` | object | Tidak | Data payload dari WhatsApp gateway |
  | `data.message.from` | string | Ya | Nomor pengirim |
  | `data.message.text.body` | string | Ya | Isi pesan teks |
- **Response (200)**:
  ```json
  {
    "ok": true
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post('/v1/whatsapp/webhook', {
    data: {
      message: {
        from: '628123456789',
        text: { body: 'Halo desa, saya ingin bertanya' }
      }
    }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::post('/v1/whatsapp/webhook', [
    'data' => [
      'message' => [
        'from' => '628123456789',
        'text' => ['body' => 'Halo desa'],
      ],
    ],
  ]);
  ```

---

### 2.11. Modul Gateway Sync (wa-gateway)

#### `GET /v1/gateway/sync`
- **Fungsi**: Endpoint yang dipanggil wa-gateway tiap 5 menit untuk sinkronisasi data — FAQ, Knowledge Base, Kategori Surat, dan Template Notifikasi.
- **Auth**: `X-API-Key` header (diverifikasi oleh `VerifyGatewayApiKey` middleware)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "faq": [...],
    "kategori_surat": [...],
    "template_notifikasi": {...}
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/gateway/sync', {
    headers: { 'X-API-Key': 'your-gateway-key' }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withHeaders(['X-API-Key' => $apiKey])->get('/v1/gateway/sync');
  ```

---

### 2.12. Modul Admin - Manajemen Penduduk

#### `GET /v1/admin/penduduk`
- **Fungsi**: [Khusus Admin] Mengambil daftar seluruh data penduduk dengan pencarian dan filter.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Query Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `search` | string | Tidak | Pencarian NIK, Nama, atau No KK |
  | `status` | string | Tidak | Filter status mutasi |
  | `per_page` | integer | Tidak | Jumlah item per halaman (default: 15) |
- **Response (200)**:
  ```json
  {
    "data": [
      { "nik": "1118060512900001", "nama_lengkap": "Ahmad Fauzi", "no_kk": "1118061208900001", "status_mutasi": "Tetap", ... }
    ],
    "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 72 }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/admin/penduduk', {
    params: { search: 'Ahmad', status: 'Tetap', per_page: 20 },
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/admin/penduduk', [
    'search' => 'Ahmad',
    'per_page' => 20,
  ]);
  ```

#### `GET /v1/admin/penduduk/{id}`
- **Fungsi**: [Khusus Admin] Menampilkan detail satu data penduduk.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "data": {
      "id": 1,
      "nik": "1118060512900001",
      "nama_lengkap": "Ahmad Fauzi",
      "no_kk": "1118061208900001",
      "tempat_lahir": "Jakarta",
      "tanggal_lahir": "1990-05-12",
      "jenis_kelamin": "L",
      "agama": "Islam",
      "pendidikan": "SMA",
      "pekerjaan": "Petani",
      "status_perkawinan": "Kawin",
      "status_keluarga": "KEPALA KELUARGA",
      "status_mutasi": "Tetap"
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get(`/v1/admin/penduduk/${id}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get("/v1/admin/penduduk/{$id}");
  ```

#### `POST /v1/admin/penduduk`
- **Fungsi**: [Khusus Admin] Menambahkan data penduduk baru.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `nik` | string | Ya | NIK (16 digit, unique) |
  | `no_kk` | string | Ya | Nomor KK (16 digit) |
  | `nama_lengkap` | string | Ya | Nama lengkap |
  | `jenis_kelamin` | string | Ya | L / P |
  | `tempat_lahir` | string | Ya | Tempat lahir |
  | `tanggal_lahir` | date | Ya | Tanggal lahir |
  | `agama` | string | Ya | Agama |
  | `pendidikan` | string | Ya | Pendidikan terakhir |
  | `pekerjaan` | string | Ya | Pekerjaan |
  | `status_perkawinan` | string | Ya | Status perkawinan |
  | `status_keluarga` | string | Ya | Status dalam keluarga |
  | `status_mutasi` | string | Ya | Status mutasi |
- **Response (201)**:
  ```json
  {
    "message": "Data penduduk berhasil ditambahkan",
    "data": { "nik": "1118060512900002", ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/admin/penduduk', {
    nik: '1118060512900002',
    no_kk: '1118061208900001',
    nama_lengkap: 'Siti Nurhaliza',
    jenis_kelamin: 'P',
    tempat_lahir: 'Jakarta',
    tanggal_lahir: '1995-08-17',
    agama: 'Islam',
    pendidikan: 'S1',
    pekerjaan: 'Guru',
    status_perkawinan: 'Kawin',
    status_keluarga: 'ISTRI',
    status_mutasi: 'Tetap'
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->post('/v1/admin/penduduk', [
    'nik' => '1118060512900002',
    'no_kk' => '1118061208900001',
    'nama_lengkap' => 'Siti Nurhaliza',
    'jenis_kelamin' => 'P',
    'tempat_lahir' => 'Jakarta',
    'tanggal_lahir' => '1995-08-17',
    'agama' => 'Islam',
    'pendidikan' => 'S1',
    'pekerjaan' => 'Guru',
    'status_perkawinan' => 'Kawin',
    'status_keluarga' => 'ISTRI',
    'status_mutasi' => 'Tetap',
  ]);
  ```

#### `PUT /v1/admin/penduduk/{id}`
- **Fungsi**: [Khusus Admin] Memperbarui data penduduk.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Body Parameter**: (sama seperti POST, semua field required)
- **Response (200)**:
  ```json
  {
    "message": "Data penduduk berhasil diperbarui",
    "data": { ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.put(`/v1/admin/penduduk/${id}`, {
    nik: '1118060512900002',
    nama_lengkap: 'Siti Nurhaliza Updated',
    ...
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->put("/v1/admin/penduduk/{$id}", [
    'nik' => '1118060512900002',
    'nama_lengkap' => 'Siti Nurhaliza Updated',
    ...
  ]);
  ```

#### `DELETE /v1/admin/penduduk/{id}`
- **Fungsi**: [Khusus Admin] Menghapus data penduduk (beserta akun user terkait jika ada).
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "message": "Data penduduk berhasil dihapus"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.delete(`/v1/admin/penduduk/${id}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->delete("/v1/admin/penduduk/{$id}");
  ```

---

### 2.13. Modul Admin - Manajemen Keluarga

#### `GET /v1/admin/keluarga`
- **Fungsi**: [Khusus Admin] Mengambil daftar seluruh Kartu Keluarga dengan pencarian.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Query Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `search` | string | Tidak | Pencarian No KK, Alamat, RT/RW |
  | `per_page` | integer | Tidak | Jumlah item per halaman (default: 15) |
- **Response (200)**:
  ```json
  {
    "data": [
      {
        "no_kk": "1118061208900001",
        "alamat": "Jl. Merdeka No. 10",
        "rt": "001",
        "rw": "002",
        "dusun": "Dusun Makmur",
        "kepala_keluarga": { "nik": "1118060512900001", "nama_lengkap": "Ahmad Fauzi" }
      }
    ],
    "meta": { "current_page": 1, "last_page": 3, "per_page": 15, "total": 35 }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/admin/keluarga', {
    params: { search: 'Merdeka', per_page: 20 },
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/admin/keluarga', [
    'search' => 'Merdeka',
  ]);
  ```

#### `POST /v1/admin/keluarga`
- **Fungsi**: [Khusus Admin] Membuat data Kartu Keluarga baru.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `no_kk` | string | Ya | Nomor KK (16 digit, unique) |
  | `alamat` | string | Ya | Alamat lengkap |
  | `rt` | string | Tidak | RT |
  | `rw` | string | Tidak | RW |
  | `dusun` | string | Tidak | Dusun |
- **Response (201)**:
  ```json
  {
    "message": "Kartu Keluarga berhasil dibuat",
    "data": { "no_kk": "1118061208900002", "alamat": "Jl. Baru No. 5", ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/admin/keluarga', {
    no_kk: '1118061208900002',
    alamat: 'Jl. Baru No. 5',
    rt: '003',
    rw: '001',
    dusun: 'Dusun Sejahtera'
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->post('/v1/admin/keluarga', [
    'no_kk' => '1118061208900002',
    'alamat' => 'Jl. Baru No. 5',
    'rt' => '003',
    'rw' => '001',
    'dusun' => 'Dusun Sejahtera',
  ]);
  ```

#### `DELETE /v1/admin/keluarga/{no_kk}`
- **Fungsi**: [Khusus Admin] Menghapus data Kartu Keluarga berdasarkan No KK.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "message": "Data KK berhasil dihapus"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.delete(`/v1/admin/keluarga/${no_kk}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->delete("/v1/admin/keluarga/{$no_kk}");
  ```

---

### 2.14. Modul Admin - Manajemen Kategori Surat

#### `GET /v1/admin/kategori-surat`
- **Fungsi**: [Khusus Admin] Mengambil daftar seluruh kategori surat.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "data": [
      { "id": 1, "kode_surat": "SKTM", "nama_surat": "Surat Keterangan Tidak Mampu", "is_active": true, "syarat_dokumen": ["Foto KTP"] }
    ]
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/admin/kategori-surat', {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/admin/kategori-surat');
  ```

#### `POST /v1/admin/kategori-surat`
- **Fungsi**: [Khusus Admin] Membuat kategori surat baru.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `kode_surat` | string | Ya | Kode unik surat |
  | `nama_surat` | string | Ya | Nama surat |
  | `syarat_dokumen` | array | Tidak | Daftar syarat dokumen |
  | `is_active` | boolean | Tidak | Status aktif (default: false) |
- **Response (201)**:
  ```json
  {
    "message": "Kategori surat berhasil dibuat",
    "data": { "id": 2, "kode_surat": "SKU", "nama_surat": "Surat Keterangan Usaha", ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/admin/kategori-surat', {
    kode_surat: 'SKU',
    nama_surat: 'Surat Keterangan Usaha',
    syarat_dokumen: ['Foto KTP', 'Foto Usaha'],
    is_active: true
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->post('/v1/admin/kategori-surat', [
    'kode_surat' => 'SKU',
    'nama_surat' => 'Surat Keterangan Usaha',
    'syarat_dokumen' => ['Foto KTP', 'Foto Usaha'],
    'is_active' => true,
  ]);
  ```

#### `PUT /v1/admin/kategori-surat/{id}`
- **Fungsi**: [Khusus Admin] Memperbarui kategori surat.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Body Parameter**: (sama seperti POST)
- **Response (200)**:
  ```json
  {
    "message": "Kategori surat berhasil diperbarui",
    "data": { ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.put(`/v1/admin/kategori-surat/${id}`, {
    kode_surat: 'SKU',
    nama_surat: 'Surat Keterangan Usaha Update',
    ...
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->put("/v1/admin/kategori-surat/{$id}", [
    'kode_surat' => 'SKU',
    'nama_surat' => 'Surat Keterangan Usaha Update',
    ...
  ]);
  ```

#### `DELETE /v1/admin/kategori-surat/{id}`
- **Fungsi**: [Khusus Admin] Menghapus kategori surat.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "message": "Kategori surat berhasil dihapus"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.delete(`/v1/admin/kategori-surat/${id}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->delete("/v1/admin/kategori-surat/{$id}");
  ```

---

### 2.15. Modul Admin - Manajemen Fasilitas & Inventaris

#### `GET /v1/admin/fasilitas`
- **Fungsi**: [Khusus Admin] Mengambil daftar inventaris fasilitas desa.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Query Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `search` | string | Tidak | Pencarian nama fasilitas |
- **Response (200)**:
  ```json
  {
    "data": [
      { "id": 1, "nama_fasilitas": "Gedung Serbaguna", "kategori": "Bangunan", "kondisi": "Baik", "lokasi": "Dusun Makmur", "jumlah": 1 }
    ],
    "meta": { "current_page": 1, "per_page": 15, "total": 10 }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/admin/fasilitas', {
    params: { search: 'Gedung' },
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/admin/fasilitas', [
    'search' => 'Gedung',
  ]);
  ```

#### `POST /v1/admin/fasilitas`
- **Fungsi**: [Khusus Admin] Menambahkan data fasilitas/inventaris baru.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `nama_fasilitas` | string | Ya | Nama fasilitas |
  | `kategori` | string | Ya | Kategori fasilitas |
  | `kondisi` | string | Ya | Kondisi (Baik, Rusak Ringan, Rusak Berat) |
  | `lokasi` | string | Tidak | Lokasi fasilitas |
  | `jumlah` | integer | Ya | Jumlah unit |
- **Response (201)**:
  ```json
  {
    "message": "Inventaris fasilitas berhasil ditambahkan",
    "data": { "id": 2, "nama_fasilitas": "Mobil Desa", ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/admin/fasilitas', {
    nama_fasilitas: 'Mobil Desa',
    kategori: 'Kendaraan',
    kondisi: 'Baik',
    lokasi: 'Kantor Desa',
    jumlah: 1
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->post('/v1/admin/fasilitas', [
    'nama_fasilitas' => 'Mobil Desa',
    'kategori' => 'Kendaraan',
    'kondisi' => 'Baik',
    'lokasi' => 'Kantor Desa',
    'jumlah' => 1,
  ]);
  ```

#### `DELETE /v1/admin/fasilitas/{id}`
- **Fungsi**: [Khusus Admin] Menghapus data fasilitas/inventaris.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "message": "Fasilitas berhasil dihapus"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.delete(`/v1/admin/fasilitas/${id}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->delete("/v1/admin/fasilitas/{$id}");
  ```

---

### 2.16. Modul Admin - Audit Log

Audit logging menggunakan **dua tabel**: `audit_logs` (legacy) dan `activity_log` (spatie/laravel-activitylog, utama).

#### `GET /v1/admin/audit-log`
- **Fungsi**: [Khusus Admin] Mengambil log aktivitas sistem secara kronologis.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "data": [
      {
        "id": 1,
        "user_type": "admin",
        "user_id": "1",
        "tindakan": "approve",
        "nama_tabel": "pengajuan_surat",
        "record_id": "1",
        "created_at": "2026-07-14T09:00:00Z"
      }
    ],
    "meta": { "current_page": 1, "per_page": 20, "total": 150 }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/admin/audit-log', {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/admin/audit-log');
  ```

---

### 2.17. Modul Admin - Manajemen Informasi (Berita)

#### `GET /v1/admin/informasi`
- **Fungsi**: [Khusus Admin] Mengambil semua draf maupun berita terbit.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Query Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `is_published` | boolean | Tidak | Filter status publikasi |
- **Response (200)**:
  ```json
  {
    "data": [
      { "id": 1, "judul": "Pengumuman PILKADES", "is_published": true, "kategori": "Pengumuman", "author": { "nama_lengkap": "Admin" } }
    ],
    "meta": { "current_page": 1, "per_page": 20, "total": 10 }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/admin/informasi', {
    params: { is_published: true },
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/admin/informasi', [
    'is_published' => true,
  ]);
  ```

#### `GET /v1/admin/informasi/{id}`
- **Fungsi**: [Khusus Admin] Menampilkan detail artikel informasi berdasarkan ID (termasuk yang masih draf).
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "data": {
      "id": 1,
      "judul": "Pengumuman Pemilihan Kepala Desa 2026",
      "slug": "pengumuman-pemilihan-kepala-desa-2026",
      "konten": "Diumumkan kepada seluruh warga...",
      "kategori": "Pengumuman",
      "is_published": true,
      "author_id": 1
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get(`/v1/admin/informasi/${id}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get("/v1/admin/informasi/{$id}");
  ```

#### `POST /v1/admin/informasi`
- **Fungsi**: [Khusus Admin] Membuat artikel berita baru.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `judul` | string | Ya | Judul artikel |
  | `konten` | string | Ya | Isi konten |
  | `kategori` | string | Ya | Kategori (Pengumuman, Berita, Kegiatan) |
  | `cover_image` | string | Tidak | Path/URL gambar sampul |
  | `is_published` | boolean | Tidak | Status publikasi (default: false) |
- **Response (201)**:
  ```json
  {
    "message": "Informasi berhasil dibuat",
    "data": { "id": 2, "judul": "Berita Baru", "slug": "berita-baru", ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.post('/v1/admin/informasi', {
    judul: 'Kegiatan Kerja Bakti',
    konten: 'Akan diadakan kerja bakti pada hari Minggu...',
    kategori: 'Kegiatan',
    is_published: true
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->post('/v1/admin/informasi', [
    'judul' => 'Kegiatan Kerja Bakti',
    'konten' => 'Akan diadakan kerja bakti pada hari Minggu...',
    'kategori' => 'Kegiatan',
    'is_published' => true,
  ]);
  ```

#### `PUT /v1/admin/informasi/{id}`
- **Fungsi**: [Khusus Admin] Memperbarui isi artikel berita.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Body Parameter**: (sama seperti POST, semua opsional)
- **Response (200)**:
  ```json
  {
    "message": "Informasi berhasil diupdate",
    "data": { ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.put(`/v1/admin/informasi/${id}`, {
    judul: 'Judul Baru',
    is_published: false
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->put("/v1/admin/informasi/{$id}", [
    'judul' => 'Judul Baru',
  ]);
  ```

#### `DELETE /v1/admin/informasi/{id}`
- **Fungsi**: [Khusus Admin] Menghapus berita dari database.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "message": "Informasi berhasil dihapus"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.delete(`/v1/admin/informasi/${id}`, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->delete("/v1/admin/informasi/{$id}");
  ```

---

### 2.18. Modul Admin - Manajemen Surat (Warga)

#### `GET /v1/admin/surat/pengajuan`
- **Fungsi**: [Khusus Admin] Mengambil daftar semua surat masuk yang menunggu verifikasi.
- **Auth**: Bearer Token (Admin)
- **Throttle**: 30 kali per 1 menit
- **Query Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `status` | string | Tidak | Filter status (Pending, Disetujui, Ditolak, Selesai) |
- **Response (200)**:
  ```json
  {
    "data": [
      {
        "id": 1,
        "nik_pemohon": "1118060512900001",
        "nomor_registrasi": "REG/2026/0001",
        "status": "Pending",
        "kategori": { "id": 1, "nama_surat": "Surat Keterangan Domisili" },
        "pemohon": { "nik": "1118060512900001", "nama_lengkap": "Ahmad Fauzi" }
      }
    ]
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/admin/surat/pengajuan', {
    params: { status: 'Pending' },
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/admin/surat/pengajuan', [
    'status' => 'Pending',
  ]);
  ```

#### `POST /v1/admin/surat/pengajuan/{id}/approve`
- **Fungsi**: [Khusus Admin] Menyetujui pengajuan surat, otomatis meng-generate QR Code TTE dan mengirim PDF ke Telegram.
- **Auth**: Bearer Token (Admin)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "message": "Pengajuan berhasil disetujui",
    "data": {
      "id": 1,
      "nomor_registrasi": "REG/2026/0001",
      "status": "Disetujui",
      "diverifikasi_oleh": 1
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post(`/v1/admin/surat/pengajuan/${id}/approve`, {}, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->post("/v1/admin/surat/pengajuan/{$id}/approve");
  ```

#### `POST /v1/admin/surat/pengajuan/{id}/reject`
- **Fungsi**: [Khusus Admin] Menolak surat dengan memberikan alasan penolakan.
- **Auth**: Bearer Token (Admin)
- **Throttle**: 30 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `catatan_penolakan` | string | Ya | Alasan penolakan |
- **Response (200)**:
  ```json
  {
    "message": "Pengajuan ditolak",
    "data": {
      "id": 1,
      "nomor_registrasi": "REG/2026/0001",
      "status": "Ditolak",
      "catatan_penolakan": "Berkas tidak lengkap"
    }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post(`/v1/admin/surat/pengajuan/${id}/reject`, {
    catatan_penolakan: 'Berkas tidak lengkap, harap lengkapi KTP dan KK'
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->post("/v1/admin/surat/pengajuan/{$id}/reject", [
    'catatan_penolakan' => 'Berkas tidak lengkap',
  ]);
  ```

---

### 2.19. Modul Admin - Manajemen Mutasi

#### `GET /v1/admin/mutasi`
- **Fungsi**: [Khusus Admin] Mengambil daftar semua mutasi masuk yang menunggu verifikasi.
- **Auth**: Bearer Token (Admin)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "data": [
      {
        "id": 1,
        "nik": "1118060512900001",
        "jenis_mutasi": "Kelahiran",
        "tanggal_mutasi": "2026-07-15",
        "status_verifikasi": "Pending"
      }
    ]
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  const response = await axios.get('/v1/admin/mutasi', {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  $response = Http::withToken($token)->get('/v1/admin/mutasi');
  ```

#### `POST /v1/admin/mutasi/{id}/approve`
- **Fungsi**: [Khusus Admin] Menyetujui pengajuan mutasi dan memperbarui status kependudukan warga.
- **Auth**: Bearer Token (Admin)
- **Throttle**: 30 kali per 1 menit
- **Response (200)**:
  ```json
  {
    "message": "Mutasi berhasil disetujui",
    "data": { "id": 1, "status_verifikasi": "Disetujui", ... }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post(`/v1/admin/mutasi/${id}/approve`, {}, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->post("/v1/admin/mutasi/{$id}/approve");
  ```

#### `POST /v1/admin/mutasi/{id}/reject`
- **Fungsi**: [Khusus Admin] Menolak pengajuan mutasi kependudukan.
- **Auth**: Bearer Token (Admin)
- **Throttle**: 30 kali per 1 menit
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `catatan_penolakan` | string | Ya | Alasan penolakan |
- **Response (200)**:
  ```json
  {
    "message": "Mutasi ditolak",
    "data": { "id": 1, "status_verifikasi": "Ditolak", "catatan_penolakan": "..." }
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post(`/v1/admin/mutasi/${id}/reject`, {
    catatan_penolakan: 'Data tidak lengkap'
  }, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->post("/v1/admin/mutasi/{$id}/reject", [
    'catatan_penolakan' => 'Data tidak lengkap',
  ]);
  ```

---

### 2.20. Modul Admin - Statistik

#### `POST /v1/admin/statistik/clear-cache`
- **Fungsi**: [Khusus Admin] Memaksa pembersihan cache statistik pada Redis.
- **Auth**: Bearer Token (Admin)
- **Throttle**: -
- **Response (200)**:
  ```json
  {
    "message": "Cache statistik berhasil dibersihkan"
  }
  ```
- **Contoh JS (axios)**:
  ```javascript
  await axios.post('/v1/admin/statistik/clear-cache', {}, {
    headers: { Authorization: `Bearer ${token}` }
  });
  ```
- **Contoh PHP (guzzle)**:
  ```php
  Http::withToken($token)->post('/v1/admin/statistik/clear-cache');
  ```

---

### 2.21. Format Respons Terstruktur

Seluruh endpoint API Resource menyertakan `*_formatted` fields untuk memudahkan rendering di sisi klien:

* **`tanggal_lahir_formatted`**: Format tanggal lokal Indonesia (dd/mm/yyyy).
* **`nama_lengkap_formatted`**: Nama dengan kapitalisasi yang telah diformat.
* **`created_at_formatted`**, **`updated_at_formatted`**: Timestamp dalam format manusiawi.
* Field ini otomatis disertakan oleh API Resource Layer Laravel dan tidak perlu dihitung ulang oleh klien.

---

### 2.22. Modul Notifikasi (Admin Panel)

#### `GET /admin/notifications`
- **Fungsi**: [Khusus Admin] Halaman pengaturan notifikasi (rendered via Inertia).
- **Auth**: Session (Filament admin)
- **Response**: Render Inertia `Admin/NotificationSettings`

#### `POST /admin/notifications/test`
- **Fungsi**: [Khusus Admin] Kirim pesan test ke Telegram/WhatsApp.
- **Auth**: Session (Filament admin)
- **Body Parameter**:
  | Parameter | Tipe | Required | Deskripsi |
  |-----------|------|----------|-----------|
  | `channel` | string | Ya | `telegram` atau `whatsapp` |
  | `target` | string | Ya | Chat ID (Telegram) atau nomor HP (WhatsApp) |
- **Response (200)**:
  ```json
  {
    "success": true,
    "message": "Pesan test berhasil dikirim"
  }
  ```

#### `GET /api/v1/notifications/settings`
- **Fungsi**: [Khusus Admin] Ambil semua pengaturan notifikasi sebagai JSON.
- **Auth**: Bearer Token (Admin)
- **Response (200)**:
  ```json
  {
    "data": {
      "telegram_bot_token": "***",
      "telegram_webhook_url": "https://...",
      "wa_provider": "wa-gateway",
      "notif_wa_surat_pending": "Status Pengajuan Surat..."
    }
  }
  ```

#### `PUT /api/v1/notifications/settings`
- **Fungsi**: [Khusus Admin] Update pengaturan notifikasi.
- **Auth**: Bearer Token (Admin)
- **Body Parameter**: Key-value pairs dari pengaturan notifikasi
- **Response (200)**:
  ```json
  {
    "message": "Pengaturan notifikasi berhasil diperbarui"
  }
  ```

---

## 3. Integrasi & Pengujian API

* **Spesifikasi OpenAPI**: File spesifikasi kontrak standar YAML berada di `docs/api-contract.yaml` (dapat diimpor ke Swagger Editor).
