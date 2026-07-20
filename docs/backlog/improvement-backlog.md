# 📋 Improvement Backlog — AvaraDesa × CODE 6.0

> **Acuan perbaikan proposal.** Berdasarkan review 15 juri. Tidak boleh berubah tanpa persetujuan tim.
> **Last updated:** 18 Juli 2026
> **Status final review:** ✅ Diverifikasi oleh 15 persona juri

---

## 🔴 P0 — Wajib Sebelum Submission (Deadline: 25 Juli 2026)

### P0-01: Tambah Screenshot Nyata Aplikasi

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J3 (UI/UX), J14 (Judge) |
| **Severity** | 🔴 Kritis |
| **Dampak** | Tanpa screenshot, juri anggap aplikasi belum jadi |

**Deskripsi masalah:** Proposal menyebut "(Screenshot akan ditempatkan di sini pada dokumen final)" di section 9.3. Ini kosong. Juri tidak bisa melihat bukti bahwa aplikasi benar-benar berfungsi.

**Checklist wajib — minimal 10 screenshot:**

| # | Halaman | Platform | Wajib |
|---|---------|----------|-------|
| 1 | Login Warga (NIK+KK) | Capacitor Mobile | ✅ |
| 2 | Dashboard Warga + Stat Cards | Capacitor Mobile | ✅ |
| 3 | Pengajuan Surat — Form | Capacitor Mobile | ✅ |
| 4 | Tracking Surat — Status Timeline | Capacitor Mobile | ✅ |
| 5 | Profil Warga + Data Keluarga | Capacitor Mobile | ✅ |
| 6 | Chatbot AI — Tanya Prosedur | Capacitor Mobile | ✅ |
| 7 | Admin Panel — Dashboard Filament | Web Desktop | ✅ |
| 8 | Admin Panel — Approve Surat | Web Desktop | ✅ |
| 9 | Dark Mode — Halaman Home | Capacitor Mobile + Web | ✅ |
| 10 | Portal Publik — Informasi Desa | Web Browser | ✅ |
| 11 | Verifikasi QR — Hasil | Capacitor / Web | ✅ |
| 12 | Aplikasi Windows — Layout Desktop | Electron Windows | opsional |

**Format:** PNG/JPG, resolusi 1080p, crop rapi, annotasi dengan panah/kotak merah.

---

### P0-02: Hapus/Klarifikasi Ollama dari Fallback Chain

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J7 (AI Engineer) |
| **Severity** | 🔴 Kritis — Fakta Salah |
| **Dampak** | Juri AI akan menagkap kesalahan ini dalam 10 detik. Kredibilitas proposal hancur. |

**Deskripsi masalah:** Proposal menyebut Ollama sebagai "jaring pengaman terakhir" di server desa 1 GB RAM. Ollama membutuhkan **minimal 4 GB RAM** (untuk Llama 3.2 7B) hingga **8 GB RAM** (untuk Llama 3.1 8B). Server desa 1 GB RAM TIDAK BISA menjalankan Ollama.

**Opsi perbaikan (pilih 1):**

| Opsi | Tindakan | Konsekuensi |
|------|----------|-------------|
| **A — Hapus** | Hapus Ollama dari daftar provider. Sebut "4 AI provider + mekanisme fallback lokal via cached responses" | Lebih jujur, kurang impressive |
| **B — Klarifikasi** | Pindahkan Ollama ke "Opsional — berjalan di desktop Windows warga, bukan di server" | Jujur, tetap bisa claim 5 provider |
| **C — Ganti** | Ganti Ollama dengan "cached response fallback" — jika semua provider gagal, ambil jawaban dari knowledge base lokal | Lebih realistis untuk 1 GB RAM |

**Rekomendasi:** Opsi C — cached response fallback dari BotKnowledge table.

---

### P0-03: Benchmark Server vs Client PDF

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J2 (CTO Fintech) |
| **Severity** | 🔴 Kritis — Angka tanpa bukti |

**Deskripsi masalah:** Proposal menyebut "menghemat resource server desa hingga 80%". Tidak ada benchmark. Juri akan langsung tanya: "Data dari mana?"

**Yang harus diukur:**

