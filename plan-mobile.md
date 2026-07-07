# AvaraDesa Mobile & Desktop App — Blueprint

## 1. Project Overview

AvaraDesa is a village administration system (Sistem Informasi Desa) built with Laravel 13 + Vue 3 (Inertia) + Filament for the web platform. This Flutter app is the companion **mobile (.apk)** and **desktop (.exe)** client for the same backend.

### Key Features
- Citizen portal: submit letters, track status, view family data, check village info
- Admin portal: approve/reject submissions, manage data, view statistics
- **Offline-first**: all views work without internet, sync when connected
- **Client-side PDF**: letter PDFs rendered on-device, not on server
- **Realtime polling**: 30-second sync cycle when online

### Constraints
- Single village (AvaraDesa), ~5000 population max
- 1GB RAM / 1 core server — server is light, computation on client
- Indonesian language only
- Must produce: `.apk` (Android) + `.exe` (Windows)

---

## 2. Tech Stack

| Concern | Choice | Rationale |
|---------|--------|-----------|
| Framework | Flutter 3.41+ | Android + Windows from 1 codebase |
| Language | Dart 3.11 | |
| State Management | **Riverpod** (flutter_riverpod) | Simple, testable, no BuildContext needed |
| HTTP Client | **Dio** | Interceptors for auth token + refresh |
| Local DB | **Drift** (SQLite ORM) | Type-safe, reactive queries |
| Key-Value | **Hive** | Sync queue, preferences, cached tokens |
| Secure Storage | **flutter_secure_storage** | Sanctum tokens |
| Auth | **Sanctum** token (Bearer) | Same as web API |
| PDF | **pdf** (client-side) | Render letter PDFs locally |
| Print | **printing** | Windows direct print, Android share |
| Connectivity | **connectivity_plus** | Detect offline/online |
| Icons | **Lucide** (via flutter_svg) | Matching web's lucide icons |
| Font | **Google Fonts** — Instrument Sans | Matching web branding |
| Image Cache | **cached_network_image** | Photos from API |

---

## 3. Backend API Reference

**Base URL:** `{APP_URL}/api/v1`

All 25 existing endpoints are at `/api/v1`. The Flutter app consumes these + 1 new sync endpoint.

### 3.1 Auth (Public)

#### POST /auth/login/warga
Login for citizens using NIK + No.KK.

```json
// Request
{ "nik": "1234567890123456", "no_kk": "1234567890123456" }

// Response 200
{ "message": "Login berhasil", "user": { "nik": "...", "nama_lengkap": "...", ... }, "token": "1|abc123..." }

// Response 422
{ "message": "NIK atau No. KK tidak valid", "errors": { "nik": ["..."] } }
```

#### POST /auth/login/admin
Login for admin using username + password.

```json
// Request
{ "username": "admin", "password": "..." }

// Response 200
{ "message": "Login berhasil", "user": { "id": "...", "username": "...", "role": "keuchik" }, "token": "1|abc123..." }
```

### 3.2 Authenticated (Shared)

All these require `Authorization: Bearer {token}` header.

#### POST /auth/logout
Revoke current token. Response: `{ "message": "Logout berhasil" }`

#### GET /auth/profile
Returns current user data (Penduduk or Administrator depending on login type).

```json
// Warga response
{ "message": "Data profil berhasil diambil", "data": { "nik": "...", "nama_lengkap": "...", "tempat_lahir": "...", "tanggal_lahir": "1990-01-01", "jenis_kelamin": "L", "agama": "Islam", "pendidikan": "SMA", "pekerjaan": "Petani", "status_perkawinan": "Kawin", "status_keluarga": "Kepala Keluarga", "no_kk": "...", "foto_profil": "/storage/...", "telegram_chat_id": null } }

// Admin response
{ "message": "Data profil berhasil diambil", "data": { "id": "...", "username": "admin", "role": "keuchik", "nama_lengkap": "Admin Desa" } }
```

### 3.3 Warga Endpoints (ability: warga)

#### GET /surat/kategori
List all letter categories.

```json
{ "data": [ { "id": 1, "nama": "Surat Keterangan Domisili", "deskripsi": "...", "icon": "home", "persyaratan": ["KTP", "KK"] }, ... ] }
```

#### GET /surat/kategori/{id}
Detail kategori + form fields required.

#### POST /surat/pengajuan
Submit a letter request.

```json
// Request
{ "kategori_surat_id": 1, "data_pengajuan": { "keperluan": "Pembuatan KTP", "alamat_tujuan": "..." }, "keterangan": "opsional" }

// Response 201
{ "message": "Pengajuan surat berhasil dikirim", "data": { "id": 42, "kode_pengajuan": "SRT/001/DESA/2026/01", "status": "menunggu", ... } }
```

