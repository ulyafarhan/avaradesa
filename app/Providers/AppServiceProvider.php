<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
/**
 * Service provider utama aplikasi untuk registrasi binding dan inisialisasi boot.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Mendaftarkan binding service container.
     *
     * Menginisialisasi AiProviderInterface sebagai singleton dengan FallbackAiService
     * yang mendukung multi-provider dengan logika prioritas dan fallback.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\Contracts\AiProviderInterface::class, \App\Services\FallbackAiService::class);
    }

    /**
     * Menjalakan inisialisasi boot aplikasi.
     *
     * Fungsi yang dilakukan:
     * 1. Mendaftarkan event listeners untuk membersihkan cache statistik saat data berubah
     * 2. Mengisi konfigurasi AI dari database pengaturan_desa (provider, API keys, model)
     * 3. Mengatur konfigurasi filesystem/storage dari database pengaturan_desa
     *
     * @return void
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        $clearStatistikCache = function () {
            \Illuminate\Support\Facades\Cache::forget('statistik_demografi');
            \Illuminate\Support\Facades\Cache::forget('statistik_layanan');
        };

        \App\Models\Penduduk::saved($clearStatistikCache);
        \App\Models\Penduduk::deleted($clearStatistikCache);

        \App\Models\Keluarga::saved($clearStatistikCache);
        \App\Models\Keluarga::deleted($clearStatistikCache);

        \App\Models\PengajuanSurat::saved($clearStatistikCache);
        \App\Models\PengajuanSurat::deleted($clearStatistikCache);

        \App\Models\MutasiPenduduk::saved($clearStatistikCache);
        \App\Models\MutasiPenduduk::deleted($clearStatistikCache);

        $tableName = \Illuminate\Support\Facades\Schema::hasTable('pengaturan_desa') ? 'pengaturan_desa' : null;

        if ($tableName) {
            $modelClass = \App\Models\PengaturanDesa::class;

            // Sinkronkan ke konfigurasi lama (kompatibilitas mundur)
            if ($aiActiveProvider = $modelClass::get('ai_active_provider')) {
                config(['services.ai.active_provider' => $aiActiveProvider]);
            }

            // Sinkronkan Gemini ke config laravel/ai
            if ($aiGeminiKey = $modelClass::get('ai_gemini_key')) {
                config([
                    'services.gemini.api_key' => $aiGeminiKey,
                    'ai.providers.gemini.key' => $aiGeminiKey,
                ]);
            }

            // Sinkronkan OpenAI ke config laravel/ai
            if ($aiOpenAiKey = $modelClass::get('ai_openai_key')) {
                config([
                    'services.ai.openai.api_key' => $aiOpenAiKey,
                    'ai.providers.openai.key'    => $aiOpenAiKey,
                ]);
            }
            if ($aiOpenAiModel = $modelClass::get('ai_openai_model')) {
                config(['services.ai.openai.model' => $aiOpenAiModel]);
            }
            if ($aiOpenAiBaseUrl = $modelClass::get('ai_openai_base_url')) {
                config([
                    'services.ai.openai.base_url' => $aiOpenAiBaseUrl,
                    'ai.providers.openai.url'     => $aiOpenAiBaseUrl,
                ]);
            }

            // Tentukan driver default laravel/ai berdasarkan provider aktif dari DB
            $activeProvider = $modelClass::get('ai_active_provider', config('services.ai.active_provider', 'gemini'));
            config(['ai.default' => $activeProvider]);

            // Storage
            if ($storageActiveDisk = $modelClass::get('storage_active_disk')) {
                config(['filesystems.default' => $storageActiveDisk]);
            }
            if ($s3Key = $modelClass::get('storage_s3_key')) {
                config(['filesystems.disks.s3.key' => $s3Key]);
            }
            if ($s3Secret = $modelClass::get('storage_s3_secret')) {
                config(['filesystems.disks.s3.secret' => $s3Secret]);
            }
            if ($s3Bucket = $modelClass::get('storage_s3_bucket')) {
                config(['filesystems.disks.s3.bucket' => $s3Bucket]);
            }
            if ($s3Region = $modelClass::get('storage_s3_region')) {
                config(['filesystems.disks.s3.region' => $s3Region]);
            }
            if ($s3Endpoint = $modelClass::get('storage_s3_endpoint')) {
                config(['filesystems.disks.s3.endpoint' => $s3Endpoint]);
            }
            if ($s3Url = $modelClass::get('storage_s3_url')) {
                config(['filesystems.disks.s3.url' => $s3Url]);
            }
            if ($s3UsePathStyle = $modelClass::get('storage_s3_use_path_style_endpoint')) {
                config(['filesystems.disks.s3.use_path_style_endpoint' => filter_var($s3UsePathStyle, FILTER_VALIDATE_BOOLEAN)]);
            }

            // Sync WhatsApp Settings
            if ($waGatewayUrl = $modelClass::get('wa_gateway_url')) {
                config(['services.whatsapp.gateway_url' => $waGatewayUrl]);
            }
            if ($waApiKey = $modelClass::get('wa_api_key')) {
                config(['services.whatsapp.api_key' => $waApiKey]);
            }
            if ($waSessionId = $modelClass::get('wa_session_id')) {
                config(['services.whatsapp.session_id' => $waSessionId]);
            }
            if ($waDefaultTarget = $modelClass::get('wa_default_target')) {
                config(['services.whatsapp.default_target' => $waDefaultTarget]);
            }
        }
    }
}
