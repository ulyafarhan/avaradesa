<?php

namespace App\Services;

use App\Services\Contracts\AiProviderInterface;
use App\Services\AiProviders\GeminiProvider;
use App\Services\AiProviders\OpenAiProvider;
use App\Models\PengaturanDesa;
use Illuminate\Support\Facades\Log;

/**
 * Layanan untuk mengelola beberapa provider AI dengan logika prioritas dan fallback.
 */
class FallbackAiService implements AiProviderInterface
{
    /**
     * Mengeksekusi aksi AI menggunakan daftar provider yang diurutkan berdasarkan prioritas.
     *
     * Metode ini akan mencoba setiap provider secara berurutan berdasarkan prioritas.
     * Jika provider pertama gagal, akan fallback ke provider berikutnya.
     * Konfigurasi diambil dari database pengaturan_desa dengan fallback ke config Laravel.
     *
     * @param  callable  $callback  Callback yang menerima AiProviderInterface dan mengembalikan respons
     * @return mixed  Respons dari provider yang berhasil, atau null jika semua provider gagal
     */
    protected function runWithFallback(callable $callback)
    {
        // 1. Ambil daftar provider dari database (pengaturan_desa -> ai_providers_list)
        $providersListJson = PengaturanDesa::get('ai_providers_list');
        $providers = [];

        if ($providersListJson && is_array($providersListJson)) {
            // Urutkan berdasarkan prioritas (angka lebih kecil = prioritas lebih tinggi)
            usort($providersListJson, function ($a, $b) {
                $pA = isset($a['priority']) ? (int) $a['priority'] : 1;
                $pB = isset($b['priority']) ? (int) $b['priority'] : 1;
                return $pA <=> $pB;
            });

            foreach ($providersListJson as $item) {
                if (isset($item['is_active']) && !$item['is_active']) {
                    continue;
                }
                $providers[] = $item;
            }
        }

        // 2. Jika daftar di database kosong, gunakan konfigurasi lama (kompatibilitas mundur)
        if (empty($providers)) {
            $activeProvider = config('services.ai.active_provider', 'gemini');
            if ($activeProvider === 'openai') {
                $providers[] = [
                    'provider_type' => 'openai',
                    'api_key' => config('services.ai.openai.api_key'),
                    'model' => config('services.ai.openai.model', 'gpt-4o-mini'),
                    'base_url' => config('services.ai.openai.base_url', 'https://api.openai.com/v1'),
                ];
            } else {
                $providers[] = [
                    'provider_type' => 'gemini',
                    'api_key' => config('services.gemini.api_key'),
                    'model' => config('services.gemini.model', 'gemini-pro'),
                ];
            }
        }

        $lastException = null;

        // 3. Iterasi setiap provider dan coba eksekusi
        foreach ($providers as $prov) {
            try {
                // Terapkan konfigurasi ke konfigurasi runtime
                $this->applyProviderConfig($prov);

                // Instansiasi provider yang sesuai
                $providerInstance = $this->resolveProviderInstance($prov['provider_type']);

                // Eksekusi callback
                $response = $callback($providerInstance);

                // Jika respons null, anggap sebagai kegagalan untuk memicu fallback
                if ($response === null) {
                    throw new \Exception("Provider " . $prov['provider_type'] . " mengembalikan respons null.");
                }

                return $response;
            } catch (\Throwable $e) {
                $providerName = $prov['name'] ?? $prov['provider_type'];
                Log::warning("AI Provider Fallback: Provider '{$providerName}' gagal. Error: " . $e->getMessage());
                $lastException = $e;
                continue;
            }
        }

        // Jika semua provider gagal, log exception terakhir dan kembalikan null
        if ($lastException) {
            Log::error("Semua AI Provider gagal. Last error: " . $lastException->getMessage());
        }

        return null;
    }