#### GET /surat/pengajuan
List user's letter submissions. Paginated.

```json
{ "current_page": 1, "data": [ { "id": 42, "kode_pengajuan": "...", "status": "menunggu", "created_at": "2026-07-05T10:00:00Z", "kategori": { "nama": "Surat Domisili" } } ], "total": 5, "per_page": 10 }
```

Possible statuses: `menunggu`, `diproses`, `disetujui`, `ditolak`, `selesai`.

#### GET /surat/pengajuan/{id}
Detail of a specific submission, includes the `file_surat` URL when approved.

```json
{ "message": "...", "data": { "id": 42, "kode_pengajuan": "...", "status": "disetujui", "data_pengajuan": {...}, "file_surat": "/storage/.../surat.pdf", "kategori": {...}, "tracking": [ { "status": "diajukan", "timestamp": "..." }, { "status": "disetujui", "timestamp": "..." } ] } }
```

#### POST /mutasi
Submit a residency mutation request.

```json
// Request
{ "jenis_mutasi": "datang", "tanggal_mutasi": "2026-07-05", "alamat_asal": "Jl. Merdeka No.1", "alasan": "Pindah domisili" }

// Response 201
{ "message": "Mutasi berhasil diajukan", "data": { ... } }
```

#### GET /mutasi
List user's mutation requests. Paginated.

#### POST /auth/bind-telegram
Bind Telegram chat ID to account. `{ "telegram_chat_id": "123456789" }`

### 3.4 Admin Endpoints (abilities: admin)

#### GET /admin/surat/pengajuan?status=menunggu
List all submissions with optional status filter. Paginated.

#### POST /admin/surat/pengajuan/{id}/approve
Approve a submission. Optional: `{ "catatan": "..." }`

#### POST /admin/surat/pengajuan/{id}/reject
Reject with reason. `{ "alasan_penolakan": "Dokumen tidak lengkap" }`

#### GET /admin/mutasi?status_verifikasi=menunggu
List mutations. Paginated.

#### POST /admin/mutasi/{id}/approve
#### POST /admin/mutasi/{id}/reject
#### CRUD /admin/informasi
Full CRUD for public information (news/articles). Supports `?is_published` filter.

### 3.5 Public Endpoints (No Auth)

#### GET /informasi?kategori={slug}
List public info articles.

#### GET /informasi/{slug}
Single article detail.

```json
{ "message": "...", "data": { "judul": "...", "konten": "HTML content...", "gambar": "/storage/...", "penulis": "Admin", "created_at": "..." } }
```

#### GET /statistik/demografi
```json
{ "message": "...", "data": { "total_penduduk": 1500, "total_kk": 450, "jenis_kelamin": { "laki_laki": 720, "perempuan": 780 }, "agama": { "Islam": 1490, "Kristen": 10 }, "pendidikan": { "SMA": 500, "S1": 200, ... }, "pekerjaan": { "Petani": 400, ... }, "golongan_darah": { "A": 200, ... } } }
```

#### GET /statistik/layanan
```json
{ "message": "...", "data": { "total_pengajuan": 500, "total_selesai": 450, "total_diproses": 30, "total_ditolak": 20 } }
```

#### GET /verifikasi/{hash}
Verify a letter's authenticity by hash from QR code.

```json
{ "message": "Surat ditemukan", "data": { "kode_pengajuan": "...", "status": "disetujui", "kategori": "...", "tanggal_cetak": "..." } }
```

### 3.6 Sync Endpoint (NEW — needs backend implementation)

#### GET /sync/pull?last_sync=2026-07-05T10:00:00Z

Returns changes since `last_sync` for the authenticated user's relevant data. Used for delta sync.

```json
{ "data": { "surat": { "new": [...], "updated": [...], "deleted_ids": [...] }, "mutasi": { "new": [...], "updated": [...], "deleted_ids": [...] }, "informasi": { "new": [...], "updated": [...], "deleted_ids": [...] }, "profile": { updated object or null }, "settings": { updated settings or null }, "server_time": "2026-07-05T12:00:00Z" } }
```

#### POST /sync/push

Push offline-created operations (letters, mutations).

```json
// Request
{ "operations": [ { "client_id": "uuid-v4", "type": "pengajuan_surat", "action": "create", "data": { "kategori_surat_id": 1, ... }, "created_at": "..." }, { "client_id": "uuid-v4-2", "type": "mutasi", "action": "create", "data": { ... }, "created_at": "..." } ] }

// Response 200
{ "results": [ { "client_id": "...", "status": "success", "server_id": 42, "server_created_at": "..." }, { "client_id": "...", "status": "conflict", "message": "Data sudah berubah sejak offline" } ] }
```

---

## 4. Authentication & Token Flow

