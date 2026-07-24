# AvaraDesa Mobile & Desktop

Aplikasi mobile (Android/iOS via Capacitor) dan desktop (via Electron) untuk AvaraDesa — Sistem Informasi Desa Terpadu.

## Tech Stack

| Lapisan | Teknologi |
|---------|-----------|
| Framework | Vue 3 (`^3.5.39`) — Composition API + `<script setup>` |
| Language | TypeScript (`~6.0.2`) |
| Bundler | Vite (`^8.1.1`) |
| State | Pinia (`^4.0.2`) |
| Router | Vue Router (`^5.2.0`) — hash history |
| Styling | Tailwind CSS v4 (`^4.3.3`) + `@tailwindcss/vite` |
| Mobile | Capacitor 8 (`@capacitor/core ^8.4.2`) — Android & iOS |
| Desktop | Electron (`^43.1.1`) + electron-builder |

## Arsitektur

```
apps/
├── src/
│   ├── api/            — HTTP client, endpoints, native API wrappers
│   │   ├── client.ts   — fetch wrapper (auto-attach Bearer token, 401 redirect)
│   │   ├── endpoints.ts— Semua endpoint REST /api/v1
│   │   ├── native.ts   — Kamera, GPS, Network, Haptics, Device Info
│   │   ├── offlineSync.ts — Queue offline mutations
│   │   └── types.ts    — API response & model types
│   ├── components/     — UI reusable (7 komponen)
│   │   ├── AppButton.vue, AppCard.vue, FormInput.vue
│   │   ├── SkeletonLoader.vue, StatusBadge.vue
│   │   ├── DynamicForm.vue — Render form dari schema JSON
│   │   └── DarkModeToggle.vue
│   ├── db/
│   │   ├── localDatabase.ts — IndexedDB wrapper (getAll, get, put, delete, clear)
│   │   └── schema.ts        — Object store definitions (surat, mutasi, informasi, sync_log)
│   ├── layouts/
│   │   ├── WargaLayout.vue  — Bottom tab nav + skeleton loader
│   │   └── AdminLayout.vue  — Sidebar desktop + bottom nav mobile + skeleton loader
│   ├── router/
│   │   └── index.ts     — Routes: /auth, /warga (14 routes), /admin (13 routes)
│   ├── stores/
│   │   ├── authStore.ts — Login warga/admin, PIN, biometric, persist ke localStorage
│   │   └── appStore.ts  — Online status, dark mode, sidebar
│   ├── sync/
│   │   ├── SyncManager.ts — Offline queue, push/pull, auto-sync on network change
│   │   ├── syncPush.ts    — Push queue operations ke server
│   │   ├── syncPull.ts    — Pull delta updates from server
│   │   └── syncQueue.ts   — Queue management helpers
│   ├── utils/
│   │   └── debounce.ts
│   ├── views/
│   │   ├── auth/        — LoginWarga, LoginAdmin, PinSetup
│   │   ├── warga/       — Dashboard, Surat (KategoriList, BuatSurat, PengajuanList, detail), Mutasi, Informasi, Profil, Keluarga, Statistik
│   │   └── admin/       — Dashboard, Penduduk, SuratManage, MutasiManage, Informasi, KategoriSurat, Fasilitas, Statistik, AuditLog, Keluarga
│   ├── App.vue
│   ├── main.ts          — createApp + Pinia + Router
│   └── style.css        — CSS variables (clr-primary, clr-surface, clr-bg, dsb.)
├── electron/            — Electron main process
├── capacitor.config.ts  — appId: com.avaradesa.app, webDir: dist
├── vite.config.ts       — Vue + Tailwind v4 plugin
├── tsconfig.json
└── package.json
```

## Key Features

### Auth
- Login Warga (NIK + No KK)
- Login Admin (username + password)
- **PIN-based login** untuk akses cepat
- **Biometric login** (sidik jari / face ID via Capacitor)
- Token persist ke localStorage, auto-hydrate saat startup
- 401 auto-redirect ke halaman login sesuai role

### Offline Sync
- **SyncManager** (`sync/SyncManager.ts`): Antrian operasi offline (pengajuan surat, mutasi) di IndexedDB
- **Push queue**: Saat online, kirim operasi tertunda ke `POST /api/v1/sync/push`
- **Pull updates**: Delta sync via `GET /api/v1/sync/pull?since={token}`
- **Auto-sync**: `initAutoSync()` mendengarkan perubahan network (Capacitor), otomatis push+pull saat koneksi tersambung
- **Conflict resolution**: Server mengembalikan `client_id` yang berhasil diproses

### Native APIs (Capacitor)
| API | Fungsi |
|-----|--------|
| Kamera | `takePhoto()` — ambil foto langsung |
| Galeri | `pickFromGallery()` — pilih dari galeri |
| GPS | `getCurrentLocation()` — koordinat real-time |
| Network | `getNetworkStatus()` + `watchNetwork()` — deteksi offline/online |
| Haptics | `hapticLight()`, `hapticSuccess()`, `hapticError()` — feedback getar |
| Device | `getDeviceInfo()` — model, platform, OS version |

### Skeleton Loader
Setiap layout (WargaLayout, AdminLayout) memiliki skeleton loader layout-level:
- `router.beforeEach` set `isLoading = true`
- `router.afterEach` set `isLoading = false` (150ms delay untuk smooth transition)
- CSS `@keyframes mobile-shimmer` dengan CSS variables

### Dark Mode
- `appStore.toggleDark()` + `initTheme()`
- Persistent ke localStorage
- CSS class `dark` di `<html>`
- CSS variables berubah sesuai tema

## Halaman Route

### Auth (`/auth`)
- Login Warga, Login Admin, PIN Setup

### Warga (`/warga` — 14 routes)
Dashboard, Surat (kategori, buat, pengajuan list/detail), Mutasi (list/buat), Informasi (list/detail), Profil, Keluarga, Statistik

### Admin (`/admin` — 13 routes)
Dashboard, Penduduk (list/tambah/edit), SuratManage, MutasiManage, Informasi (list/tambah/edit), KategoriSurat, Fasilitas, Statistik, AuditLog, KeluargaManage

## NPM Scripts

| Script | Perintah |
|--------|----------|
| `npm run dev` | Vite dev server (hot reload) |
| `npm run build` | TypeScript check + Vite build |
| `npm run preview` | Preview production build |
| `npm run cap:sync` | Build + Capacitor sync |
| `npm run electron:dev` | Jalankan Electron dev |
| `npm run electron:build` | Build + Electron package |

## Environment

```env
VITE_API_URL=http://localhost:8000
```

## Catatan

- Menggunakan `createWebHashHistory()` karena static file deployment di Capacitor/Electron
- Auth token disimpan di localStorage key `auth` dengan format `{ state: { token, user } }`
- Semua API call via `/api/v1/*` — backend Laravel yang sama dengan web frontend
- Styling menggunakan CSS variables kustom (bukan TailAdmin atau Material Design)
