# ADR-001: Laravel + Filament + Vue/Capacitor/Electron Stack

**Status:** Accepted  
**Date:** 2026-07-17 (Updated dari Flutter)

## Context
AvaraDesa membutuhkan sistem informasi desa yang melayani tiga platform: web (warga), admin panel, dan mobile/desktop app.
Sebelumnya direncanakan menggunakan Flutter, namun diubah ke Capacitor+Electron agar bisa me-reuse ekosistem Vue 3 secara total.

## Decision
Memilih Laravel 13 (backend) + Filament 5 (admin panel) + Vue 3 Inertia (web) + Vue 3 Standalone via Capacitor/Electron (mobile/desktop).

## Rationale
1. **Laravel 13**: Ekosistem matang untuk aplikasi desa, Eloquent ORM, Sanctum auth, queue, caching.
2. **Filament 5**: Admin panel siap pakai di sisi web, mengurangi development time.
3. **Vue 3 + Inertia**: SPA modern tanpa kompleksitas API client-side untuk portal web.
4. **Vue 3 + Capacitor/Electron**: Memungkinkan single JS codebase untuk mobile (Android/iOS) dan desktop (Windows/Linux/Mac), menggunakan framework UI yang sama dengan web.
5. **YAGNI & DRY API**: Capacitor app menggunakan rute API `/api/v1/` yang persis sama dengan yang digunakan secara internal, menghindari duplikasi endpoint.

## Consequences
1. Backend jadi single source of truth — Capacitor app mengkonsumsi API yang sama.
2. Kapabilitas offline di-handle via IndexedDB/SQLite (Capacitor plugin).
3. Pengurangan learning curve karena tim hanya perlu menguasai Vue/TS, tidak perlu belajar Dart/Flutter.
