# AvaraDesa — AGENTS.md

## Project
- **Nama**: AvaraDesa (Sistem Informasi Desa Terpadu)
- **Stack**: Laravel 13 + Filament 5 + Inertia/Vue 3 + Tailwind v4
- **Mobile**: Capacitor (Android/iOS) — Vue Router, bukan Inertia
- **Desktop**: Electron
- **Database**: MySQL
- **Auth**: Sanctum (API), Filament auth (admin), custom guard (warga)
- **AI**: Laravel AI + Gemini/OpenAI/DeepSeek/Ollama
- **Notifikasi**: WhatsApp (dual-provider: wa-gateway + Fonnte) + Telegram
- **Logging**: spatie/laravel-activitylog

## Struktur
```
app/
├── Console/Commands/       — Artisan commands (system:cleanup, log:migrate-from-audit)
├── Filament/               — Admin panel
│   ├── AdminPanelProvider.php
│   ├── Auth/AdminLogin.php
│   ├── Pages/              — Dashboard, PengaturanSistem, PengaturanKontenPublik
│   ├── Resources/          — CRUD resources untuk semua model
│   └── Widgets/            — AdminStatsOverview, ServerPerformance, TrafficChart, RecentSubmissions
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          — Notification settings controllers
│   │   ├── Api/            — REST API controllers
│   │   └── Web/            — Inertia page controllers (Citizen, PublicPortal)
│   └── Middleware/          — TrackTraffic, AddSecurityHeaders, HandleInertiaRequests, etc.
├── Models/                 — Eloquent models (Penduduk, Keluarga, Surat, etc.)
├── Policies/               — Authorization policies
└── Services/               — Business logic (WhatsAppService, TelegramService, AuthService, SystemLogger, etc.)
apps/
├── src/                    — Vue 3 + Capacitor mobile app
└── electron/               — Electron desktop app
resources/
├── js/                     — Vue 3 frontend (Inertia pages + components)
├── css/                    — Styles
└── views/                  — Blade templates (Filament, layouts, PDF templates)
routes/
├── web.php                 — Web routes (public + warga + admin notification)
├── api.php                 — REST API routes
├── console.php             — Schedule + custom commands
└── channels.php            — Broadcast channels
```

## Bahasa
- Kode, error, file paths, commands: tetap aslinya
- Respons: Bahasa Indonesia
- UI: Bahasa Indonesia

## Memory Workflow (KONTEKS)

### Awal sesi
```
memory({ mode: "search", query: "project context architecture decisions" })
memory({ mode: "search", query: "current task status blockers" })
memory({ mode: "profile" })
```

### Akhir sesi
```
memory({ mode: "add", content: "## Session Summary\n- Task: <ringkasan>\n- Files modified: <path>\n- Decisions: <keputusan>\n- Next steps: <langkah>" })
```

## Ponytail Rules
- YAGNI: gak dibutuhin sekarang → skip
- Stdlib dulu: PHP/Laravel built-in sebelum custom
- One-liner: minimal code yang works
- Bug fix = root cause, bukan symptom
- No unrequested abstractions
- Mark simplifications: `// ponytail: <alasan>`

## VIBE Init
- **Init date**: 2026-07-24
- **Project**: AvaraDesa (Sistem Informasi Desa Terpadu)
- **Stack**: Laravel 13, Filament 5, Vue 3 + Inertia, Tailwind v4, MySQL
- **Mobile**: Capacitor 8 (Android/iOS) + Vue Router
- **Desktop**: Electron 43
- **Auth**: Sanctum + Filament + custom guard
- **AI**: Laravel AI (Gemini/OpenAI/DeepSeek/Ollama)
- **Notifikasi**: WhatsApp (wa-gateway + Fonnte), Telegram
- **Commits**: 65
- **Models**: 28 Eloquent
- **Controllers**: 11 (6 API, 5 Web)
- **Tests**: 43 file PHPUnit
- **Codebase**: Indexed via codebase-memory-mcp
