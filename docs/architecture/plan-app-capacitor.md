# Blueprint Apps (Capacitor & Electron)

## 1. Stack
- **Framework**: Vue 3 (Composition API) + TypeScript
- **Bundler**: Vite 8
- **Styling**: Tailwind CSS v4 (CSS variables kustom — bukan TailAdmin/Material Design)
- **State**: Pinia
- **Router**: Vue Router (hash history)
- **Platforms**:
  - Android & iOS (via Capacitor 8)
  - Windows, macOS & Linux (via Electron)

## 2. API & Sync Strategy
- **Base URL**: `/api/v1` (Reuse API web existing, YAGNI over-engineered split `/api/app/v1`)
- **Auth**: Sanctum token — login warga (NIK+NoKK) dan admin (username+password), plus PIN & biometric
- **Offline Data**: IndexedDB (native, tanpa library) dengan object stores: surat, mutasi, informasi, penduduk, sync_log
- **Sync**: SyncManager — delta timestamp pull (`GET /sync/pull?since=`) & transaction queue push (`POST /sync/push`), auto-sync on network change via Capacitor Network plugin

## 3. Directory Layout (Aktual)
```
apps/
├── electron/              — Electron main process
├── src/                   — Vue source code
│   ├── api/               — HTTP client (fetch), endpoint definitions, native wrappers
│   ├── components/        — UI components (7 reusable)
│   ├── db/                — IndexedDB wrapper & schema
│   ├── layouts/           — WargaLayout (bottom tabs) & AdminLayout (sidebar)
│   ├── router/            — Vue Router config with auth guards
│   ├── stores/            — Pinia stores (authStore, appStore)
│   ├── sync/              — SyncManager, push/pull/queue
│   ├── utils/             — debounce
│   ├── views/             — auth/, warga/, admin/ view pages
│   ├── App.vue
│   ├── main.ts
│   └── style.css          — CSS custom properties (clr-primary, clr-surface, etc.)
├── capacitor.config.ts    — appId: com.avaradesa.app
├── vite.config.ts         — Vue + Tailwind v4 plugin
└── package.json
```

## 4. Native Plugins
| Plugin | Penggunaan |
|--------|------------|
| Camera | `takePhoto()`, `pickFromGallery()` |
| Geolocation | `getCurrentLocation()` |
| Network | `getNetworkStatus()`, `watchNetwork()` — trigger auto-sync |
| Haptics | Feedback getar pada interaksi |
| Device | `getDeviceInfo()` — info perangkat |
| Splash Screen | Native splash |
| Status Bar | Kontrol status bar native |

## 5. Auth Methods
- **Login Warga**: NIK + No KK → token
- **Login Admin**: Username + password → token
- **Login PIN**: NIK + 6-digit PIN (terenkripsi)
- **Login Biometric**: NIK + biometric key (Capacitor native)
- **Persist**: localStorage → `{ state: { token, user } }` — di-hydrate saat startup
