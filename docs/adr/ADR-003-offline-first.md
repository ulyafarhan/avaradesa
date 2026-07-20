# ADR-003: Offline-First Architecture

**Status:** Accepted  
**Date:** 2026-07-14  

## Context
Desa sering memiliki koneksi internet tidak stabil. Aplikasi harus tetap berfungsi tanpa internet.

## Decision
Capacitor Vue 3 app menggunakan offline-first:
1. **Local DB**: IndexedDB via localForage / SQLite Capacitor plugin — semua data di-cache lokal.
2. **Sync Engine**: Push offline mutations → Pull delta changes.
3. **Conflict Resolution**: Server-wins (last-write-wins).

## Rationale
1. IndexedDB adalah standar browser database, localForage memberikan API sederhana yang konsisten.
2. Untuk native device, SQLite Capacitor plugin memberikan performa lebih tinggi.
3. Sync engine memungkinkan create/update offline, sinkronisasi saat online.
4. Untuk skala desa (5000 penduduk), server-wins conflict resolution cukup.

## Consequences
1. Backend perlu endpoint `/sync/pull` dan `/sync/push`.
2. Setiap mutation perlu client_id (UUID) untuk idempotency.
3. Token disimpan di capacitor-secure-storage-plugin untuk keamanan.
4. Kompleksitas testing meningkat (offline → online scenario).
