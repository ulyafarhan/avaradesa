<?php

/**
 * KONFIGURASI SCRIBE — Generator Dokumentasi API
 *
 * File ini mengatur bagaimana Scribe menghasilkan dokumentasi API untuk
 * AvaraDesa. Scribe secara otomatis
 * mengekstrak informasi dari route, controller, dan model Laravel
 * untuk menghasilkan halaman dokumentasi yang interaktif, koleksi Postman,
 * dan spesifikasi OpenAPI.
 *
 * @see https://scribe.knuckles.wtf/laravel/reference/config
 */

use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Extracting\Strategies;

use function Knuckles\Scribe\Config\configureStrategy;
use function Knuckles\Scribe\Config\removeStrategies;

return [

    /*
    |--------------------------------------------------------------------------
    | JUDUL DOKUMENTASI
    |--------------------------------------------------------------------------
    |
    | Nilai dari tag <title> HTML pada halaman dokumentasi yang dihasilkan.
    | Akan muncul di tab/judul browser.
    |
    */
    'title' => 'AvaraDesa API Documentation',

    /*
    |--------------------------------------------------------------------------
    | DESKRIPSI API
    |--------------------------------------------------------------------------
    |
    | Deskripsi singkat tentang API Anda. Akan disertakan dalam halaman web
    | dokumentasi, koleksi Postman, dan spesifikasi OpenAPI.
    |
    */
    'description' => 'API Documentation untuk AvaraDesa — Sistem Informasi Desa Terpadu',

    /*
    |--------------------------------------------------------------------------
    | TEKS PENGANTAR
    |--------------------------------------------------------------------------
    |
    | Teks yang ditempatkan di bagian "Pendahuluan", tepat setelah `description`.
    | Mendukung format Markdown dan HTML. Di sini Anda bisa menjelaskan base URL,
    | cara autentikasi, format response, rate limiting, dan panduan awal lainnya
    | bagi pengguna API.
    |
    */
    'intro_text' => <<<'INTRO'
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
        INTRO,

    /*
    |--------------------------------------------------------------------------
    | BASE URL
    |--------------------------------------------------------------------------
    |
    | URL dasar yang ditampilkan di dokumentasi.
    | Untuk tipe `laravel`, Anda bisa menggunakan string dinamis seperti
    | {{ config("app.tenant_url") }} untuk mendapatkan base URL yang dinamis.
    |
    */
    'base_url' => config('app.url'),

    /*
    |--------------------------------------------------------------------------
    | RUTE YANG AKAN DISERTAKAN
    |--------------------------------------------------------------------------
    |
    | Menentukan route/rute API mana yang akan disertakan dalam dokumentasi.
    | Anda dapat mencocokkan berdasarkan prefiks path dan domain.
    | Gunakan * sebagai wildcard untuk mencocokkan karakter apa pun.
    |
    */
    'routes' => [
        [
            'match' => [
                // Hanya sertakan route yang path-nya cocok dengan pola ini (contoh: 'users/*')
                'prefixes' => ['api/*'],

                // Hanya sertakan route yang domain-nya cocok dengan pola ini (contoh: 'api.*')
                'domains' => ['*'],
            ],

            // Sertakan route ini meskipun tidak cocok dengan aturan di atas
            'include' => [
                // 'users.index', 'POST /new', '/auth/*'
            ],

            // Kecualikan route ini meskipun cocok dengan aturan di atas
            'exclude' => [
                // 'GET /health', 'admin.*'
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | JENIS OUTPUT DOKUMENTASI
    |--------------------------------------------------------------------------
    |
    | - "static"   → menghasilkan halaman HTML statis di folder /public/docs
    | - "laravel"  → menghasilkan dokumentasi sebagai Blade view, sehingga
    |                Anda bisa menambahkan routing dan autentikasi sendiri
    | - "external_static" / "external_laravel" → sama seperti di atas, tetapi
    |                spesifikasi OpenAPI diberikan sebagai URL ke template UI eksternal
    |
    */
    'type' => 'laravel',

    /*
    |--------------------------------------------------------------------------
    | TEMA TAMPILAN
    |--------------------------------------------------------------------------
    |
    | Tema yang digunakan untuk merender halaman dokumentasi.
    | Opsi yang didukung: 'default' (lihat dokumentasi Scribe untuk tema lain).
    |
    */
    'theme' => 'default',

    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI OUTPUT STATIS
    |--------------------------------------------------------------------------
    |
    | Digunakan ketika `type` diatur ke 'static'.
    | Menentukan folder output untuk HTML, asset, dan koleksi Postman.
    | Sumber Markdown asli tetap berada di resources/docs.
    |
    */
    'static' => [
        'output_path' => 'public/docs',
    ],

    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI LARAVEL (BLADE)
    |--------------------------------------------------------------------------
    |
    | Digunakan ketika `type` diatur ke 'laravel'.
    | Mengontrol pembuatan route otomatis, URL dokumentasi, direktori asset,
    | dan middleware yang dipasang ke endpoint dokumentasi.
    |
    */
    'laravel' => [
        // Buat route otomatis untuk melihat dokumentasi yang dihasilkan
        'add_routes' => true,

        // Pola URL untuk endpoint dokumentasi (jika `add_routes` true):
        // - `/docs`          → halaman HTML
        // - `/docs.postman`  → koleksi Postman
        // - `/docs.openapi`  → spesifikasi OpenAPI
        'docs_url' => '/docs',

        // Direktori di dalam folder `public` untuk menyimpan asset CSS dan JS.
        // Default: public/vendor/scribe. Jika diisi, asset akan disimpan di public/{direktori}
        'assets_directory' => null,

        // Middleware yang dipasang ke endpoint dokumentasi
        'middleware' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI EKSTERNAL
    |--------------------------------------------------------------------------
    |
    | Atribut HTML tambahan untuk template dokumentasi eksternal.
    |
    */
    'external' => [
        'html_attributes' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | FITUR "COBA LANGSUNG" (TRY IT OUT)
    |--------------------------------------------------------------------------
    |
    | Menambahkan tombol "Coba Langsung" pada setiap endpoint sehingga
    | pengguna dapat menguji endpoint langsung dari browser.
    | Pastikan header CORS sudah diaktifkan untuk endpoint Anda.
    |
    */
    'try_it_out' => [
        // Aktifkan tombol "Coba Langsung"
        'enabled' => true,

        // Base URL untuk penguji API. Biarkan null agar sama dengan base_url yang ditampilkan
        'base_url' => null,

        // [Laravel Sanctum] Ambil token CSRF sebelum setiap request dan
        // tambahkan sebagai header X-XSRF-TOKEN
        'use_csrf' => false,

        // URL untuk mengambil token CSRF (jika `use_csrf` true)
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI AUTENTIKASI
    |--------------------------------------------------------------------------
    |
    | Informasi tentang cara API Anda diautentikasi. Akan digunakan dalam
    | dokumentasi yang ditampilkan, contoh yang dihasilkan, dan pemanggilan response.
    |
    */
    'auth' => [
        // Aktifkan jika ADA endpoint yang menggunakan autentikasi
        'enabled' => true,

        // Aktifkan jika API Anda harus diautentikasi secara default.
        // Jika true, Anda bisa menggunakan @unauthenticated atau @authenticated
        // pada endpoint tertentu untuk mengubah statusnya dari default.
        'default' => false,

        // Di mana nilai auth dikirim dalam request?
        // Opsi: AuthIn::BEARER (header Authorization: Bearer), AuthIn::HEADER, AuthIn::QUERY, dll.
        'in' => AuthIn::BEARER->value,

        // Nama parameter auth (mis: token, key, apiKey) atau header (mis: Authorization, Api-Key)
        'name' => 'Authorization',

        // Nilai parameter yang digunakan oleh Scribe untuk mengautentikasi pemanggilan response.
        // Nilai ini TIDAK akan disertakan dalam dokumentasi yang dihasilkan.
        // Jika kosong, Scribe akan menggunakan nilai acak.
        'use_value' => env('SCRIBE_AUTH_KEY'),

        // Placeholder yang akan dilihat pengguna untuk parameter auth di contoh request.
        // Set ke null jika ingin Scribe menggunakan nilai acak sebagai placeholder.
        'placeholder' => '{YOUR_AUTH_TOKEN}',

        // Informasi tambahan terkait autentikasi untuk pengguna. Mendukung Markdown dan HTML.
        'extra_info' => 'Untuk mendapatkan token, login terlebih dahulu menggunakan endpoint <code>POST /api/v1/auth/login/warga</code> (untuk warga) atau <code>POST /api/v1/auth/login/admin</code> (untuk admin). Token yang didapat kemudian digunakan sebagai Bearer token di header Authorization.',
    ],

    /*
    |--------------------------------------------------------------------------
    | BAHASA CONTOH REQUEST
    |--------------------------------------------------------------------------
    |
    | Contoh request untuk setiap endpoint akan ditampilkan dalam bahasa-bahasa
    | berikut. Opsi yang didukung: bash, javascript, php, python.
    | Untuk menambahkan bahasa sendiri, lihat dokumentasi Scribe.
    | Catatan: tidak berfungsi untuk tipe dokumentasi `external`.
    |
    */
    'example_languages' => [
        'bash',
        'javascript',
    ],

    /*
    |--------------------------------------------------------------------------
    | KOLEKSI POSTMAN
    |--------------------------------------------------------------------------
    |
    | Menghasilkan koleksi Postman (v2.1.0) selain dokumentasi HTML.
    | Untuk tipe 'static': koleksi akan dihasilkan ke public/docs/collection.json.
    | Untuk tipe 'laravel': koleksi akan dihasilkan ke storage/app/scribe/collection.json.
    | Jika `laravel.add_routes` true, route untuk koleksi juga akan ditambahkan.
    |
    */
    'postman' => [
        'enabled' => true,

        'overrides' => [
            // 'info.version' => '2.0.0',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SPESIFIKASI OPENAPI (SWAGGER)
    |--------------------------------------------------------------------------
    |
    | Menghasilkan spesifikasi OpenAPI selain halaman dokumentasi.
    | Untuk tipe 'static': file akan dihasilkan ke public/docs/openapi.yaml.
    | Untuk tipe 'laravel': file akan dihasilkan ke storage/app/scribe/openapi.yaml.
    | Jika `laravel.add_routes` true, route untuk spesifikasi juga akan ditambahkan.
    |
    */
    'openapi' => [
        'enabled' => true,

        // Versi spesifikasi OpenAPI: '3.0.3' atau '3.1.0'
        // OpenAPI 3.1 lebih kompatibel dengan JSON Schema dan menjadi versi dominan.
        'version' => '3.0.3',

        'overrides' => [
            // 'info.version' => '2.0.0',
        ],

        // Generator tambahan untuk membuat spesifikasi OpenAPI.
        // Harus merupakan turunan dari `Knuckles\Scribe\Writing\OpenApiSpecGenerators\OpenApiGenerator`.
        'generators' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | GRUP ENDPOINT & URUTAN
    |--------------------------------------------------------------------------
    |
    | Mengatur grup default untuk endpoint yang tidak memiliki @group,
    | serta urutan tampilan grup dan endpoint dalam dokumentasi.
    |
    */
    'groups' => [
        // Grup default untuk endpoint yang tidak memiliki anotasi @group
        'default' => 'Endpoints',

        // Daftar grup, subgrup, dan endpoint dalam urutan yang diinginkan.
        // Secara default, Scribe mengurutkan grup secara alfabetis dan
        // endpoint sesuai urutan definisi route.
        // Catatan: tidak berfungsi untuk tipe dokumentasi `external`
        'order' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | LOGO KUSTOM
    |--------------------------------------------------------------------------
    |
    | Path logo kustom. Akan digunakan sebagai nilai atribut src pada tag <img>.
    | Pastikan path menunjuk ke URL atau path yang dapat diakses.
    | Set ke false untuk tidak menggunakan logo.
    |
    | Contoh jika logo ada di public/img:
    | - 'logo' => '../img/logo.png'  → untuk tipe 'static'
    | - 'logo' => 'img/logo.png'     → untuk tipe 'laravel'
    |
    */
    'logo' => false,

    /*
    |--------------------------------------------------------------------------
    | INFORMASI "TERAKHIR DIPERBARUI"
    |
    | Kustomisasi nilai "Terakhir diperbarui" yang ditampilkan di dokumentasi.
    |
    | Token yang tersedia:
    | - {date:<format>}  → format tanggal PHP (contoh: {date:F j Y} => March 28, 2022)
    | - {git:<format>}   → hash Git, format "short" atau "long"
    |
    | Catatan: tidak berfungsi untuk tipe dokumentasi `external`
    |
    */
    'last_updated' => 'Last updated: {date:F j, Y}',

    /*
    |--------------------------------------------------------------------------
    | GENERATOR CONTOH
    |--------------------------------------------------------------------------
    |
    | Mengontrol bagaimana Scribe menghasilkan nilai contoh untuk parameter
    | dan model response.
    |
    */
    'examples' => [
        // Seed untuk Faker — gunakan angka yang sama untuk menghasilkan
        // nilai contoh yang konsisten di setiap proses generating
        'faker_seed' => 1234,

        // Sumber model contoh untuk response API.
        // Scribe akan mencoba factory model terlebih dahulu, lalu mengambil
        // data pertama dari database jika factory gagal.
        // Urutan dapat diubah atau strategi dapat dihapus.
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    /*
    |--------------------------------------------------------------------------
    | STRATEGI EKSTRAKSI
    |--------------------------------------------------------------------------
    |
    | Strategi yang digunakan Scribe untuk mengekstrak informasi tentang route
    | pada setiap tahap:
    | - metadata      → informasi umum endpoint
    | - headers       → header request
    | - urlParameters → parameter di URL
    | - queryParameters → parameter query string
    | - bodyParameters → parameter body request
    | - responses     → response endpoint
    | - responseFields → field dalam response
    |
    | Gunakan configureStrategy() untuk mengatur pengaturan strategi tertentu.
    | Gunakan removeStrategies() untuk menghapus strategi.
    |
    */
    'strategies' => [
        'metadata' => [
            ...Defaults::METADATA_STRATEGIES,
        ],
        'headers' => [
            ...Defaults::HEADERS_STRATEGIES,
            Strategies\StaticData::withSettings(data: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]),
        ],
        'urlParameters' => [
            ...Defaults::URL_PARAMETERS_STRATEGIES,
        ],
        'queryParameters' => [
            ...Defaults::QUERY_PARAMETERS_STRATEGIES,
        ],
        'bodyParameters' => [
            ...Defaults::BODY_PARAMETERS_STRATEGIES,
        ],
        'responses' => configureStrategy(
            Defaults::RESPONSES_STRATEGIES,
            Strategies\Responses\ResponseCalls::withSettings(
                // Hanya panggil endpoint GET untuk menghasilkan response contoh
                only: ['GET *'],
                // Nonaktifkan mode debug untuk menghindari stack trace error di response
                config: [
                    'app.debug' => false,
                ]
            )
        ),
        'responseFields' => [
            ...Defaults::RESPONSE_FIELDS_STRATEGIES,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | TRANSaksi DATABASE UNTUK PANGGILAN RESPONSE
    |--------------------------------------------------------------------------
    |
    | Saat menghasilkan response contoh, Scribe akan mencoba memulai transaksi
    | database agar tidak ada perubahan yang benar-benar tersimpan ke database.
    | Tentukan koneksi database mana yang harus ditransaksikan di sini.
    | Jika hanya menggunakan satu koneksi db, biarkan apa adanya.
    |
    */
    'database_connections_to_transact' => [config('database.default')],

    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI FRACTAL (SERIALIZER)
    |--------------------------------------------------------------------------
    |
    | Jika Anda menggunakan serializer kustom dengan league/fractal,
    | Anda dapat menentukannya di sini.
    |
    */
    'fractal' => [
        'serializer' => null,
    ],
];