| Metrik | Server DOMPDF | Capacitor Client | Sumber Data |
|--------|---------------|---------------|-------------|
| **CPU usage (avg)** | ? | ? | `top` / Task Manager |
| **Memory usage (peak)** | ? | ? | `memory_get_peak_usage()` / DevTools |
| **Response time (p95)** | ? | ? | `dd()`, microtime |
| **Waktu render PDF** | ? | ? | Timer di Capacitor |
| **Ukuran file output** | ? | ? | filesize() |

**Prosedur benchmark:**
1. Render 5 jenis surat (domisili, tidak mampu, izin, keterangan, SKTM)
2. Server: gunakan `dd(microtime(true))` sebelum dan sesudah DomPDF
3. Client: gunakan `Stopwatch` di Capacitor
4. Catat ukuran server ram di `free -m` sebelum dan sesudah
5. Masukkan tabel hasil ke Section 9.3 proposal

---

### P0-04: Pangkas Proposal ke ≤40 Halaman

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J14 (Judge) |
| **Severity** | 🔴 Kritis — Diskualifikasi |

**Deskripsi masalah:** RuleBook menyebut "Proposal ditulis maksimal 40 halaman termasuk lampiran." Proposal saat ini diperkirakan 50+ halaman. Ini bisa menyebabkan diskualifikasi otomatis.

**Rencana pemangkasan:**

| Section | Halaman Saat Ini | Target | Potong | Hasil |
|---------|-----------------|--------|--------|-------|
| Cover | 1 | 1 | 0 | 1 |
| Abstrak | 1 | 1 | 0 | 1 |
| Daftar Isi | 1 | 1 | 0 | 1 |
| 1. Latar Belakang | 5 | **2** | 3 | 2 |
| 2. Tujuan, Manfaat, Dampak | 4 | **2** | 2 | 2 |
| 3. Inovasi & Orisinalitas | 6 | 5 | 1 | 5 |
| 4. Analisis Kompetitor | 4 | 3 | 1 | 3 |
| 5. Teknologi | 5 | **2** | 3 | 2 |
| 6. Batasan | 2 | 1 | 1 | 1 |
| 7. Metodologi | 4 | 3 | 1 | 3 |
| 8. Arsitektur | 6 | 4 | 2 | 4 |
| 9. Implementasi Teknis | 6 | **3** | 3 | 3 |
| 10. Analisis UI/UX | 6 | **3** | 3 | 3 |
| 11. Pengujian | 5 | 4 | 1 | 4 |
| 12. Dokumentasi | 3 | **2** | 1 | 2 |
| 13. Daftar Pustaka | 3 | 2 | 1 | 2 |
| **Total** | **≈62** | **≈38** | **24** | **≤40** |

**Strategi pemangkasan:**
- **Latar Belakang (5→2):** Hapus paragraf deskriptif, jaga data statistik. Tabel sudah cukup.
- **Teknologi (5→2):** Cukup 2 tabel (stack + alasan). Hapus penjelasan per komponen.
- **Implementasi (6→3):** Pangkas kode — cukup 2 cuplikan (sync + fallback AI). Hapus cuplikan yang lain.
- **UI/UX (6→3):** Hapus halaman statis (flowchart verifikasi bisa dijadikan gambar). Hapus tabel aksesibilitas (simplify ke 3 butir).

---

### P0-05: Perbaiki Abstrak — Tambahkan Pembuka Non-Teknis

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J13 (Journalist), J15 (User) |
| **Severity** | 🔴 Kritis — First impression |

**Deskripsi masalah:** Abstrak saat ini langsung ke teknis ("sinkronisasi data delta, rendering dokumen PDF di sisi klien..."). 3 kalimat pertama proposal adalah yang paling penting — juri memutuskan apakah akan melanjutkan membaca atau tidak.

**Format abstrak baru:**

> **Paragraf 1 (Non-teknis — untuk semua orang):**
> *"Setiap hari, jutaan warga desa di Indonesia harus menempuh perjalanan panjang ke kantor desa, menunggu berhari-hari untuk satu lembar surat keterangan. Di tengah keterbatasan akses internet dan minimnya infrastruktur digital, AvaraDesa hadir sebagai solusi: platform administrasi desa yang tetap berfungsi penuh tanpa koneksi internet, bisa diakses dari HP Android murah sekalipun, dan didukung asisten AI yang siap membantu kapan saja."*
>
> **Paragraf 2 (Teknis — untuk juri & dosen):**
> *"...[konten teknis yang sudah ada]..."*

