# Dokumentasi Models - SIG-Udeung

Dokumentasi ini menjelaskan seluruh model Eloquent dalam aplikasi SIG-Udeung, termasuk relasi, scope, accessor, dan behavior unik masing-masing model.

---

## Daftar Model

| Model | Tabel | PK | Keterangan |
|-------|-------|----|------------|
| `Penduduk` | `penduduk` | NIK (VARCHAR 16) | Data kependudukan warga |
| `Keluarga` | `keluarga` | No KK (VARCHAR 16) | Data Kartu Keluarga |
| `Administrator` | `administrators` | ULID | Admin perangkat desa |
| `PengajuanSurat` | `pengajuan_surat` | ULID | Pengajuan surat warga |
| `MutasiPenduduk` | `mutasi_penduduk` | ULID | Laporan mutasi kependudukan |
| `KategoriSurat` | `kategori_surat` | ULID | Template/kategori surat |
| `InformasiPublik` | `informasi_publik` | ULID | Artikel berita/pengumuman |
| `TrackingPengajuanSurat` | `tracking_pengajuan_surat` | ULID | Log perubahan status surat |
| `PengaturanDesa` | `pengaturan_desa` | ULID | Konfigurasi sistem (key-value) |
| `PengaturanFrontend` | `pengaturan_frontend` | Kunci (VARCHAR 50) | Konfigurasi konten frontend |
| `ReferensiWilayah` | `referensi_wilayah` | Kode Wilayah (VARCHAR 15) | Master data wilayah |
| `BotKnowledge` | `bot_knowledges` | ULID | Basis pengetahuan chatbot |
| `ChatbotLog` | `chatbot_logs` | ULID | Log interaksi chatbot |
| `TelegramBroadcastQueue` | `telegram_broadcast_queue` | ULID | Antrean pesan massal Telegram |
| `AuditLog` | `audit_logs` | ULID | Log audit aktivitas |
| `TrafficLog` | `traffic_logs` | ULID | Log kunjungan website |
| `User` | `users` | ULID | Akun default Laravel |

---

## Model Utama

### Penduduk

Model untuk data kependudukan warga desa. Menggunakan NIK sebagai primary key dan meng-extend `Authenticatable` untuk autentikasi Sanctum.

**Relasi:**
- `belongsTo(Keluarga)` - Relasi ke Kartu Keluarga melalui `no_kk`
- `hasMany(MutasiPenduduk)` - Riwayat mutasi warga
- `hasMany(PengajuanSurat)` - Riwayat pengajuan surat

**Accessor:**
- `getFotoProfilAttribute()` - URL dinamis foto profil ( Storage::url atau URL eksternal)
- `getFotoKtpAttribute()` - URL dinamis foto KTP
- `getFotoKkAttribute()` - URL dinamis foto KK
- `getUmurAttribute()` - Umur berdasarkan tanggal lahir

**Scope:**
- `scopeAktif()` - Filter penduduk tetap (status_mutasi = 'Tetap')
- `scopeLakiLaki()` - Filter laki-laki
- `scopePerempuan()` - Filter perempuan
- `scopeByDusun()` - Filter berdasarkan dusun

**Hidden:** `telegram_chat_id`

---

### Keluarga

Model untuk data Kartu Keluarga (KK). Menggunakan No KK sebagai primary key.

**Relasi:**
- `hasMany(Penduduk)` - Seluruh anggota keluarga (`anggota()`)
- `belongsTo(Penduduk)` - Kepala keluarga (`kepalaKeluarga()`)

**Scope:**
- `scopeByDusun()` - Filter berdasarkan dusun

**Catatan:** Tidak menggunakan timestamps.

---

### Administrator

Model untuk admin perangkat desa. Menggunakan ULID sebagai primary key dan meng-extend `Authenticatable` dengan implementasi `FilamentUser`.

**Relasi:**
- `hasMany(PengajuanSurat)` - Pengajuan surat yang diverifikasi
- `hasMany(MutasiPenduduk)` - Mutasi yang diverifikasi
- `hasMany(InformasiPublik)` - Artikel informasi yang ditulis
- `hasMany(TelegramBroadcastQueue)` - Pesan broadcast yang dibuat

**Method:**
- `canAccessPanel()` - Mengecek apakah role termasuk kepala desa/sekdes/operator
- `getNameAttribute()` - Mengembalikan username sebagai nama tampilan

**Hidden:** `password`

---

### PengajuanSurat

Model untuk pengajuan surat mandiri oleh warga. Menggunakan ULID sebagai primary key.

**Relasi:**
- `belongsTo(Penduduk)` - Pemohon surat (`pemohon()`)
- `belongsTo(KategoriSurat)` - Kategori surat (`kategori()`)
- `belongsTo(Administrator)` - Verifikator (`verifikator()`)
- `hasMany(TrackingPengajuanSurat)` - Log tracking (`tracking()`)

**Scope:**
- `scopePending()` - Filter pengajuan pending
- `scopeDiproses()` - Filter pengajuan sedang diproses

**Behavior:**
- Auto-generate `nomor_registrasi` saat creating (format: YYYYMMDD-XXXX)
- Cast `data_isian` dan `file_syarat` ke array

