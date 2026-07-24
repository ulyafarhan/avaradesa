# ADR-004: Activity Log System dengan spatie/laravel-activitylog

**Status:** Accepted  
**Date:** 2026-07-23  

## Context

Sistem membutuhkan audit trail profesional untuk:
1. Mencatat perubahan konfigurasi sistem (siapa mengubah apa)
2. Mencatat aktivitas autentikasi admin (login berhasil/gagal)
3. Mencatat pengiriman notifikasi (WhatsApp, Telegram) dan respons webhook
4. Mencatat unggahan file dan operasi sistem lainnya
5. Menyediakan antarmuka admin untuk melihat dan memfilter log
6. Retensi dan pembersihan log otomatis

Sebelumnya sistem memiliki model `AuditLog` (tabel `audit_logs`) buatan sendiri dengan keterbatasan:
- Tidak memiliki relasi polymorphic (causer/subject)
- Tidak mendukung event typing
- Tidak ada fitur batch logging
- Kolom `user_type` menggunakan enum (`admin|warga`) yang kaku
- Tidak ada integrasi dengan Filament secara native

## Decision

Gunakan **spatie/laravel-activitylog v4** sebagai fondasi activity log system.

### Alasan Pemilihan

| Kriteria | spatie/laravel-activitylog | Custom AuditLog |
|:---------|:---------------------------|:----------------|
| Maturity | 3.8k+ GitHub stars, 700+ forks | Buatan sendiri |
| Maintenance | Aktif (rilis reguler) | Manual |
| Relasi | `causer()` + `subject()` polymorphic | Tidak ada |
| Event | Built-in `event()` method | Manual string |
| Filtering | Scope bawaan (by event, subject, causer) | Manual query |
| Cleanup | Built-in command + config | Manual query |
| Filament | Compatible | Butuh adapter |
| Testing | Extensive test suite | Test terbatas |

### Arsitektur

```
SystemLogger (service wrapper)
  └── activity()->event()->causedBy()->performedOn()->withProperties()->log()
        └── Spatie\Activitylog\Models\Activity (tabel: activity_log)
              ├── Polymorphic causer (Administrator / Penduduk)
              ├── Polymorphic subject (model apa pun)
              ├── Event string (auth.login, config.changed, notification.sent, ...)
              └── JSON properties (ip, user_agent, diff, metadata, ...)
```

### Titik Logging (12 lokasi)

| Event | Trigger | File |
|:------|:--------|:-----|
| `config.changed` | Pengaturan Desa disimpan | `PengaturanSistem.php::save()` |
| `content.changed` | Konten publik disimpan | `PengaturanKontenPublik.php::save()` |
| `auth.login` | Admin login berhasil | `AdminLogin.php::authenticate()` |
| `auth.login.failed` | Admin login gagal | `AdminLogin.php::authenticate()` |
| `notification.sent` | WhatsApp/Telegram terkirim/gagal | `WhatsAppService.php`, `TelegramService.php` |
| `webhook.received` | Webhook WA/Telegram masuk | `WhatsAppWebhookController.php`, `TelegramWebhookController.php` |
| `file.uploaded` | Gambar cover/fasilitas diunggah | `InformasiPublikResource.php`, `InventarisFasilitasResource.php` |
| `system.error` | Database error / exception tak tertangani | `bootstrap/app.php` |
| `system.cleanup` | Pembersihan sistem dijalankan | `SystemCleanupCommand.php` |
| `aspirasi.kirim` | Aspirasi publik dikirim | `PublicPortalController.php::storeAspirasi()` |

## Consequences

### Positif

1. **Industry standard** — Digunakan ribuan proyek Laravel, dokumentasi lengkap
2. **Polymorphic relationships** — `causer()` otomatis mendeteksi Admin atau Penduduk
3. **Event system** — Filter dan grouping log berdasarkan event type
4. **Built-in cleanup** — Konfigurasi retention via `delete_records_older_than_days`
5. **Fitur batch UUID** — Untuk grouping log dalam satu operasi

### Negatif

1. **Dependency baru** — Harus install package via Composer
2. **Migration data** — Data `audit_logs` lama perlu di-migrasi ke `activity_log`
3. **Konfigurasi ULID** — Migration default spatie menggunakan BIGINT untuk morph ID, perlu diubah ke STRING(26) untuk kompatibilitas ULID (migration `2026_07_23_162730` dan `2026_07_23_164712`)

### Migration Path

```bash
# 1. Cek jumlah data lama
php artisan log:migrate-from-audit --dry-run

# 2. Eksekusi migrasi
php artisan log:migrate-from-audit
```

### Retensi

- Activity log otomatis dibersihkan setelah 90 hari via `config/activitylog.php`
- Pembersihan terjadwal: `php artisan system:cleanup` (dijalankan setiap pukul 02:00 via `routes/console.php`)

## Related

- PRD: Bagian audit & logging
- `app/Services/SystemLogger.php`
- `app/Filament/Resources/ActivityLogResource.php`
- `app/Filament/Resources/AspirasiResource.php`
- `config/activitylog.php`
