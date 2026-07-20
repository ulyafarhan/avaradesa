# ADR-002: Authentication Strategy

**Status:** Accepted  
**Date:** 2026-07-14  

## Context
Warga desa perlu login tanpa email/password. Sebagian besar warga tidak punya email.

## Decision
Tiga mekanisme auth:
1. **Warga (Registrasi Awal)**: NIK (16 digit) + No. KK (16 digit) — untuk verifikasi identitas pertama kali.
2. **Warga (Login Selanjutnya)**: NIK (16 digit) + PIN (6 digit) — PIN dibuat saat registrasi awal.
3. **Admin**: username + password tradisional.
4. **Opsional**: Biometric (fingerprint/face unlock) — fallback setelah login PIN.

Menggunakan Laravel Sanctum dengan token-based auth.

## Rationale
1. NIK + KK adalah kombinasi unik yang hanya diketahui warga bersangkutan.
2. Sanctum lightweight, tidak perlu OAuth complexity untuk skala desa.
3. Token expiry 24 jam (1440 menit) — keseimbangan keamanan dan kenyamanan.
4. PIN 6 digit mengurangi input dari 32 digit (NIK+KK) menjadi 22 digit (NIK+PIN).
5. Biometric mempercepat login untuk warga yang device-nya mendukung.

## Consequences
1. Login warga perlu rate limiting ketat — throttle:login (5,1): max 5 percobaan per menit.
2. NIK adalah identifier sensitif — harus dilindungi di log dan response.
3. Vue 3 menyimpan token di capacitor-secure-storage-plugin.
4. Backend perlu endpoint baru: `POST /auth/register-pin` dan `POST /auth/login-pin`.
5. Perlu tabel `user_pins` terpisah untuk menyimpan hash PIN.
