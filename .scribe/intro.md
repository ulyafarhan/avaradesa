# Introduction

API Documentation untuk AvaraDesa — Sistem Informasi Desa Terpadu

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    Dokumentasi ini menyediakan semua informasi yang Anda butuhkan untuk bekerja dengan API AvaraDesa.

    ## Base URL
    ```
    https://your-domain.com/api/v1
    ```

    ## Authentication
    API ini menggunakan **Laravel Sanctum** untuk autentikasi. Sebagian besar endpoint memerlukan token autentikasi.

    ### Login Warga (via NIK)

    Login menggunakan NIK (16 digit) dan No. KK (16 digit):

    ```bash
    POST /auth/login/warga
    {
      "nik": "1234567890123456",
      "no_kk": "1234567890123456"
    }
    ```

    ### Login Warga (via PIN)

    Alternatif login menggunakan NIK dan PIN (6 digit):

    ```bash
    POST /auth/login-pin
    {
      "nik": "1234567890123456",
      "pin": "123456"
    }
    ```

    ### Login Admin

    Login menggunakan username dan password:

    ```bash
    POST /auth/login/admin
    {
      "username": "operator",
      "password": "password123"
    }
    ```

    ### Mendapatkan Token

    Setelah login berhasil, Anda akan menerima token beserta informasi user:

    **Response Login Warga:**
    ```json
    {
      "message": "Login berhasil",
      "token": "1|abc123def456...",
      "user": {
        "nik": "1234567890123456",
        "nama": "John Doe"
      },
      "has_pin": true,
      "has_biometric": false
    }
    ```

    **Response Login Admin:**
    ```json
    {
      "message": "Login berhasil",
      "token": "1|xyz789abc123...",
      "user": {
        "id": 1,
        "username": "operator",
        "name": "Operator Desa"
      }
    }
    ```

    Token warga memiliki scope terbatas (akses data kependudukan milik sendiri), sedangkan token admin memiliki scope lebih luas (manajemen data desa).

    Sertakan token di header setiap request:

    ```
    Authorization: Bearer {token}
    ```

    ## Response Format
    Semua response menggunakan format JSON dengan struktur standar:

    **Success Response:**
    ```json
    {
      "message": "Success message",
      "data": { ... }
    }
    ```

    **Error Response:**
    ```json
    {
      "message": "Error message",
      "errors": {
        "field": ["Error detail"]
      }
    }
    ```

    ## Rate Limiting
    API ini menerapkan rate limiting terpisah:
    - **Endpoint login**: maksimal 5 request per menit per IP
    - **Endpoint API umum**: maksimal 60 request per menit per IP

    <aside>Kode contoh untuk bekerja dengan API tersedia di area gelap di sebelah kanan (atau sebagai bagian dari konten di mobile).</aside>