    /**
     * Menerapkan pengaturan konfigurasi ke konfigurasi runtime Laravel.
     *
     * Mengupdate config services.ai.openai atau services.gemini sesuai tipe provider.
     *
     * @param  array  $prov  Array konfigurasi provider berisi provider_type, api_key, model, dll
     * @return void
     */
    protected function applyProviderConfig(array $prov): void
    {
        if ($prov['provider_type'] === 'openai') {
            config([
                // Kompatibilitas lama
                'services.ai.openai.api_key' => $prov['api_key'] ?? '',
                'services.ai.openai.model'   => $prov['model'] ?? 'gpt-4o-mini',
                'services.ai.openai.base_url'=> $prov['base_url'] ?? 'https://api.openai.com/v1',
                // laravel/ai SDK
                'ai.providers.openai.key'    => $prov['api_key'] ?? '',
                'ai.providers.openai.url'    => $prov['base_url'] ?? 'https://api.openai.com/v1',
                'ai.default'                 => 'openai',
            ]);
        } else {
            config([
                // Kompatibilitas lama
                'services.gemini.api_key'    => $prov['api_key'] ?? '',
                'services.gemini.model'      => $prov['model'] ?? 'gemini-flash-lite-latest',
                'services.gemini.base_url'   => $prov['base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta',
                // laravel/ai SDK
                'ai.providers.gemini.key'    => $prov['api_key'] ?? '',
                'ai.default'                 => 'gemini',
            ]);
        }
    }


    /**
     * Menyelesaikan instance provider konkret yang sebenarnya.
     *
     * @param  string  $type  Tipe provider ('openai' atau 'gemini')
     * @return AiProviderInterface  Instance provider yang sesuai
     * @throws \InvalidArgumentException  Jika tipe provider tidak dikenali
     */
    protected function resolveProviderInstance(string $type): AiProviderInterface
    {
        return match ($type) {
            'openai' => new OpenAiProvider(),
            'gemini' => new GeminiProvider(),
            default => throw new \InvalidArgumentException("Unknown AI provider type: {$type}"),
        };
    }

    /**
     * Menghasilkan respons untuk pesan pengguna.
     *
     * @param  string  $userMessage  Pesan dari pengguna
     * @param  string  $chatId  ID chat Telegram untuk logging
     * @param  string|null  $context  Konteks RAG dari knowledge base (opsional)
     * @return string|null  Respons AI, atau null jika semua provider gagal
     */
    public function generateResponse(string $userMessage, string $chatId, ?string $context = null): ?string
    {
        return $this->runWithFallback(function (AiProviderInterface $provider) use ($userMessage, $chatId, $context) {
            return $provider->generateResponse($userMessage, $chatId, $context);
        });
    }

    /**
     * Memperbaiki dan mengoptimalkan copywriting artikel.
     *
     * @param  string  $text  Teks artikel yang akan diperbaiki
     * @param  string|null  $title  Judul artikel (opsional)
     * @return string|null  Teks artikel yang sudah diperbaiki, atau null jika semua provider gagal
     */
    public function fixCopywriting(string $text, ?string $title = null): ?string
    {
        return $this->runWithFallback(function (AiProviderInterface $provider) use ($text, $title) {
            return $provider->fixCopywriting($text, $title);
        });
    }

    /**
     * Menghasilkan metadata SEO untuk artikel.
     *
     * @param  string  $title  Judul artikel
     * @param  string  $content  Konten artikel
     * @return array|null  Assoc array dengan 'meta_description' dan 'kata_kunci', atau null jika gagal
     */
    public function generateSeoMetadata(string $title, string $content): ?array
    {
        return $this->runWithFallback(function (AiProviderInterface $provider) use ($title, $content) {
            return $provider->generateSeoMetadata($title, $content);
        });
    }

    /**
     * Memeriksa kesehatan penyedia AI.
     *
     * @return bool  true jika minimal satu provider AI merespons dengan sehat
     */
    public function checkHealth(): bool
    {
        try {
            $result = $this->runWithFallback(function (AiProviderInterface $provider) {
                return $provider->checkHealth() ? true : null;
            });
            return (bool)$result;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