### 4.1 Login Flow
1. User enters credentials (NIK+NoKK for warga, username+password for admin)
2. POST to `/auth/login/warga` or `/auth/login/admin`
3. On success: store `token` in `flutter_secure_storage`, user data in Hive
4. Store role (`warga` or `admin`) and user info
5. Navigate to home screen

### 4.2 Auto-Login
1. App starts → check `flutter_secure_storage` for existing token
2. If token exists: GET `/auth/profile` to validate
3. If 401: delete token, redirect to login
4. If 200: load user data, navigate to home

### 4.3 Dio Interceptor
```dart
// interceptor.dart
class AuthInterceptor extends Interceptor {
  @override
  void onRequest(RequestOptions options, RequestInterceptorHandler handler) {
    final token = await secureStorage.read(key: 'auth_token');
    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
    }
    options.headers['Accept'] = 'application/json';
    handler.next(options);
  }

  @override
  void onError(DioException err, ErrorInterceptorHandler handler) {
    if (err.response?.statusCode == 401) {
      // Clear token & redirect to login
      secureStorage.delete(key: 'auth_token');
      // Navigate to login screen
    }
    handler.next(err);
  }
}
```

---

## 5. Offline Architecture

### 5.1 Local Database (Drift — SQLite)

Tables mirror a subset of the server data relevant for offline use:

```dart
// database.dart — Drift schema
@DriftDatabase(tables: [
  SuratKategori,
  SuratPengajuan,
  MutasiPenduduk,
  InformasiPublik,
  PendudukProfile,
  Keluarga,
  FasilitasDesa,
  SyncQueue,
  Settings,
])
class AppDatabase extends _$AppDatabase { ... }
```

**SyncQueue table** — operations queued while offline:
```dart
class SyncQueue extends Table {
  TextColumn get clientId => text()();  // UUID v4
  TextColumn get type => text()();      // 'pengajuan_surat' | 'mutasi'
  TextColumn get action => text()();    // 'create'
  TextColumn get data => text()();      // JSON
  DateTimeColumn get createdAt => dateTime()();
  TextColumn get status => text()();    // 'pending' | 'syncing' | 'synced' | 'failed'
  TextColumn? get error => text().nullable()();
  IntColumn? get serverId => integer().nullable()();
}
```

### 5.2 Sync Flow

```
                     App starts / Network restored
                               │
                    ┌──────────▼──────────┐
                    │  1. Push Queue      │  POST /sync/push
                    │   (pending ops)     │  → update local status
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │  2. Pull Delta      │  GET /sync/pull?last_sync={ts}
                    │   (new/updated/deleted)
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │  3. Update Local    │  Upsert into Drift tables
                    │   DB                │  Delete soft-deleted items
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │  4. Update          │  Save new server_time
                    │   last_sync_ts      │  as last_sync timestamp
                    └─────────────────────┘
```

