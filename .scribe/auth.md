# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

## Mendapatkan Token

### Login Warga

| Metode | Endpoint | Keterangan |
|--------|----------|------------|
| NIK + No. KK | `POST /api/v1/auth/login/warga` | Login utama warga |
| NIK + PIN | `POST /api/v1/auth/login-pin` | Alternatif login warga |
| Biometric | `POST /api/v1/auth/login-biometric` | Login sidik jari/face ID (jika diaktifkan) |

### Login Admin

| Metode | Endpoint | Keterangan |
|--------|----------|------------|
| Username + Password | `POST /api/v1/auth/login/admin` | Login untuk operator/admin desa |

## Scope Token

- **Token Warga** — scope terbatas: hanya dapat mengakses data kependudukan milik sendiri (NIK, KK, pengajuan surat).
- **Token Admin** — scope lebih luas: dapat mengelola data kependudukan, membuat surat, dan mengatur master data desa.

## Contoh Penggunaan

Setelah mendapatkan token, sertakan di header setiap request:

```bash
curl -X GET https://your-domain.com/api/v1/warga/profil \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Accept: application/json"
```
