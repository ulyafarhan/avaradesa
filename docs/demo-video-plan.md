# Demo Video Plan — AvaraDesa

**Tim**: Quantum Nexus  
**Durasi**: 3–5 menit  
**Platform Rekaman**: OBS Studio  
**Resolusi**: 1920×1080

---

## Scene 1 — Opening (30 detik)

| Aspek | Detail |
|-------|--------|
| **Layar** | Black screen → fade in logo AvaraDesa + tulisan "Quantum Nexus" |
| **Narasi** | "Desa masih bergulat dengan tumpukan kertas dan antrean panjang. AvaraDesa hadir — satu platform untuk administrasi desa modern: web, mobile, desktop, dan AI." |
| **Aksi** | Cut ke mockup tiga perangkat (HP, laptop, desktop) berdampingan |
| **App** | Hasil edit / animasi |

---

## Scene 2 — Web Admin (45 detik)

| Aspek | Detail |
|-------|--------|
| **Layar** | Browser → `https://avaradesa.my.id/admin/login` |
| **Narasi** | "Admin desa login ke panel Filament. Dasbor menampilkan statistik real-time: jumlah penduduk, pengajuan aktif, dan performa server." |
| **Aksi** | Ketik email `admin@avaradesa.my.id` + password → login → Dashboard → klik **Data Penduduk** → scroll tabel (tunjukin data real) → klik salah satu warga → lihat detail |
| **App** | Web Browser (Chrome) |
| **Durasi** | 45 detik |

---

## Scene 3 — Web Warga (45 detik)

| Aspek | Detail |
|-------|--------|
| **Layar** | Browser → `https://avaradesa.my.id` (halaman publik) |
| **Narasi** | "Warga login pakai NIK. Ajukan surat secara online, lacak statusnya kapan saja tanpa datang ke kantor desa." |
| **Aksi** | Klik "Login Warga" → masuk pakai NIK demo + password → klik "Ajukan Surat" → pilih "Surat Keterangan Domisili" → isi form → submit → tunjukkan status "Diproses" di halaman tracking |
| **App** | Web Browser (Chrome) |
| **Durasi** | 45 detik |

---

## Scene 4 — Mobile App APK (30 detik)

| Aspek | Detail |
|-------|--------|
| **Layar** | Android emulator atau HP real — AvaraDesa app |
| **Narasi** | "Aplikasi Android — unduh APK, install, dan nikmati layanan desa dari genggaman. Informasi publik, pengajuan surat, semua di sini." |
| **Aksi** | Buka app → login → scroll halaman informasi publik → klik artikel → buka menu surat |
| **App** | Avaradesa.apk di Android |
| **Durasi** | 30 detik |

---

## Scene 5 — Desktop App EXE (30 detik)

| Aspek | Detail |
|-------|--------|
| **Layar** | Windows — AvaraDesa desktop app (Electron) |
| **Narasi** | "Desktop app untuk perangkat desa. Tampilan native, performa ringan, semua fitur web ada di sini." |
| **Aksi** | Buka app → login → navigasi menu → buka data penduduk → edit biodata |
| **App** | Avaradesa Setup 1.0.1.exe |
| **Durasi** | 30 detik |

---

## Scene 6 — QR Code Verification (30 detik)

| Aspek | Detail |
|-------|--------|
| **Layar** | Browser — PDF surat dengan QR code → HP scan QR |
| **Narasi** | "Setiap surat dilengkapi QR code. Instansi atau warga scan untuk verifikasi keaslian — anti pemalsuan." |
| **Aksi** | Buka PDF surat (tampilkan QR code) → scan QR → browser buka halaman verifikasi → centang hijau "SURAT ASLI" |
| **App** | Web (PDF viewer) + HP kamera |
| **Durasi** | 30 detik |

---

## Scene 7 — AI Chatbot Telegram (30 detik)

| Aspek | Detail |
|-------|--------|
| **Layar** | Telegram — chat dengan `@avaradesa_bot` |
| **Narasi** | "Butuh bantuan? Chatbot AI di Telegram siap menjawab pertanyaan warga 24/7 — informasi desa, tracking surat, semuanya." |
| **Aksi** | Buka Telegram → cari @avaradesa_bot → ketik "Halo" → balasan bot → tanya "Cara buat surat domisili?" → bot jawab |
| **App** | Telegram (HP/Desktop) |
| **Durasi** | 30 detik |

---

## Scene 8 — Testing (30 detik)

| Aspek | Detail |
|-------|--------|
| **Layar** | Terminal — `php artisan test` running |
| **Narasi** | "43 file test, 200+ assertions — setiap fitur diuji otomatis. Kualitas terjamin sebelum rilis." |
| **Aksi** | Jalankan `php artisan test` → scroll hasil → tunjukkan semua PASSED |
| **App** | Terminal / VS Code |
| **Durasi** | 30 detik |

---

## Scene 9 — Closing (15 detik)

| Aspek | Detail |
|-------|--------|
| **Layar** | Black screen → fade in logo AvaraDesa + tagline + "Quantum Nexus" |
| **Narasi** | "AvaraDesa — desa digital, masa depan cerah. Dari Quantum Nexus, untuk Indonesia." |
| **Aksi** | Muncul call-to-action: https://avaradesa.my.id |
| **App** | Hasil edit |
| **Durasi** | 15 detik |

---

## Ringkasan Durasi

| Scene | Durasi |
|-------|--------|
| 1. Opening | 30s |
| 2. Web Admin | 45s |
| 3. Web Warga | 45s |
| 4. Mobile APK | 30s |
| 5. Desktop EXE | 30s |
| 6. QR Code | 30s |
| 7. AI Chatbot | 30s |
| 8. Testing | 30s |
| 9. Closing | 15s |
| **Total** | **4 menit 45 detik** |

## Tips Produksi

- **Transisi**: Gunakan fade antar scene
- **Background music**: Royalty-free instrumental — upbeat/cinematic
- **Overlay text**: Nama fitur muncul di pojok bawah saat demo
- **No dead air**: Potong bagian loading atau tunggu
- **Credentials**: Tempelkan login/password di sticky note di layar agar terlihat
- **Data real**: Gunakan data demo yang sudah diisi di database (jangan kosong)
