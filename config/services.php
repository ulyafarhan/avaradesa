<?php

/**
 * KONFIGURASI LAYANAN PIHAK KETIGA — AvaraDesa
 *
 * Berkas ini digunakan untuk mendefinisikan konfigurasi akses ke layanan
 * (Mailgun, Postmark, AWS, Slack) maupun layanan khusus AvaraDesa
 * (Telegram Bot, Gemini AI, API Kemendagri, dan OpenAI).
 *
 * Semua nilai kredensial diambil dari file .env melalui helper env().
 */

return [

    /*
    |--------------------------------------------------------------------------
    | LAYANAN PIHAK KETIGA UMUM
    |--------------------------------------------------------------------------
    |
    | Kredensial untuk layanan email, notifikasi, dan infrastruktur pihak ketiga
    | yang umum digunakan oleh paket Laravel.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | POSTMARK
    |--------------------------------------------------------------------------
    |
    | Layanan email transaksional. Token API digunakan untuk mengirim email
    | melalui API Postmark.
    |
    */
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AWS SES (SIMPLE EMAIL SERVICE)
    |--------------------------------------------------------------------------
    |
    | Layanan email dari AWS. Membutuhkan key, secret, dan region.
    | Region default: us-east-1.
    |
    */
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | RESEND
    |--------------------------------------------------------------------------
    |
    | Layanan email modern sebagai alternatif Postmark/SES.
    |
    */
    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SLACK (NOTIFIKASI)
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk mengirim notifikasi error dan alert ke channel Slack.
    | Membutuhkan OAuth token bot dan ID channel tujuan.
    |
    */
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | LAYANAN KHUSUS SIG-UDEUNG
    |--------------------------------------------------------------------------
    |
    | Berikut adalah konfigurasi untuk layanan-layanan yang terintegrasi
    | secara khusus dengan sistem AvaraDesa.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | TELEGRAM BOT
    |--------------------------------------------------------------------------
    |
    | Bot Telegram digunakan untuk mengirim notifikasi dan informasi
    | kepada warga melalui grup Telegram. Konfigurasi meliputi token bot,
    | URL webhook untuk menerima update, dan ID grup chat tujuan.
    |
    */
    'telegram' => [
        'bot_token'      => env('TELEGRAM_BOT_TOKEN'),
        'webhook_url'    => env('TELEGRAM_WEBHOOK_URL'),
        'group_chat_id'  => env('TELEGRAM_GROUP_CHAT_ID'),
        'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WHATSAPP GATEWAY
    |--------------------------------------------------------------------------
    |
    | Konfigurasi WhatsApp (multi-provider: wa-gateway atau fonnte).
    | Nilai aktual diambil dari database pengaturan_desa agar dapat dikonfigurasi
    | oleh admin tanpa mengubah file ini.
    |
    | Variabel env (sebagai fallback jika DB kosong):
    | - WHA_PROVIDER     : wa-gateway | fonnte
    | - WHA_GATEWAY_URL  : URL gateway (contoh: http://127.0.0.1:2785)
    | - WHA_API_KEY      : API key gateway (header X-API-Key)
    | - WHA_SESSION_ID   : Session ID WhatsApp (contoh: default)
    | - WHA_DEFAULT_TARGET : Nomor HP default untuk notifikasi berita (contoh: 62812xxxx)
    | - FONNTE_TOKEN     : Token Fonnte API (jika pakai provider fonnte)
    |
    */
    'whatsapp' => [
        'provider'       => env('WHA_PROVIDER', 'wa-gateway'),
        'gateway_url'    => env('WHA_GATEWAY_URL', 'http://localhost:2785'),
        'api_key'        => env('WHA_API_KEY'),
        'session_id'     => env('WHA_SESSION_ID', 'default'),
        'default_target' => env('WHA_DEFAULT_TARGET'),
        'fonnte_token'   => env('FONNTE_TOKEN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | GOOGLE GEMINI AI
    |--------------------------------------------------------------------------
    |
    | Layanan AI dari Google yang digunakan untuk fitur chatbot/Asisten
    | Virtual Desa. Model default: gemini-flash-lite-latest (ringan & cepat).
    |
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-flash-lite-latest'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API KEMENDAGRI
    |--------------------------------------------------------------------------
    |
    | Integrasi dengan API Kementerian Dalam Negeri untuk sinkronisasi data
    | kependudukan dan kewilayahan (provinsi, kabupaten/kota, kecamatan,
    | desa).
    |
    */
    'kemendagri' => [
        'api_url' => env('KEMENDAGRI_API_URL'),
        'api_key' => env('KEMENDAGRI_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | LAYANAN AI (MULTI-PROVIDER)
    |--------------------------------------------------------------------------
    |
    | Konfigurasi multi-provider AI. Secara default menggunakan Gemini,
    | tetapi dapat dialihkan ke OpenAI dengan mengubah variabel AI_PROVIDER.
    |
    | Provider aktif ditentukan oleh: AI_PROVIDER (default: 'gemini')
    |
    | OpenAI:
    | - api_key  : Kunci API OpenAI
    | - model    : Model yang digunakan (default: gpt-4o-mini)
    | - base_url : Endpoint API, dapat diubah untuk proxy/kustom (default: https://api.openai.com/v1)
    |
    */
    'ai' => [
        'active_provider' => env('AI_PROVIDER', 'gemini'),
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        ],
        'ollama' => [
            'api_key' => env('OLLAMA_API_KEY', ''),
            'base_url' => env('OLLAMA_BASE_URL', 'http://localhost:11434/v1'),
            'model' => env('OLLAMA_MODEL', 'llama3'),
        ],
        'bedrock' => [
            'access_key' => env('BEDROCK_ACCESS_KEY'),
            'secret_key' => env('BEDROCK_SECRET_KEY'),
            'region' => env('BEDROCK_REGION', 'us-east-1'),
            'model' => env('BEDROCK_MODEL', 'anthropic.claude-v2'),
        ],
        'deepseek' => [
            'api_key' => env('DEEPSEEK_API_KEY'),
            'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1'),
            'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
        ],
    ],

];
