# Dokumentasi Database - AvaraDesa

Dokumentasi ini merinci skema tabel, relasi, normalisasi data, serta indeks optimasi database untuk platform AvaraDesa.

---

## 1. Spesifikasi Teknis Database
* **Database Utama**: MySQL 8.0+ / MariaDB (Production)
* **Database Lokal (Development/Testing)**: SQLite
* **Primary Key Format**: Seluruh tabel utama menggunakan ULID (Universally Unique Lexicographically Sortable Identifier) untuk melindungi sistem dari serangan enumeration dan SQL injection, kecuali `referensi_wilayah` yang menggunakan kode wilayah, serta `penduduk` dan `keluarga` yang menggunakan NIK/No KK.

---

## 2. Struktur Relasi Tabel Utama (ERD)

Sistem ini didukung oleh 16 tabel + 2 konfigurasi yang dinormalisasi hingga 3NF:

```mermaid
erDiagram
    keluarga ||--o{ penduduk : "memiliki (1:N)"
    penduduk ||--o{ pengajuan_surat : "mengajukan (1:N)"
    kategori_surat ||--o{ pengajuan_surat : "mengelompokkan (1:N)"
    pengajuan_surat ||--o{ tracking_pengajuan_surat : "memiliki log (1:N)"
    administrators ||--o{ informasi_publik : "membuat (1:N)"

    keluarga {
        string no_kk PK
        string kepala_keluarga_nik FK
        string alamat
        string dusun
        string rt_rw
    }
    penduduk {
        string nik PK
        string no_kk FK
        string nama_lengkap
        string telegram_chat_id
        string status_mutasi
    }
    kategori_surat {
        ulid id PK
        string kode_surat
        string nama_surat
        json schema_isian
        json syarat_dokumen
    }
    pengajuan_surat {
        ulid id PK
        string nik_pemohon FK
        ulid kategori_surat_id FK
        string nomor_registrasi
        string nomor_surat
        string status
        json data_isian
        json file_syarat
        string qr_hash
    }
    tracking_pengajuan_surat {
        ulid id PK
        ulid pengajuan_surat_id FK
        string status_sebelumnya
        string status_baru
        text keterangan_update
    }
    administrators {
        ulid id PK
        string username
        string role
    }
    informasi_publik {
        ulid id PK
        string judul
        ulid author_id FK
    }
```

---

## 3. Rincian Tabel Tambahan

* **`bot_knowledges`**: Menyimpan basis pengetahuan (FAQ & RAG Context) untuk Telegram chatbot. Kunci-kunci dicari secara dinamis oleh AI atau sistem bot.
* **`audit_logs`**: Mencatat semua log mutasi database (INSERT, UPDATE, DELETE) lengkap dengan data lama (`data_lama`), data baru (`data_baru`), IP address, dan user agent aktor yang memicu.
* **`pengaturan_desa`**: Berisi konfigurasi global sistem desa (Key-Value), seperti identitas wilayah, visi misi, serta kredensial API AI dan Cloud Storage.
* **`pengaturan_frontend`**: Menyimpan konfigurasi khusus konten publik, meliputi data nama & foto aparat desa, kontak layanan warga, serta link akun media sosial resmi desa.
* **`traffic_logs`**: Mencatat log aktivitas kunjungan web publik dan area warga untuk keperluan analisis lalu lintas dan statistik grafik dasbor secara riil.

---

## 4. Indeks & Optimasi Kueri
Untuk memastikan kecepatan eksekusi kueri di bawah 500ms, indeks database dibuat pada kolom-kolom berikut:

**Catatan:** Kolom `nomor_surat` pada tabel `pengajuan_surat` menyimpan nomor surat resmi yang diterbitkan setelah diverifikasi, berbeda dengan `nomor_registrasi` yang merupakan nomor pendaftaran sistem.

### Single Column Indexes (19)
* `idx_pengaturan_kunci` pada `pengaturan_desa(kunci)`
* `idx_penduduk_nama` pada `penduduk(nama_lengkap)`
* `idx_penduduk_no_kk` pada `penduduk(no_kk)`
* `idx_penduduk_status_mutasi` pada `penduduk(status_mutasi)`
* `idx_penduduk_jenis_kelamin` pada `penduduk(jenis_kelamin)`
* `idx_penduduk_tanggal_lahir` pada `penduduk(tanggal_lahir)`
* `idx_keluarga_dusun` pada `keluarga(dusun)`
* `idx_keluarga_kepala_keluarga_nik` pada `keluarga(kepala_keluarga_nik)`
* `idx_pengajuan_status` pada `pengajuan_surat(status)`
* `idx_pengajuan_nik` pada `pengajuan_surat(nik_pemohon)`
* `idx_pengajuan_created_at` pada `pengajuan_surat(created_at)`
* `idx_mutasi_jenis` pada `mutasi_penduduk(jenis_mutasi)`
* `idx_mutasi_tanggal` pada `mutasi_penduduk(tanggal_mutasi)`
* `idx_chatbot_logs_created_at` pada `chatbot_logs(created_at)`
* `idx_bot_knowledges_kunci` pada `bot_knowledges(kunci)`
* `idx_traffic_logs_created_at` pada `traffic_logs(created_at)`
* `idx_informasi_slug` pada `informasi_publik(slug)`
* `idx_informasi_kategori` pada `informasi_publik(kategori)`
* `idx_pengaturan_frontend_kunci` pada `pengaturan_frontend(kunci)`

### Composite Indexes
* `idx_audit_tabel_record` pada `audit_logs(nama_tabel, record_id)`
* `idx_pengajuan_status_created` pada `pengajuan_surat(status, created_at)`
* `idx_pengajuan_kategori_created` pada `pengajuan_surat(kategori_surat_id, created_at)`
* `idx_informasi_published_created` pada `informasi_publik(is_published, created_at)`
* `idx_informasi_published_kategori_created` pada `informasi_publik(is_published, kategori, created_at)`
* `idx_penduduk_nik_created` pada `penduduk(nik, created_at)`
* `idx_penduduk_no_kk_nik` pada `penduduk(no_kk, nik)`
