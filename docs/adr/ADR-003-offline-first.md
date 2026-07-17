# ADR-003: Offline-First Architecture

**Status:** Accepted  
**Date:** 2026-07-14  

## Context
Desa sering memiliki koneksi internet tidak stabil. Aplikasi harus tetap berfungsi tanpa internet.

## Decision
Flutter app menggunakan offline-first:
1. **Local DB**: Drift (SQLite) — semua data di-cache lokal.
2. **Sync Engine**: Push offline mutations → Pull delta changes.
3. **Conflict Resolution**: Server-wins (last-write-wins).

## Rationale
1. SQLite adalah standar mobile database, Drift memberikan type-safe queries.
2. Sync engine memungkinkan create/update offline, sinkronisasi saat online.
3. Untuk skala desa (5000 penduduk), server-wins conflict resolution cukup.

## Consequences
1. Backend perlu endpoint `/sync/pull` dan `/sync/push`.
2. Setiap mutation perlu client_id (UUID) untuk idempotency.
3. Kompleksitas testing meningkat (offline → online scenario).