### 5.3 Conflict Resolution
- Operations are **idempotent** — each has a client-side UUID
- **Server-wins** for data conflicts (server's `updated_at` is authoritative)
- For mutations: server checks if the user's profile has changed since last sync
- Failed syncs go to `failed` status with error message, user can retry

### 5.4 Offline Indicators
- Top bar shows "Offline" / "Sync pending (3)" badge
- List items created offline show a clock icon badge
- Pull-to-refresh triggers sync
- Toast when sync completes

---

## 6. Navigation & Routes

```dart
// route_table.dart
final goRouter = GoRouter(
  initialLocation: '/splash',
  routes: [
    GoRoute(path: '/splash', builder: (_, __) => SplashScreen()),
    GoRoute(path: '/login', builder: (_, __) => LoginScreen()),
    GoRoute(path: '/login/admin', builder: (_, __) => AdminLoginScreen()),

    // Warga — bottom nav
    StatefulShellRoute.indexedStack(
      builder: (_, __, navigationShell) => MainShell(navigationShell: navigationShell),
      branches: [
        // Tab 0 — Home
        StatefulShellBranch(routes: [
          GoRoute(path: '/home', builder: (_, __) => HomeScreen()),
        ]),
        // Tab 1 — Surat
        StatefulShellBranch(routes: [
          GoRoute(path: '/surat', builder: (_, __) => SuratListScreen()),
        ]),
        // Tab 2 — Informasi
        StatefulShellBranch(routes: [
          GoRoute(path: '/informasi', builder: (_, __) => InformasiListScreen()),
        ]),
        // Tab 3 — Profile
        StatefulShellBranch(routes: [
          GoRoute(path: '/profil', builder: (_, __) => ProfileScreen()),
        ]),
      ],
    ),

    // Sub-routes
    GoRoute(path: '/surat/kategori/:id', ...),
    GoRoute(path: '/surat/pengajuan/:id', ...),
    GoRoute(path: '/surat/buat/:kategoriId', ...),
    GoRoute(path: '/surat/pdf/:id', ...),
    GoRoute(path: '/mutasi', ...),
    GoRoute(path: '/mutasi/buat', ...),
    GoRoute(path: '/fasilitas', ...),
    GoRoute(path: '/statistik', ...),
    GoRoute(path: '/informasi/:slug', ...),
    GoRoute(path: '/verifikasi/:hash', ...),
    GoRoute(path: '/profil/keluarga', ...),
    GoRoute(path: '/pengaturan', ...),

    // Admin — sidebar nav (desktop) or bottom nav (mobile)
    GoRoute(path: '/admin/home', ...),
    GoRoute(path: '/admin/surat', ...),
    GoRoute(path: '/admin/surat/:id', ...),
    GoRoute(path: '/admin/mutasi', ...),
    GoRoute(path: '/admin/mutasi/:id', ...),
    GoRoute(path: '/admin/informasi', ...),
    GoRoute(path: '/admin/informasi/buat', ...),
    GoRoute(path: '/admin/informasi/:id/edit', ...),
    GoRoute(path: '/admin/statistik', ...),
  ],
);
```

### Navigation by Role
- **Warga**: Bottom nav with 4 tabs (Home, Surat, Informasi, Profil)
- **Admin**: Sidebar (desktop) or bottom nav (mobile) with admin sections
- **Unauthenticated**: Login screens only

### Desktop-Specific
- Use `NavigationRail` or sidebar instead of BottomNavigationBar
- Window min size: 1024x768
- AppBar with window controls (native Windows frame)

---

## 7. Screen Specifications

### 7.1 Splash Screen
- Display logo + "AvaraDesa"
- Check auth token → auto-login or redirect to login
- If online: trigger initial sync
- Duration: 2s minimum, or until auth check completes

### 7.2 Login Screen
- Two tabs: "Warga" / "Admin"
- Warga: NIK (16-digit) + No. KK (16-digit) fields
- Admin: Username + Password fields
- Validation: required, NIK format must be numeric 16 chars
- Loading state: disable button, show spinner
- Error: show message from API (invalid credentials)
- Success: store token, navigate to home

### 7.3 Home Screen (Warga)
```
┌──────────────────────┐
│ AvaraDesa        🔔  │  AppBar with settings bell
│ Selamat datang,      │
│   Ulya Farhan!      │  Greeting with name
├──────────────────────┤
│ [Statistik Card]      │
│   Total Penduduk:1500│  Row of stat cards
│   Surat Aktif: 3    │  (from cached statistik)
├──────────────────────┤
│ Layanan Cepat        │
│ ┌────┐ ┌────┐       │
│ │Ajuan│ │Cek  │       │  Quick action grid
│ │Surat│ │Status│      │
│ └────┘ └────┘       │
│ ┌────┐ ┌────┐       │
│ │Info │ │Fasil│       │
│ │Desa│ │itas │      │
│ └────┘ └────┘       │
├──────────────────────┤
│ Pengajuan Terbaru    │
│ • Surat Domisili     │  Latest 3 submissions
│   ✅ Disetujui      │  with status + timestamp
│ • Surat SKTM        │
│   ⏳ Diproses       │
└──────────────────────┘
```

**Data sources:**
- Stat cards: local Drift (cached from `/statistik/demografi` + `/statistik/layanan`)
- Quick actions: static
- Recent submissions: local Drift (cached from `/surat/pengajuan`)

**Offline:** Full functionality, uses cached data.

### 7.4 Surat — Kategori List
- Grid of category cards (icon + name + description)
- Tap → navigate to buat surat form
- Data from: local cache from `/surat/kategori`
- Offline: full functionality

### 7.5 Surat — Buat (Form)
```
┌──────────────────────┐
│ ← Buat Surat Domisili│
├──────────────────────┤
│ Kategori: Domisili   │  Read-only
│                      │
│ Keperluan:          │  TextField
│ [________________]  │
│                      │
│ Keterangan:          │  Optional TextField
│ [________________]  │
│                      │
│ Persyaratan:         │
│ ☑ KTP               │  Checkbox list
│ ☑ KK                │
│                      │
│ [      Ajukan      ] │  Button (saved offline if no network)
└──────────────────────┘
```

**Validation:**
- Keperluan: required, min 10 chars
- Persyaratan: must check all required items

**Submit behavior:**
1. If online: POST to `/surat/pengajuan` → get `server_id` → save to local
2. If offline: generate UUID → save to local Drift + SyncQueue (pending) → show "Tersimpan offline, akan dikirim saat online"

### 7.6 Surat — List Pengajuan
```
┌──────────────────────┐
│ ← Pengajuan Surat    │  [Sync badge: 3 pending]
├──────────────────────┤
│ [Tabs: Semua |       │
│  Menunggu | Selesai]  │
├──────────────────────┤
│ SRU/001/DESA/2026/01│  Card per item
│ Surat Domisili      │
│ 🟡 Diproses        │  Status badge (color coded)
│ 05 Jul 2026         │
├──────────────────────┤
│ SRU/002/DESA/2026/01│
│ Surat Keterangan    │
│ 🟢 Disetujui       │
│ 04 Jul 2026         │
└──────────────────────┘
```

**Status colors:** menunggu=🟡, diproses=🔵, disetujui=🟢, ditolak=🔴, selesai=🟣

### 7.7 Surat — Detail & PDF
- Full detail of submission including tracking timeline
- If approved: "Lihat/Cetak Surat" button
- PDF generation: render client-side using `pdf` package (same format as server's DOMPDF)
- Desktop: direct print dialog via `printing` package
- Mobile: share PDF file

### 7.8 Client-Side PDF Rendering

The `pdf` package generates the PDF locally using the same template format:

```dart
Future<Uint8List> generateSuratPdf({
  required SuratPengajuan data,
  required PengaturanGampong settings,
}) async {
  final pdf = Document();
  pdf.addPage(MultiPage(
    pageFormat: PdfPageFormat.a4,
    margin: EdgeInsets.all(32),
    build: (context) => [
      // Kop surat
      HeaderLogo(
        logo: await logoImage,
        namaDesa: settings.namaGampong,
        alamat: settings.alamatLengkap,
      ),
      // Garis pemisah
      LineSeparator(),
      // Nomor surat
      Text('Nomor: ${data.kodePengajuan}'),
      // Isi surat (different per kategori)
      SuratBody(kategori: data.kategori, data: data),
      // Tanda tangan
      SignatureBlock(kepalaDesa: settings.kepalaDesa),
      // QR Code
      QrCode(hash: data.hash),
    ],
  ));
  return pdf.save();
}
```

### 7.9 Mutasi — List & Buat
Similar pattern to Surat:
- List page with all mutation requests
- Create page with form fields (jenis, tanggal, alamat_asal, alasan)
- Tracking status
- Offline queue support

### 7.10 Informasi Publik
```
┌──────────────────────┐
│ ← Informasi Desa     │
├──────────────────────┤
│ ┌────────────────┐  │
│ │ [Gambar]       │  │  Card with image
│ │ Judul Berita   │  │
│ │ 05 Jul 2026    │  │
│ └────────────────┘  │
│ ┌────────────────┐  │
│ │ [Gambar]       │  │
│ │ Judul Berita 2 │  │
│ └────────────────┘  │
└──────────────────────┘
```

- Tap → detail page with full content (HTML rendered via `flutter_widget_from_html` or `flutter_html`)
- Offline: cached articles are available

### 7.11 Statistik
```
┌──────────────────────┐
│ ← Statistik Desa     │
├──────────────────────┤
│ [Tab: Demografi /    │
│       Layanan]       │
├──────────────────────┤
│         Demografi    │
│ ┌───┐ ┌───┐        │
│ │   │ │   │        │
│ │ 720│ │1500│       │  Stat cards with icons
│ │Laki│ │Total│      │
│ └───┘ └───┘        │
│                      │
│ Agama               │  Pie chart or horizontal bar
│ ████████░ Islam 99% │
│ ██░░░░░░░ Kristen1%│
│                      │
│ Pendidikan           │  Bar chart
│ ████████░ SMA   33% │
│ ██████░░░ S1    20% │
│ ████░░░░░ SMP  15% │
└──────────────────────┘
```

Use `fl_chart` package for charts.

### 7.12 Fasilitas Desa
```dart
// read-only, grouped by jenis_fasilitas
Group: "Pendidikan"
├── SD Negeri 1 — Baik
├── Perpustakaan Desa — Baik
Group: "Kesehatan"
├── Posyandu Melati — Baik
├── Puskesdes — Rusak Ringan
```

### 7.13 Profil & Keluarga
- Display user profile (from cached `/auth/profile`)
- Display family members (from API endpoint)
- Edit profile (some fields)
- Logout button

### 7.14 Admin Screens
- Custom nav (sidebar on desktop)
- Dashboard: stats overview, recent submissions widget
- Surat management: list all, filter by status, approve/reject
- Mutasi management: same pattern
- Informasi management: create/edit/delete articles

---

## 8. Folder Structure

```
lib/
├── main.dart                          # Entry point
├── app.dart                           # MaterialApp.router config
├── core/
│   ├── api/
│   │   ├── api_client.dart            # Dio instance + interceptors
│   │   ├── api_endpoints.dart         # All endpoint constants
│   │   └── api_response.dart          # Generic response models
│   ├── database/
│   │   ├── app_database.dart          # Drift database definition
│   │   ├── tables/                    # Table definitions
│   │   └── daos/                      # Data access objects
│   ├── sync/
│   │   ├── sync_service.dart          # Orchestrates push + pull
│   │   ├── sync_queue_service.dart    # Queue management
│   │   └── sync_scheduler.dart        # Periodic + connectivity trigger
│   ├── network/
│   │   └── connectivity_service.dart  # Online/offline detection
│   ├── storage/
│   │   ├── secure_storage.dart        # flutter_secure_storage wrapper
│   │   └── preferences_service.dart   # Hive-based preferences
│   └── theme/
│       ├── app_theme.dart             # Light theme definition
│       ├── app_colors.dart            # Color constants
│       └── app_typography.dart        # Text styles
├── features/
│   ├── auth/
│   │   ├── data/
│   │   │   ├── auth_repository.dart   # API calls
│   │   │   └── auth_local.dart        # Local session + token
│   │   ├── domain/
│   │   │   └── user_model.dart        # User entity
│   │   └── presentation/
│   │       ├── provider/
│   │       │   └── auth_provider.dart # Auth state + notifier
│   │       ├── splash_screen.dart
│   │       └── login_screen.dart
│   ├── home/
│   │   └── presentation/
│   │       ├── provider/
│   │       │   └── home_provider.dart
│   │       ├── home_screen.dart
│   │       └── widgets/
│   │           ├── stat_card.dart
│   │           ├── quick_action_grid.dart
│   │           └── recent_submissions.dart
│   ├── surat/
│   │   ├── data/
│   │   │   ├── surat_repository.dart
│   │   │   └── surat_local.dart       # Drift DAO wrappers
│   │   ├── domain/
│   │   │   └── surat_models.dart
│   │   └── presentation/
│   │       ├── provider/
│   │       │   └── surat_provider.dart
│   │       ├── kategori_list_screen.dart
│   │       ├── buat_surat_screen.dart
│   │       ├── pengajuan_list_screen.dart
│   │       ├── pengajuan_detail_screen.dart
│   │       └── widgets/
│   │           ├── kategori_card.dart
│   │           ├── pengajuan_card.dart
│   │           └── status_badge.dart
│   ├── mutasi/                       # Mirrors surat pattern
│   ├── informasi/                     # List + detail
│   ├── fasilitas/                     # Read-only grouped list
│   ├── statistik/                     # Charts + cards
│   ├── profil/                        # Profile + family
│   └── admin/                         # Admin dashboard
├── shared/
│   ├── widgets/
│   │   ├── app_scaffold.dart          # Platform-aware scaffold
│   │   ├── sync_indicator.dart        # Sync status badge
│   │   ├── empty_state.dart
│   │   ├── error_state.dart
│   │   ├── loading_shimmer.dart
│   │   └── avatar_with_name.dart
│   └── constants/
│       ├── api_constants.dart         # Base URL, timeouts
│       └── app_constants.dart         # Shared preferences keys
└── platform/
    ├── android/                       # Android-specific: FCM, permissions
    └── windows/                       # Windows-specific: print, file dialogs
```

---

## 9. State Management (Riverpod)

```dart
// Core providers
final dioProvider = Provider<Dio>((ref) => createDio(ref));
final databaseProvider = Provider<AppDatabase>((ref) => AppDatabase());
final secureStorageProvider = Provider<FlutterSecureStorage>((ref) => FlutterSecureStorage());

// Auth
final authProvider = AsyncNotifierProvider<AuthNotifier, AuthState>(AuthNotifier.new);

// Data (cache-first)
final suratKategoriProvider = FutureProvider<List<Kategori>>((ref) async {
  final repo = ref.watch(suratRepositoryProvider);
  return repo.getKategori();  // tries local first, then API
});

final suratPengajuanProvider = FutureProvider<List<Pengajuan>>((ref) async {
  final repo = ref.watch(suratRepositoryProvider);
  return repo.getPengajuan();
});

// Sync
final syncStatusProvider = StateProvider<SyncStatus>((ref) => SyncStatus.idle);
final syncQueueCountProvider = Provider<int>((ref) {
  final db = ref.watch(databaseProvider);
  return db.syncQueueCount();  // reactive from Drift
});
```

**Pattern:** Repository fetches from local DB first (instant), triggers API call in background, updates local DB which triggers UI rebuild via Drift's reactive streams.

---

## 10. Theme

### Colors
```dart
// From web: teal primary, amber secondary, slate neutrals
static const primary = Color(0xFF0D9488);      // teal-600
static const primaryLight = Color(0xFF14B8A6);  // teal-500
static const primaryDark = Color(0xFF0F766E);   // teal-700
static const secondary = Color(0xFFD97706);     // amber-600
static const secondaryLight = Color(0xFFF59E0B);// amber-500
static const surface = Color(0xFFF8FAFC);       // slate-50
static const onSurface = Color(0xFF1E293B);     // slate-800
static const onSurfaceVariant = Color(0xFF64748B); // slate-500
static const error = Color(0xFFDC2626);         // red-600
```

### Typography
```dart
// Google Fonts: Instrument Sans (same as web)
TextStyle headlineLarge = GoogleFonts.instrumentSans(fontSize: 28, fontWeight: FontWeight.w700);
TextStyle titleLarge = GoogleFonts.instrumentSans(fontSize: 20, fontWeight: FontWeight.w600);
TextStyle bodyLarge = GoogleFonts.instrumentSans(fontSize: 16, fontWeight: FontWeight.w400);
TextStyle bodySmall = GoogleFonts.instrumentSans(fontSize: 14, fontWeight: FontWeight.w400);
TextStyle labelLarge = GoogleFonts.instrumentSans(fontSize: 14, fontWeight: FontWeight.w600);
```

### Material 3 Theme
```dart
ThemeData(
  useMaterial3: true,
  colorScheme: ColorScheme.fromSeed(
    seedColor: primary,
    primary: primary,
    secondary: secondary,
    error: error,
    surface: surface,
  ),
  fontFamily: 'Instrument Sans',  // via google_fonts
);
```

---

## 11. Platform-Specific

### Android
- **Min SDK:** 24 (Android 7.0)
- **Target SDK:** 34 (Android 14)
- **Permissions:**
  - `INTERNET` (default)
  - `CAMERA` (for KTP photo)
  - `READ_EXTERNAL_STORAGE` / `READ_MEDIA_IMAGES` (API 33+)
  - `POST_NOTIFICATIONS` (API 33+, for FCM)
- **FCM / Push:** For notification when letter status changes
  - Backend sends push via FCM when admin approves/rejects
  - On notification tap: open the relevant pengajuan detail screen
- **App Name:** "AvaraDesa"
- **Package Name:** `com.avaradesa.app`

### Windows
- **Min window size:** 1024 x 768
- **Title:** "AvaraDesa — Sistem Informasi Desa"
- **Print:** Use `printing` package for direct Print dialog
- **File save:** Save PDF to Downloads folder
- **Taskbar:** Single instance, app icon matching Android icon

### Adaptive Widgets
Use `PlatformX` pattern or `Theme.of(context).platform`:
- Mobile: `BottomNavigationBar`, `CupertinoDialog`, swipe back gesture
- Desktop: `NavigationRail`, `AlertDialog`, keyboard shortcuts

```dart
// Example adaptive scaffold
class AppScaffold extends StatelessWidget {
  // Mobile: Scaffold with BottomNavigationBar
  // Desktop: Scaffold with NavigationRail
}
```

---

## 12. Dependencies (pubspec.yaml)

```yaml
name: avaradesa_app
description: AvaraDesa Mobile & Desktop Application
publish_to: 'none'
version: 1.0.0+1

environment:
  sdk: ^3.11.0

dependencies:
  flutter:
    sdk: flutter

  # State Management
  flutter_riverpod: ^2.6.1
  riverpod_annotation: ^2.6.1

  # Navigation
  go_router: ^14.8.1

  # Networking
  dio: ^5.7.0
  connectivity_plus: ^6.1.2

  # Local Database (Drift)
  drift: ^2.22.1
  sqlite3_flutter_libs: ^0.5.28
  path_provider: ^2.1.5
  path: ^1.9.1

  # Key-Value + Secure Storage
  hive: ^2.2.3
  hive_flutter: ^1.1.0
  flutter_secure_storage: ^9.2.4

  # UI
  google_fonts: ^6.2.1
  flutter_svg: ^2.0.16
  cached_network_image: ^3.4.1
  fl_chart: ^0.69.2
  flutter_html: ^3.0.0-beta.2
  shimmer: ^3.0.0

  # PDF
  pdf: ^3.11.1
  printing: ^5.13.4

  # Utils
  intl: ^0.19.0
  uuid: ^4.5.1
  image_picker: ^1.1.2
  url_launcher: ^6.3.1

  # Notifications
  firebase_messaging: ^15.2.1
  firebase_core: ^3.12.1
  flutter_local_notifications: ^18.0.1

dev_dependencies:
  flutter_test:
    sdk: flutter
  flutter_lints: ^5.0.0
  drift_dev: ^2.22.1
  build_runner: ^2.4.14
  riverpod_generator: ^2.6.3
  mockito: ^5.4.6
  mocktail: ^1.0.4
```

---

## 13. Build Commands

```bash
# Development
flutter run -d android        # Run on connected Android device/emulator
flutter run -d windows        # Run on Windows desktop

# Android APK (release)
flutter build apk --split-per-abi
# → build/app/outputs/flutter-apk/app-arm64-v8a-release.apk
# → build/app/outputs/flutter-apk/app-armeabi-v7a-release.apk
# → build/app/outputs/flutter-apk/app-x86_64-release.apk

# Android App Bundle (for Play Store)
flutter build appbundle

# Windows EXE (release)
flutter build windows
# → build/windows/x64/runner/Release/avaradesa_app.exe
```

**For .apk signing** (required for release):
```bash
keytool -genkey -v -keystore avaradesa-keystore.jks \
  -keyalg RSA -keysize 2048 -validity 10000 -alias avaradesa
flutter build apk --split-per-abi
```

---

## 14. Testing Strategy

| Level | Tool | Focus |
|-------|------|-------|
| Unit | `flutter_test` + `mocktail` | Repositories, services, providers |
| Widget | `flutter_test` | Screen rendering, user interaction |
| Integration | `integration_test` | Full flow: login → surat → sync |

**Key tests:**
- Auth: login success, login failure, auto-login, logout
- Offline: submit surat while offline → queue → sync when online
- PDF: generate PDF matches expected output
- State: Riverpod provider state transitions
- Platform: responsive layout (mobile vs desktop)

---

## 15. Implementation Order

### Phase 1: Foundation (Day 1-2)
1. Flutter project scaffold + all dependencies
2. Theme, colors, typography
3. Dio client + auth interceptor + secure storage
4. Drift database schema + DAOs
5. Riverpod providers setup
6. GoRouter configuration + navigation shell
7. Splash screen + auto-login
8. Login screens (warga + admin)

### Phase 2: Core Features (Day 3-5)
9. Home screen with stats + quick actions
10. Surat kategori list + buat form with offline queue
11. Surat pengajuan list + detail screen
12. Mutasi list + buat form with offline queue
13. Client-side PDF generation (all 5 letter types)

### Phase 3: Informasi + Statistik (Day 6-7)
14. Informasi publik list + detail (HTML render)
15. Statistik demografi + layanan with charts
16. Fasilitas desa (grouped read-only)
17. Profil + keluarga screen

### Phase 4: Sync Engine (Day 8-9)
18. Sync service (push pending → pull delta → update local)
19. Connectivity listener (auto-sync when online)
20. Sync queue management UI (pending count, retry failed)
21. Conflict resolution handling

### Phase 5: Admin + Polish (Day 10-12)
22. Admin dashboard with stats
23. Admin surat management (list, approve, reject)
24. Admin mutasi management
25. Admin informasi CRUD
26. Desktop adaptive layout (NavigationRail)
27. Android: FCM push notifications
28. Windows: print dialog, file save
29. Error handling, empty states, loading shimmers
30. Testing + build `.apk` + `.exe`

---

## 16. Key Design Decisions

1. **Same API as web** — not a separate API. Single backend serves web + mobile + desktop. Only added 1 new endpoint (`/sync`).
2. **Client-side PDF** — server stores data only, PDF rendered on-device. Reduces server CPU load significantly.
3. **Polling, not WebSocket** — 30-second poll cycle is sufficient for village-scale usage. FCM for important push notifications on Android.
4. **Server-wins conflict** — for village apps, conflicts are rare (each user edits own data). Server's version is authoritative.
5. **Riverpod over BLoC** — simpler API, no separate event/state classes, better for rapid development.
6. **Material 3** — modern look, adaptive to platform (Material on Android, Material + desktop cues on Windows).
7. **No Firebase for auth** — Sanctum token auth is sufficient. Firebase only for push notifications (optional Phase 5).

---

## 17. Error Handling Patterns

```dart
// API errors — always show user-friendly messages
try {
  await suratRepository.submit(data);
} on DioException catch (e) {
  if (e.type == DioExceptionType.connectionTimeout || e.type == DioExceptionType.connectionError) {
    // Offline — save to local queue
    await suratLocal.savePending(data);
  } else if (e.response?.statusCode == 422) {
    // Validation error — show field-level errors
    showErrors(e.response?.data['errors']);
  } else {
    // Generic error
    showSnackbar('Terjadi kesalahan. Silakan coba lagi.');
  }
}

// Data states
class AsyncValue<T> {
  const AsyncValue.loading();
  const AsyncValue.data(T value);
  const AsyncValue.error(String message, [T? previousData]);
}
```

---

*Document generated for AI-assisted development. Use this as the single source of truth for the Flutter app architecture, features, and behavior.*
