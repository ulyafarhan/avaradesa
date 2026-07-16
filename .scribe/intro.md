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
    API ini menggunakan Laravel Sanctum untuk autentikasi. Sebagian besar endpoint memerlukan token autentikasi.

    ### Untuk Warga
    Login menggunakan NIK (16 digit):
    ```bash
    POST /auth/login/warga
    {
      "nik": "1234567890123456"
    }
    ```

    ### Untuk Admin
    Login menggunakan username dan password:
    ```bash
    POST /auth/login/admin
    {
      "username": "operator",
      "password": "password123"
    }
    ```

    Setelah login berhasil, Anda akan menerima token yang harus disertakan di header setiap request:
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
    API ini menggunakan rate limiting standar Laravel (60 requests per menit per IP).

    <aside>Kode contoh untuk bekerja dengan API tersedia di area gelap di sebelah kanan (atau sebagai bagian dari konten di mobile).</aside>

