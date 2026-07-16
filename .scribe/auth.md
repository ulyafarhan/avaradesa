# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Untuk mendapatkan token, login terlebih dahulu menggunakan endpoint <code>POST /api/v1/auth/login/warga</code> (untuk warga) atau <code>POST /api/v1/auth/login/admin</code> (untuk admin). Token yang didapat kemudian digunakan sebagai Bearer token di header Authorization.