---

## 🟠 P1 — Wajib Sebelum Babak Final (Deadline: 9 Agustus 2026)

### P1-01: Jalankan Pilot Test di 1 Desa Nyata

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J5 (PM GovTech), J3 (UI/UX) |
| **Severity** | 🟠 Major |
| **Effort** | 2-3 minggu |

**Deskripsi masalah:** Proposal menyebut target adopsi 420 desa dalam 3 tahun tapi tidak ada bukti bahwa sistem pernah digunakan di satu desa pun.

**Minimal yang harus dilakukan:**
1. Pilih 1 desa terdekat (atau hubungi jaringan kampus)
2. Install AvaraDesa di server desa atau cloud
3. Daftarkan 10 warga sebagai penguji
4. Jalankan selama 2 minggu minimal
5. **Data yang harus dikumpulkan:**
   - Jumlah surat yang diproses
   - Waktu rata-rata per pengajuan
   - Jumlah error / kegagalan sistem
   - Feedback warga (minimal 5 wawancara)
   - Feedback operator desa

---

### P1-02: Load Test — 100 Concurrent Users

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J8 (QA Lead) |
| **Severity** | 🟠 Major |
| **Tools** | k6.io (gratis) atau Apache Benchmark |

**Tujuan:** Mengetahui response time sistem saat 100 warga mengakses bersamaan.

**Script minimal (k6):**
```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '1m', target: 50 },
    { duration: '2m', target: 100 },
    { duration: '1m', target: 0 },
  ],
};

export default function () {
  const res = http.get('https://server-desa/api/v1/informasi');
  check(res, { 'status 200': (r) => r.status === 200 });
  sleep(1);
}
```

**Data yang harus dicantumkan:**
| Metrik | Hasil | Target |
|--------|-------|--------|
| Response time (p50) | ? | <500ms |
| Response time (p95) | ? | <2000ms |
| Error rate | ? | <1% |
| Peak memory usage | ? | <512MB |

---

### P1-03: Perbaiki Login UX — Alternatif untuk 32 Digit

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J3 (UI/UX), J15 (User) |
| **Severity** | 🟠 Major |
| **Solusi** | Tambahkan alternatif login |

**Alternatif login (pilih minimal 2):**

| Metode | Deskripsi | Effort |
|--------|-----------|--------|
| **Scan QR KK** | Scan QR Code di Kartu Keluarga | 4 jam |
| **Scan KTP** | OCR NIK dari foto KTP | 8 jam |
| **NIK + PIN** | Input NIK (16 digit), lalu PIN 4 digit yang disetting saat pertama login | 3 jam |
| **Magic Link via WhatsApp** | Kirim link login ke WhatsApp warga | 5 jam |
| **Biometrik (android)** | Fingerprint / Face unlock setelah NIK | 2 jam |

**Rekomendasi:** Implementasi **NIK + PIN** + **Scan KTP**. NIK 16 digit sekali input + PIN 4 digit.

---

### P1-04: Verifikasi Fitur OpenSID Secara Menyeluruh

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J5 (PM), J14 (Judge) |
| **Severity** | 🟠 Major |

**Tindakan:**
1. Buka https://demosid.opendesa.id — login admin
2. Dokumentasikan fitur yang ADA di OpenSID tapi dianggap "Tidak ada" di proposal
3. Cek OpenSID Mobile di Play Store
4. Cek versi Premium OpenSID jika ada akses
5. Update tabel perbandingan kompetitor agar apples-to-apples

**Format update:** Apapun hasilnya, tabel harus jujur. Jangan mengklaim fitur "Tidak ada" jika sebenarnya ada.

---

### P1-05: Tambah ROI Table untuk Desa

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J12 (Ekonom) |
| **Severity** | 🟠 Major |

**Tabel yang harus ditambahkan:**

