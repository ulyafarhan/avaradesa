# ADR-002: Authentication Strategy

**Status:** Accepted  
**Date:** 2026-07-14  

## Context
Warga desa perlu login tanpa email/password. Sebagian besar warga tidak punya email.

## Decision
Dua mekanisme auth:
1. **Warga**: NIK (16 digit) + No. KK (16 digit) — data yang pasti dimiliki setiap warga.
2. **Admin**: username + password tradisional.

Menggunakan Laravel Sanctum dengan token-based auth.

## Rationale
1. NIK + KK adalah kombinasi unik yang hanya diketahui warga bersangkutan.
2. Sanctum lightweight, tidak perlu OAuth complexity untuk skala desa.
3. Token expiry 1 tahun (default), bisa di-refresh.

## Consequences
1. Login warga perlu rate limiting ketat (3x/menit) untuk mencegah brute force.
2. NIK adalah identifier sensitif — harus dilindungi di log dan response.
3. Flutter menyimpan token di flutter_secure_storage.
