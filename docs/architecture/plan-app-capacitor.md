# Blueprint Apps (Capacitor & Electron)

## 1. Stack
- **Framework**: Vue 3 (Composition API) + TypeScript
- **Bundler**: Vite
- **UI Framework**: TailAdmin (Admin) & Material Design JS (Warga)
- **Platforms**:
  - Android & iOS (via Capacitor)
  - Windows, macOS & Linux (via Electron)

## 2. API & Sync Strategy
- **Base URL**: `/api/v1` (Reuse API web existing, YAGNI over-engineered split `/api/app/v1`)
- **Offline Data**: IndexedDB (LocalForage) / SQLite Capacitor plugin
- **Sync**: Delta timestamp pulling & transaction queue push (same as original sync plan, written in TypeScript)

## 3. Directory Layout
- `apps/` — standalone SPA
  - `electron/` — Electron main process
  - `src/` — Vue source code
    - `views/warga` — Warga views (Material Design style)
    - `views/admin` — Admin views (TailAdmin template)
  - `capacitor.config.ts` — Capacitor settings
