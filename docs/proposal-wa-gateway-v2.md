# Proposal Pengembangan WhatsApp Gateway v5

**Dari:** Tim AvaraDesa / SIG-Udeung  
**Untuk:** Tim Developer WhatsApp Gateway  
**Tanggal:** Juli 2026  
**Status:** Draft — menunggu persetujuan

---

## Daftar Isi

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Arsitektur Target](#2-arsitektur-target)
3. [Fitur P1 — Template Auto-Reply per Sesi + Kategori](#3-fitur-p1--template-auto-reply-per-sesi--kategori)
4. [Fitur P2 — Configurable Behavior per Sesi](#4-fitur-p2--configurable-behavior-per-sesi)
5. [Fitur P3 — Priority Queue + Broadcast Throttle](#5-fitur-p3--priority-queue--broadcast-throttle)
6. [Fitur P4 — Analytics Dashboard + CSV Export](#6-fitur-p4--analytics-dashboard--csv-export)
7. [Fitur P5 — Webhook Reliability (Retry + Dead Letter)](#7-fitur-p5--webhook-reliability-retry--dead-letter)
8. [Fitur P6 — Template Sync dari Laravel](#8-fitur-p6--template-sync-dari-laravel)
9. [Fitur P7 — Multi-Tenant / Multi-Project](#9-fitur-p7--multi-tenant--multi-project)
10. [Fitur P8 — SOCKS5 Proxy Auto-Rotate](#10-fitur-p8--socks5-proxy-auto-rotate)
11. [Fitur P9 — Session Pooling untuk Broadcast Massal](#11-fitur-p9--session-pooling-untuk-broadcast-massal)
12. [Prioritas dan Estimasi](#12-prioritas-dan-estimasi)

---

## 1. Ringkasan Eksekutif

### 1.1 Latar Belakang

WhatsApp Gateway v4 saat ini sudah berjalan dengan Behavior Engine yang mencakup:

- ✅ Online K-Means persona detection (quick/normal/relaxed)
- ✅ Adaptive timing simulation (read → typing → send)
- ✅ Token bucket rate limiting (3/min, 20/hr, 100/day)
- ✅ Safety engine (quiet hours 22:00–07:00, burst detection)
- ✅ Content diversity engine
- ✅ Auto-reconnect exponential backoff
- ✅ SOCKS5 proxy (Cloudflare WARP)
- ✅ Multi-provider AI (OpenAI, Anthropic, Bedrock, Azure)
- ✅ Webhook outbox dengan retry

Namun seiring bertambahnya jumlah pengguna dan project yang terhubung, diperlukan peningkatan arsitektur untuk mendukung:

1. **Multi-project** — satu gateway melayani banyak aplikasi (AvaraDesa, SIG-Udeung, project lain)
2. **Skalabilitas** — broadcast ke ribuan nomor tanpa banned
3. **Fleksibilitas** — admin bisa kustomisasi perilaku bot tanpa deploy ulang
4. **Sinkronisasi** — template dan FAQ di Laravel bisa dipakai gateway tanpa duplikasi data
5. **Monitoring** — visibility penuh ke kesehatan dan performa gateway
6. **Anti-ban maksimal** — 93% → 99% dengan rotating proxy dan session pooling

### 1.2 Project yang Akan Terhubung

| Project | Base URL | Kebutuhan |
|---------|----------|-----------|
| AvaraDesa | `https://avaradesa.id` | Notifikasi surat/mutasi, FAQ bot, AI chatbot |
| SIG-Udeung | `https://udeung.desa.id` | Sama + broadcast berita |
| Project Lain | _(akan menyusul)_ | Akan menggunakan satu gateway yang sama |

---

## 2. Arsitektur Target

```
                            ┌─────────────────────────────┐
                            │     Multi-Tenant Manager     │
                            │  (tenant → session mapping)  │
                            └──────────┬──────────────────┘
                                       │
               ┌───────────────────────┼───────────────────────┐
               │                       │                       │
               ▼                       ▼                       ▼
      ┌─────────────────┐   ┌─────────────────┐   ┌─────────────────┐
      │  Session A      │   │  Session B      │   │  Session C      │
      │  (AvaraDesa WA) │   │  (SIG-Udeung)   │   │  (Project X)    │
      │  behavior: A    │   │  behavior: B    │   │  behavior: C    │
      │  templates: A   │   │  templates: B   │   │  templates: C   │
      └────────┬────────┘   └────────┬────────┘   └────────┬────────┘
               │                       │                       │
               └───────────────────────┼───────────────────────┘
                                       │
                                       ▼
                              ┌─────────────────┐
                              │   Proxy Pool     │
                              │  WARP 1,2,3...   │
                              │  + Datacenter    │
                              └─────────────────┘
                                       │
                                       ▼
                              ┌─────────────────┐
                              │   Baileys WS     │
                              │  (WhatsApp Web)  │
                              └─────────────────┘
```

### 2.1 Alur Pesan Keluar (Outbound)

```
Laravel App
  │
  ├── POST /api/sessions/{id}/messages
  │      { chatId, text, priority: high|normal }
  │
  ▼
Express Router → Auth Middleware
  │
  ▼
Priority Queue (per session)
  ├── high: notifikasi approve/reject → kirim segera
  ├── normal: broadcast berita → antri dengan rate limit
  └── low: promosi/sosialisasi → kirim di jam sepi
  │
  ▼
Behavior Engine
  ├── volume.consume() → token bucket check
  ├── safety.check() → quiet hours?
  └── timing.generate() → read/typing/send delay
  │
  ▼
Proxy Pool → Baileys → WhatsApp
```

### 2.2 Alur Pesan Masuk (Inbound)

```
WhatsApp → Baileys WS
  │
  ▼
messages.upsert event
  │
  ▼
processIncomingMessage()
  ├── 1. OnlineKMeans → update persona
  ├── 2. Volume → token bucket consume
  ├── 3. Safety → burst check + quiet hours
  ├── 4. Content Engine:
  │     ├── FAQ match (dari database session)
  │     ├── Template match (per intent)
  │     ├── AI call (jika FAQ/template tidak cocok)
  │     └── Fallback (pesan default)
  ├── 5. Diversity → vary content
  ├── 6. Timing → read → typing → send delay
  └── 7. Send + log ke behavior_outbox
  │
  ▼
Webhook Outbox → notify Laravel
  ├── event: message.incoming
  ├── event: message.sent
  ├── event: message.failed
  └── retry 5x, lalu dead letter
```

---

## 3. Fitur P1 — Template Auto-Reply per Sesi + Kategori

### 3.1 Kebutuhan

Saat ini template auto-reply di-define di `content.js` secara hardcoded. Admin tidak bisa mengubahnya tanpa deploy ulang.

### 3.2 Spesifikasi

**Endpoint:**

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/sessions/:id/templates` | Lihat semua template |
| `POST` | `/api/sessions/:id/templates` | Tambah template baru |
| `PUT` | `/api/sessions/:id/templates/:templateId` | Update template |
| `DELETE` | `/api/sessions/:id/templates/:templateId` | Hapus template |

**Payload POST/PUT:**
```json
{
  "intent": "greeting",
  "label": "Salam Siang",
  "templates": [
    "Halo {nama}, ada yang bisa saya bantu?",
    "Selamat siang, silakan tanya apa saja ya terkait layanan desa."
  ],
  "is_active": true
}
```

**Intent yang didukung:**

| Intent | Trigger keywords | Contoh |
|--------|-----------------|--------|
| `greeting` | halo, hai, pagi, siang, malam, hi, helo | "Selamat siang, ada yang bisa dibantu?" |
| `information` | siapa, apa itu, jelaskan, info | "Silakan tanya apa yang ingin diketahui." |
| `help` | tolong, bantu, mohon, butuh, minta | "Baik, saya bantu. Silakan sampaikan." |
| `question` | kapan, berapa, dimana, bagaimana | "Silakan tanya, saya siap membantu." |
| `thanks` | terima kasih, makasih, thanks | "Sama-sama, senang bisa membantu." |
| `acknowledge` | selesai, sudah, oke, ok, baik | "Baik, terima kasih." |
| `unknown` | _(fallback)_ | Pesan umum / AI |

**Struktur database (SQLite):**
```sql
CREATE TABLE session_templates (
    id TEXT PRIMARY KEY,
    session_id TEXT NOT NULL,
    intent TEXT NOT NULL,
    label TEXT,
    templates TEXT NOT NULL,  -- JSON array of strings
    is_active INTEGER DEFAULT 1,
    created_at INTEGER,
    updated_at INTEGER,
    FOREIGN KEY (session_id) REFERENCES sessions(session_id)
);
```

### 3.3 Prioritas Content Engine (update dari content.js)

```
1. FAQ (dari tabel bot_knowledges / session_faqs)
2. Template (dari session_templates — cocokkan intent)
3. AI (jika diaktifkan dan FAQ/template tidak cocok)
4. Fallback (pesan default random)
```

---

## 4. Fitur P2 — Configurable Behavior per Sesi

### 4.1 Kebutuhan

Saat ini timing range hardcoded di `timing.js`:
```javascript
const RANGES = {
  quick:   { read: [3, 10],  typing: [15, 40],  send: [20, 90] },
  normal:  { read: [5, 20],  typing: [20, 60],  send: [30, 180] },
  relaxed: { read: [15, 60], typing: [30, 90],  send: [60, 300] },
};
```

Setiap sesi punya kebutuhan berbeda. Contoh:
- Sesi **notifikasi** → quick (tidak perlu simulasi manusia untuk pesan satu arah)
- Sesi **CS chat** → relaxed (perlu simulasi manusia sempurna)
- Sesi **broadcast** → timing singkat, tanpa typing simulation

### 4.2 Spesifikasi

**Endpoint:**

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/sessions/:id/behavior` | Lihat konfigurasi behavior |
| `PUT` | `/api/sessions/:id/behavior` | Update konfigurasi behavior |

**Payload:**
```json
{
  "persona_enabled": true,
  "timing_ranges": {
    "quick":   { "read": [1, 3],   "typing": [5, 15],   "send": [10, 30] },
    "normal":  { "read": [3, 10],  "typing": [10, 30],  "send": [15, 60] },
    "relaxed": { "read": [8, 30],  "typing": [15, 45],  "send": [30, 150] }
  },
  "volume": {
    "per_minute": 3,
    "per_hour": 20,
    "per_day": 100,
    "cooldown_ms": 30000
  },
  "quiet_hours": {
    "start": 22,
    "end": 7,
    "timezone": "Asia/Jakarta"
  },
  "safety": {
    "burst_limit": 3,
    "burst_window_ms": 30000
  },
  "ai_enabled": true,
  "faq_enabled": true,
  "template_enabled": true
}
```

### 4.3 Default per Tipe Sesi

| Tipe Sesi | Persona | Timing | Volume | Quiet Hours | AI |
|-----------|---------|--------|--------|-------------|----|
| **notifikasi** (satu arah) | quick | 1-3s read, 0 typing | 10/min, 100/hr | 22-07 | ❌ |
| **cs_chat** (customer service) | normal | 5-20s read, 20-60s typing | 3/min, 20/hr | 22-07 | ✅ |
| **broadcast** (massal) | quick | 1s read, 0 typing | 2/min, 30/hr | ✅ strict | ❌ |
| **default** | normal | 5-20s read, 20-60s typing | 3/min, 20/hr | 22-07 | ✅ |

---

## 5. Fitur P3 — Priority Queue + Broadcast Throttle

### 5.1 Kebutuhan

Saat ini semua pesan masuk ke queue yang sama. Notifikasi approve/reject surat harus dikirim **segera**, tapi harus menunggu broadcast berita yang mengantri 100 pesan.

### 5.2 Spesifikasi

**Perubahan di `session.js` → `processQueue()`:**

```javascript
// Queue structure
session.queue = {
  high: [],     // notifikasi: approve/reject/selesai → kirim < 30 detik
  normal: [],   // auto-reply, broadcast terbatas → kirim < 5 menit
  low: [],      // broadcast massal, promosi → kirim di jam sepi / rate rendah
};
```

**Throttle per prioritas:**

| Prioritas | Rate Limit | Maks per Broadcast |
|-----------|------------|--------------------|
| high | RATE_LIMIT_MS (1.5s) | 10 pesan |
| normal | RATE_LIMIT_MS × 2 (3s) | 100 pesan |
| low | RATE_LIMIT_MS × 4 (6s) | 500 pesan, hanya jam aktif |

**Endpoint broadcast dengan prioritas:**
```json
POST /api/sessions/:id/messages/broadcast
{
  "targets": ["62812...", "62813..."],
  "text": "Pesan broadcast...",
  "priority": "low",
  "schedule_at": null
}
```

### 5.3 Scheduled Broadcast

```
POST /api/sessions/:id/messages/broadcast
{
  "targets": [...],
  "text": "...",
  "priority": "low",
  "schedule_at": "2026-07-22T08:00:00+07:00"
  // ↑ jika diisi, pesan masuk queue tapi tidak dikirim sampai jam itu
}
```

Cron job di gateway tiap 5 menit: cek `schedule_at` → pindahkan ke queue.

---

## 6. Fitur P4 — Analytics Dashboard + CSV Export

### 6.1 Kebutuhan

Admin tidak punya visibility ke:
- Berapa banyak pesan terkirim per sesi?
- Berapa persen FAQ vs AI vs Template vs Fallback?
- Berapa banyak yang gagal?
- Bagaimana distribusi persona pengirim?

### 6.2 Spesifikasi

**Endpoint:**

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `GET` | `/api/sessions/:id/analytics/summary` | Ringkasan statistik sesi |
| `GET` | `/api/sessions/:id/analytics/volume?range=24h` | Volume per jam (24h / 7d / 30d) |
| `GET` | `/api/sessions/:id/analytics/sources` | Distribusi sumber (FAQ vs Template vs AI vs Fallback) |
| `GET` | `/api/sessions/:id/analytics/personas` | Distribusi persona (quick/normal/relaxed) |
| `GET` | `/api/sessions/:id/analytics/export.csv` | CSV export lengkap |

**Response Summary:**
```json
{
  "period": { "start": "2026-07-01", "end": "2026-07-21" },
  "messages": {
    "sent": 15420,
    "failed": 23,
    "success_rate": 99.85
  },
  "sources": {
    "faq": 3200,
    "template": 8900,
    "ai": 1800,
    "fallback": 1520
  },
  "personas": {
    "quick": 120,
    "normal": 340,
    "relaxed": 80
  },
  "avg_response_time_ms": 3200,
  "active_users": 540
}
```

**CSV Export — kolom:**
```
timestamp, session_id, direction, type, source, persona, response_time_ms, status
2026-07-21T10:00:00Z, main-wa, outbound, text, template, normal, 3200, sent
2026-07-21T10:00:05Z, main-wa, inbound, text, faq, quick, 1500, replied
```

### 6.3 Admin Panel (EJS Pages)

Halaman admin yang sudah ada perlu ditambah:

| Halaman | Dataset |
|---------|---------|
| `/` (Dashboard) | Stat summary, volume chart (24h), recent errors |
| `/sessions/:id` | Detail sesi: volume per jam, persona distribution, source pie |
| `/analytics` | CSV export, date range picker, per-session filter |

---

## 7. Fitur P5 — Webhook Reliability (Retry + Dead Letter)

### 7.1 Kebutuhan

Saat ini webhook retry 5x dengan interval 30s. Jika gagal terus, data hilang. Juga tidak ada mekanisme **ordered delivery** — webhook bisa sampai tidak berurutan.

### 7.2 Spesifikasi

**Perubahan di `webhook.js`:**

```javascript
const WEBHOOK_RETRY = {
  max_attempts: 10,
  intervals: [5, 15, 60, 300, 900, 1800, 3600, 7200, 14400, 28800], // detik
  // 5s → 15s → 1m → 5m → 15m → 30m → 1h → 2h → 4h → 8h
  dead_letter_after: 86400000, // 24 jam → pindahkan ke dead letter
  max_queue_size: 10000, // max webhook pending
};
```

**Dead Letter Queue:**
```sql
CREATE TABLE webhook_dead_letter (
    id TEXT PRIMARY KEY,
    session_id TEXT,
    event TEXT,
    payload TEXT,
    last_error TEXT,
    created_at INTEGER,
    last_attempt_at INTEGER
);
```

**Ordered Delivery (sequencing):**
```javascript
// Setiap webhook punya sequence_number per session
// Laravel / client harus acknowledge dengan sequence_number terakhir
// Gateway baru kirim webhook berikutnya setelah acknowledge diterima
// Fallback: timeout 30s → kirim ulang
```

---

## 8. Fitur P6 — Template Sync dari Laravel

### 8.1 Kebutuhan

Admin set FAQ dan template di Laravel (Filament), tapi gateway butuh data yang sama untuk auto-reply. Saat ini data di dua tempat = tidak sinkron.

### 8.2 Spesifikasi

**Arsitektur:**

```
Option A: Gateway polling (rekomendasi — sederhana)
────────────────────────────────────────────────────
Gateway → GET /api/sync/templates?since={timestamp} → Laravel
         ← { templates: [...], faqs: [...], updated_at: "..." }
         → update local SQLite
         → polling tiap 5 menit

Option B: Laravel push (real-time, tapi kompleks)
────────────────────────────────────────────────────
Laravel → POST /api/sessions/:id/sync → Gateway
         → Gateway update local SQLite
         → Trigger: event di Laravel saat admin simpan template
```

**Endpoint di Laravel (dibuatkan oleh tim Laravel):**
```php
GET /api/v1/gateway/sync/templates?since={iso_timestamp}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "templates": [
      {
        "id": "tpl_001",
        "intent": "greeting",
        "label": "Salam Siang",
        "templates": ["Halo {nama}, ada yang bisa dibantu?", "Selamat siang..."],
        "is_active": true,
        "updated_at": "2026-07-21T10:00:00Z"
      }
    ],
    "faqs": [
      {
        "id": "faq_001",
        "question": "Syarat SKTM",
        "keywords": ["sktm", "tidak mampu", "miskin"],
        "answer": "Syarat SKTM: KTP, KK, Surat keterangan RT/RW...",
        "is_active": true,
        "updated_at": "2026-07-21T09:00:00Z"
      }
    ],
    "synced_at": "2026-07-21T10:00:00Z"
  }
}
```

---

## 9. Fitur P7 — Multi-Tenant / Multi-Project

### 9.1 Kebutuhan

Satu gateway melayani banyak project. Setiap project punya:
- Sesi WhatsApp sendiri (nomor sendiri)
- Konfigurasi behavior sendiri
- Template & FAQ sendiri
- Webhook URL sendiri (masing-masing project)

### 9.2 Spesifikasi

**Struktur tabel:**
```sql
CREATE TABLE tenants (
    tenant_id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    api_key TEXT NOT NULL UNIQUE,
    is_active INTEGER DEFAULT 1,
    created_at INTEGER,
    updated_at INTEGER
);

-- Tambah tenant_id ke sessions
ALTER TABLE sessions ADD COLUMN tenant_id TEXT REFERENCES tenants(tenant_id);
```

**Auth middleware — validasi tenant:**
```javascript
function authMiddleware(req, res, next) {
    const apiKey = req.headers['x-api-key'];
    // API Key sekarang per-tenant, bukan global
    const tenant = db.prepare('SELECT * FROM tenants WHERE api_key = ?').get(apiKey);
    if (!tenant) return res.status(401).json({ error: 'Invalid API key' });
    req.tenant = tenant;
    next();
}
```

**Setiap request otomatis discope ke tenant:**
```javascript
// Semua query session otomatis filter tenant
db.prepare('SELECT * FROM sessions WHERE session_id = ? AND tenant_id = ?').get(id, req.tenant.id);
```

**Webhook dikirim ke URL masing-masing tenant:**
```javascript
// webhook_url di sessions per session, gateway kirim ke Laravel project ybs
// Tenant A → https://avaradesa.id/api/v1/whatsapp/webhook
// Tenant B → https://udeung.desa.id/api/v1/whatsapp/webhook
```

### 9.3 Provisioning Endpoint

```json
POST /api/admin/tenants
{
  "name": "AvaraDesa",
  "session_id": "avara-wa"
}
```

Response:
```json
{
  "tenant_id": "tn_abc123",
  "api_key": "sk_live_xxxxx",
  "session_id": "avara-wa",
  "webhook_url": "https://avaradesa.id/api/v1/whatsapp/webhook"
}
```

---

## 10. Fitur P8 — SOCKS5 Proxy Auto-Rotate

### 10.1 Kebutuhan

Saat cuma 1 proxy (WARP), jika IP-nya diblokir WhatsApp, semua sesi mati. Perlu rotating proxy pool.

### 10.2 Spesifikasi

**Konfigurasi:**
```env
# Multiple proxies, comma-separated
SOCKS5_PROXY_POOL=socks5://user:pass@warp1:40000,socks5://user:pass@warp2:40000,socks5://user:pass@dc1:1080

# Strategy: round-robin | random | latency-based
PROXY_STRATEGY=round-robin

# Health check setiap proxy tiap 60s
PROXY_HEALTH_CHECK_INTERVAL=60000
```

**Logic:**
```javascript
class ProxyPool {
    constructor(proxies, strategy = 'round-robin') {
        this.proxies = proxies.map(url => ({ url, healthy: true, latency: 0 }));
        this.strategy = strategy;
        this.index = 0;
    }

    getNext() {
        const healthy = this.proxies.filter(p => p.healthy);
        if (healthy.length === 0) return null;

        if (this.strategy === 'round-robin') {
            const p = healthy[this.index % healthy.length];
            this.index++;
            return p.url;
        }
        if (this.strategy === 'random') {
            return healthy[Math.floor(Math.random() * healthy.length)].url;
        }
        // latency-based: pilih yang latency terendah
        return healthy.sort((a, b) => a.latency - b.latency)[0].url;
    }

    async healthCheck() {
        for (const p of this.proxies) {
            const start = Date.now();
            try {
                // Test koneksi ke whatsapp
                await fetch('https://web.whatsapp.com', { agent: new SocksProxyAgent(p.url), signal: AbortSignal.timeout(5000) });
                p.healthy = true;
                p.latency = Date.now() - start;
            } catch {
                p.healthy = false;
            }
        }
    }
}
```

**Rotasi otomatis:**
- Jika koneksi WhatsApp terputus (DisconnectReason), ganti proxy sebelum reconnect
- Health check tiap 60s, proxy mati di-skip sampai pulih
- Log: `[session] Proxy rotated: warp1 → dc1`

---

## 11. Fitur P9 — Session Pooling untuk Broadcast Massal

### 11.1 Kebutuhan

Broadcast ke 5000+ nomor dengan 1 sesi memakan waktu: `5000 × 1.5s = 125 menit`. Dengan 4 nomor (4 sesi), waktu turun jadi `31 menit`.

### 11.2 Spesifikasi

**Konsep:**
Satu project punya banyak nomor WhatsApp (multi-session). Broadcast didistribusikan ke semua sesi yang available.

**Endpoint broadcast pool:**
```json
POST /api/tenant/:tenantId/broadcast
{
  "targets": ["62812...", "62813...", "62814...", ...],
  "text": "Pesan broadcast...",
  "priority": "low"
}
```

**Response:**
```json
{
  "broadcast_id": "bc_xxx",
  "total_targets": 5000,
  "sessions_used": 4,
  "estimated_duration_minutes": 31,
  "status": "queued"
}
```

**Distribusi:**
```javascript
function distributeBroadcast(targets, activeSessions) {
    // Bagi rata: tiap session dapat N target
    const chunkSize = Math.ceil(targets.length / activeSessions.length);
    const chunks = [];
    for (let i = 0; i < activeSessions.length; i++) {
        chunks.push({
            session_id: activeSessions[i].session_id,
            targets: targets.slice(i * chunkSize, (i + 1) * chunkSize),
        });
    }
    return chunks;
}
```

**Tracking per broadcast:**
```sql
CREATE TABLE broadcast_jobs (
    id TEXT PRIMARY KEY,
    tenant_id TEXT,
    total_targets INTEGER,
    sent INTEGER DEFAULT 0,
    failed INTEGER DEFAULT 0,
    status TEXT DEFAULT 'queued',  -- queued | running | completed | partial
    created_at INTEGER,
    completed_at INTEGER
);

CREATE TABLE broadcast_assignments (
    id TEXT PRIMARY KEY,
    broadcast_id TEXT,
    session_id TEXT,
    targets TEXT, -- JSON array
    sent INTEGER DEFAULT 0,
    failed INTEGER DEFAULT 0,
    status TEXT DEFAULT 'pending',
    FOREIGN KEY (broadcast_id) REFERENCES broadcast_jobs(id)
);
```

---

## 12. Prioritas dan Estimasi

### 12.1 Prioritasi

| Prioritas | Fitur | Dependensi | Manfaat Utama | Estimasi |
|-----------|-------|------------|---------------|----------|
| **P1** | Template Auto-Reply per Sesi | — | Admin bisa ubah balasan tanpa deploy, hemat AI cost, SOP desa terjamin | 3 hari |
| **P2** | Configurable Behavior per Sesi | — | Timing & volume disesuaikan per use case, anti-ban lebih optimal | 1 hari |
| **P3** | Priority Queue + Broadcast Throttle | P2 | Notifikasi prioritas tidak tertahan broadcast, scalable | 2 hari |
| **P4** | Analytics Dashboard + CSV | — | Visibility penuh, debugging mudah | 3 hari |
| **P5** | Webhook Reliability + Dead Letter | — | Data tidak hilang, ordered delivery | 1 hari |
| **P6** | Template Sync dari Laravel | P1 + endpoint Laravel | Satu source of truth FAQ/template | 2 hari |
| **P7** | Multi-Tenant | P1, P2, P6 | Satu gateway untuk semua project | 3 hari |
| **P8** | Proxy Auto-Rotate | — | 99% anti-ban, no single point of failure | 2 hari |
| **P9** | Session Pooling Broadcast | P7 | Broadcast 5000 nomor dalam 30 menit | 3 hari |

### 12.2 Estimasi Total

| Fase | Fitur | Hari Kerja | Catatan |
|------|-------|-----------|---------|
| **Fase 1** | P1 + P2 | 4 | Core: template + behavior configurable |
| **Fase 2** | P3 + P5 | 3 | Queue priority + webhook reliability |
| **Fase 3** | P4 | 3 | Analytics + monitoring |
| **Fase 4** | P6 + P7 | 5 | Multi-tenant + sync |
| **Fase 5** | P8 + P9 | 5 | Anti-ban maksimal + broadcast massal |
| | **Total** | **~20 hari** | |

### 12.3 Catatan Teknis

1. **Backward compatibility:** Semua endpoint yang ada tidak berubah. Fitur baru ditambahkan tanpa mengubah API yang sudah ada.
2. **Database migration:** Semua perubahan tabel menggunakan `ALTER TABLE` atau tabel baru — tidak ada breaking change ke data existing.
3. **Testing:** Setiap fitur wajib memiliki test (manual dulu, automated menyusul).
4. **Dokumentasi:** Setiap endpoint baru wajib ditambahkan ke README.md.

---

## Lampiran: Contoh Kasus Penggunaan

### Skenario 1: Notifikasi Surat (high priority)

```
Warga ajukan surat → Laravel → POST /api/sessions/desaku-wa/messages
                      { chatId: "62812xxx", text: "Surat Anda telah disetujui",
                        priority: "high" }
                      → Masuk queue HIGH
                      → 1.5s delay → kirim
                      → Webhook ke Laravel: { event: "message.sent" }
```

### Skenario 2: Broadcast Berita (low priority)

```
Admin publish berita → Laravel → POST /api/sessions/desaku-wa/messages/broadcast
                      { targets: [500 nomor], text: "Berita desa...",
                        priority: "low", schedule_at: "08:00" }
                      → Masuk queue LOW (jadwal 08:00)
                      → 6s delay antar kirim
                      → Webhook tiap selesai 100 kirim: { event: "broadcast.progress" }
```

### Skenario 3: Auto-Reply Chat (inbound)

```
Warga chat: "Selamat siang"
→ Behavior Engine → intent: greeting
                  → Template match: "Halo, ada yang bisa dibantu?"
                  → Timing: read 5s, typing 20s, send 10s
                  → Kirim balasan
                  → Webhook ke Laravel: { event: "message.incoming", text: "...",
                    reply: "Halo, ada yang bisa dibantu?", source: "template" }
```

---

## Kontak

Dokumen ini disusun oleh Tim AvaraDesa. Untuk diskusi lebih lanjut, silakan hubungi:
- Email: [alamat email]
- Repo: [link repo]

---
*Dokumen ini siap untuk didiskusikan dan direvisi sesuai kebutuhan tim developer wa-gateway.*