---

### MutasiPenduduk

Model untuk laporan mutasi kependudukan. Menggunakan ULID sebagai primary key.

**Relasi:**
- `belongsTo(Penduduk)` - Penduduk bersangkutan (`penduduk()`)
- `belongsTo(Administrator)` - Verifikator (`verifikator()`)

**Scope:**
- `scopePending()` - Filter mutasi pending

**Catatan:** Tidak menggunakan timestamps otomatis.

---

### KategoriSurat

Model untuk template/kategori layanan persuratan. Menggunakan ULID sebagai primary key.

**Relasi:**
- `hasMany(PengajuanSurat)` - Seluruh pengajuan dengan kategori ini (`pengajuan()`)

**Scope:**
- `scopeActive()` - Filter kategori yang aktif

**Catatan:** Cast `schema_isian` dan `syarat_dokumen` ke array.

---

### InformasiPublik

Model untuk artikel berita dan pengumuman desa. Menggunakan ULID sebagai primary key.

**Relasi:**
- `belongsTo(Administrator)` - Penulis artikel (`author()`)

**Accessor:**
- `getCoverImageAttribute()` - URL dinamis gambar sampul

**Scope:**
- `scopePublished()` - Filter artikel yang sudah dipublikasikan

**Behavior:**
- Auto-generate `slug` dari judul saat creating/updating
- Dispatch `SendNewsTelegramNotificationJob` saat artikel dipublikasikan

**Catatan:** Tidak menggunakan timestamps otomatis.

---

## Model Pendukung

### TrackingPengajuanSurat

Log riwayat perubahan status pengajuan surat.

**Relasi:**
- `belongsTo(PengajuanSurat)` - Pengajuan terkait (`pengajuan()`)
- `belongsTo(Administrator)` - Admin pengubah (`updater()`)

---

### PengaturanDesa

Konfigurasi global sistem desa (key-value). Menggunakan ULID sebagai primary key.

**Method Statis:**
- `get($key, $default)` - Ambil nilai dengan casting tipe data
- `set($key, $value, $type)` - Simpan/update nilai

---

### PengaturanFrontend

Konfigurasi konten frontend publik (key-value). Menggunakan kunci sebagai primary key.

**Method Statis:**
- `get($key, $default)` - Ambil nilai dengan casting tipe data
- `set($key, $value, $type)` - Simpan/update nilai

**Catatan:** Mirip dengan `PengaturanDesa` tetapi untuk konten frontend.

---

### ReferensiWilayah

Master data wilayah administrasi negara (hierarki self-referencing).

**Relasi:**
- `belongsTo(ReferensiWilayah)` - Wilayah induk (`parent()`)
- `hasMany(ReferensiWilayah)` - Sub-wilayah (`children()`)

---

### BotKnowledge

Basis pengetahuan (knowledge base) chatbot Telegram.

**Behavior:**
- Auto-invalidate cache `telegram_bot_knowledges` saat saved/deleted

---

### ChatbotLog

Log interaksi percakapan warga dengan chatbot Telegram.

**Catatan:** Tidak menggunakan timestamps otomatis (`created_at` di-set manual).

---

### TelegramBroadcastQueue

Antrean pengiriman pesan massal (broadcast) Telegram.

**Relasi:**
- `belongsTo(Administrator)` - Pembuat broadcast (`creator()`)

**Scope:**
- `scopeQueued()` - Filter pesan dalam antrean
- `scopeReady()` - Filter pesan siap dikirim (jadwal <= now)

---

### AuditLog

Log riwayat audit aktivitas pengguna/sistem.

**Method Statis:**
- `log($userType, $userId, $tindakan, $namaTabel, ...)` - Buat catatan audit baru

**Catatan:** Tidak menggunakan timestamps otomatis. Menyimpan IP address dan user agent secara otomatis.

---

### TrafficLog

Log kunjungan/traffic website.

**Catatan:** Tidak menggunakan timestamps otomatis.

---

### User

Model default Laravel untuk akun pengguna. Digunakan sebagai fallback.

---

## Catatan Umum

1. **Primary Key**: Sebagian besar model menggunakan ULID (`HasUlids` trait), kecuali `Penduduk` (NIK), `Keluarga` (No KK), `PengaturanFrontend` (kunci), dan `ReferensiWilayah` (kode wilayah).

2. **Timestamps**: Beberapa model tidak menggunakan timestamps otomatis (`$timestamps = false`) dan mengelola `created_at` secara manual.

3. **Casting**: Model yang menggunakan kolom JSON (`data_isian`, `file_syarat`, `schema_isian`, `syarat_dokumen`, `kata_kunci`, `data_lama`, `data_baru`) di-cast ke array.

4. **Eager Loading**: Model utama (`Penduduk`, `PengajuanSurat`, `Keluarga`) menggunakan eager loading global (`$with`) untuk relasi yang sering diakses guna mencegah N+1 query.

5. **Soft Deletes**: Tidak ada model yang menggunakan soft deletes. Penghapusan data menggunakan hard delete dengan cascade relationship.