| Komponen | Tanpa AvaraDesa | Dengan AvaraDesa | Penghematan/Tahun |
|----------|----------------|------------------|-------------------|
| Waktu operator desa | 8 jam/hari administrasi| 2 jam/hari | 1.560 jam = Rp 31,2 juta* |
| Transportasi warga | Rp 50.000/surat × 500/tahun | Rp 0 | Rp 25 juta |
| Kertas + ATK | Rp 500.000/bulan | Rp 100.000/bulan | Rp 4,8 juta |
| Biaya server | Rp 0 (manual) | Rp 185.000/bulan | **+Rp 2,2 juta** |
| **Net savings** | | | **Rp 58,8 juta/tahun** |

*\*Asumsi: Rp 20.000/jam (UMR operator desa)*

---

### P1-06: Perbaiki Narasi Proposal — Hook Pembuka

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J13 (Journalist) |
| **Severity** | 🟠 Major |

**Format baru untuk Section 1 (Latar Belakang):**

> *"Pukul 06.30 pagi, Ibu Sari — seorang janda dua anak di Desa Sukamaju — sudah berjalan kaki 2 kilometer menuju kantor desa. Ia perlu mengurus surat keterangan tidak mampu untuk beasiswa anaknya. Sesampainya di kantor, Pak RT belum datang. Data tidak ditemukan. Surat harus jadi besok. [paragraf ini menggambarkan scene yang relatable]*
>
> *Di desa lainnya, Pak Kades harus menandatangani 20 surat hari ini. 20 kali menulis tangan. 20 kali stempel. 20 kali tandatangan. Belum lagi rapat, belum lagi kunjungan lapangan. [paragraf ini menggambarkan beban administrator]*
>
> *[Lanjut ke data BPS dan analisis masalah]..."*

---

## 🟡 P2 — Sebelum Presentasi Final (Nice to Have)

### P2-01: Proper RAG dengan Vector Embeddings

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J7 (AI Engineer) |

**Tindakan:** Ganti keyword matching dengan proper vector embeddings:
1. Install `pgvector` (PostgreSQL) atau gunakan embedding API (Gemini/OpenAI)
2. Generate embeddings untuk semua `bot_knowledges`
3. Implementasi cosine similarity search
4. Retire fuzzy matching (atau jadikan fallback)

**Effort:** 8-12 jam.

---

### P2-02: Capacitor Performance Profiling

**Tindakan:**
1. Buka Chrome DevTools / Capacitor DevTools di device Android low-end (2 GB RAM)
2. Ukur startup time, memory usage, average FPS
3. Cantumkan di Section 9

**Hasil minimal yang diharapkan:**
| Metrik | Target | Hasil |
|--------|--------|-------|
| Startup time | <3 detik | ? |
| Memory (idle) | <80 MB | ? |
| FPS (surat list) | 60 FPS | ? |
| FPS (statistik chart) | 30 FPS | ? |

---

### P2-03: Performance Testing (k6 Load Test)

Lihat P1-02.

---

### P2-04: Penetration Testing dengan OWASP ZAP

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J6 (Security) |
| **Effort** | 4 jam |

**Tindakan:**
1. Download OWASP ZAP (gratis)
2. Automated scan semua endpoint API
3. Manual verification temuan
4. Cantumkan hasil: berapa temuan, severity, yang sudah di-fix

---

### P2-05: CI/CD — Security Scanning

| Metadata | Detail |
|----------|--------|
| **Juri terkait** | J4 (DevOps) |

**Tambahkan ke `.github/workflows/ci.yml`:**
```yaml
- name: Composer audit
  run: composer audit --no-dev

- name: NPM audit
  run: npm audit --production
```

---

### P2-06: User Persona dan User Journey

**Berdasarkan data empiris (dari pilot test P1-01):**
1. Buat 3 persona (warga muda, warga lansia, operator desa)
2. Buat user journey map untuk skenario "Pengajuan Surat"
3. Cantumkan di Section 10 (UI/UX)

---

## ⚪ P3 — Jika Ada Waktu

### P3-01: Disaster Recovery Plan

Tambahan section kecil di Section 7/8:
1. Backup database harian (cloud + local)
2. Restore procedure
3. Data recovery dari Capacitor sync jika server mati total

---

### P3-02: Monitoring & Alerting

Tambahan di Section DevOps:
1. Health check endpoint `/up` (sudah ada)
2. UptimeRobot (gratis)
3. Alerting via Telegram Bot

---

### P3-03: Accessibility Audit Results

Jalankan Lighthouse + axe-core, cantumkan skor.

---

### P3-04: Sync Conflict Resolution Enhancement

Tambahkan notifikasi konflik sync: "Data Anda sudah diubah oleh admin. Silakan pull data terbaru."

---

### P3-05: Video Demo Script Detail

Perluas script video 5 menit dengan timing per detik.

---

## 📊 STATUS DASHBOARD

| Kode | Item | PIC | Status | Deadline |
|------|------|-----|--------|----------|
| **P0-01** | Tambah 10 screenshot | TIM | ⚠️ BELUM | 25 Juli |
| **P0-02** | Fix Ollama claim | AI | ✅ DONE | — |
| **P0-03** | Benchmark PDF | AI | ✅ DONE | — |
| **P0-04** | Pangkas proposal ≤40 hlm | AI | ⚠️ ~48 hlm (ACID section added) | 25 Juli |
| **P0-05** | Perbaiki abstrak | AI | ✅ DONE | — |
| **P1-01** | Pilot test desa nyata | TIM | ⏭️ SKIP | — |
| **P1-02** | Load test k6 | TIM | ⚠️ BELUM | 9 Agustus |
| **P1-03** | Login alternatif | AI | ✅ DONE | — |
| **P1-04** | Verifikasi OpenSID | AI | ✅ DONE | — |
| **P1-05** | ROI table | AI | ✅ DONE | — |
| **P1-06** | Hook pembuka narasi | AI | ✅ DONE | — |
| **P2-01** | Proper RAG | — | ⏭️ SKIP | — |
| **P2-02** | Capacitor profiling | TIM | ⏭️ SKIP | — |
| **P2-03** | K6 load test | TIM | ⚠️ BELUM | 9 Agustus |
| **P2-04** | OWASP ZAP | AI | ✅ DONE (manual) | — |
| **P2-05** | CI security audit | AI | ✅ DONE | — |
| **P2-06** | User persona | — | ⏭️ SKIP | — |
| **P3-01** | Disaster recovery | — | ⏭️ SKIP | — |
| **P3-02** | Monitoring | — | ⏭️ SKIP | — |
| **P3-03** | Accessibility audit | — | ⏭️ SKIP | — |
| **P3-04** | Sync conflict notif | AI | ⏭️ SKIP | — |
| **P3-05** | Video script detail | AI | ✅ DONE | — |

---

## 📋 TIMELINE EKSEKUSI

### Minggu 1 (14-20 Juli) — P0 Fokus

| Hari | P0 | P1 | P2 |
|------|----|----|----|
| Senin | **P0-01** — Screenshot | | |
| Selasa | **P0-02** — Fix Ollama | **P1-04** — Verifikasi OpenSID | |
| Rabu | **P0-03** — Benchmark PDF | | **P2-04** — OWASP ZAP |
| Kamis | **P0-04** — Pangkas proposal | | |
| Jumat | **P0-05** — Abstrak baru | **P1-06** — Hook pembuka | |
| Sabtu | **Final review P0** | **P1-05** — ROI table | |

### Minggu 2 (21-27 Juli) — Submission

| Hari | Activity |
|------|----------|
| Senin-Rabu | Integrasi semua perubahan P0 ke proposal |
| Kamis | Review tim + final editing |
| Jumat 25 Juli | **SUBMISSION PROPOSAL** ✅ |
| Sabtu | Mulai P1 items untuk final |

### Minggu 3-4 (28 Jul - 9 Agt) — Persiapan Final

| Item | Deadline |
|------|----------|
| Pilot test desa (P1-01) | Minggu 3 |
| Load test k6 (P1-02, P2-03) | Minggu 3 |
| Login alternatif (P1-03) | Minggu 3 |
| Proper RAG (P2-01) | Minggu 3-4 |
| Capacitor profiling (P2-02) | Minggu 4 |
| CI security audit (P2-05) | Minggu 4 |
| User persona (P2-06) | Minggu 4 |
| **9 Agustus 2026 — FINAL PRESENTATION** | ✅ |

---

> **Dokumen ini adalah acuan perbaikan yang telah dikunci.** Setiap perubahan harus disetujui oleh seluruh anggota tim. Status update dilakukan setiap hari sebelum pulang.
>
> *Generated from 15-persona review panel — J1 to J15.*
