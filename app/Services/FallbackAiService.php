<?php

namespace App\Services;

use App\Services\Contracts\AiProviderInterface;
use App\Models\ChatbotLog;
use App\Models\PengaturanDesa;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Facades\Ai;

/**
 * Layanan untuk mengelola beberapa provider AI dengan logika prioritas dan fallback.
 * Kini menggunakan Laravel AI SDK (laravel/ai) secara native tanpa duplikasi wrapper kelas.
 */
class FallbackAiService implements AiProviderInterface
{
    /**
     * Menjalankan callback melalui daftar provider AI berdasarkan prioritas
     * dengan mekanisme fallback otomatis jika provider gagal merespons.
     */
    protected function runWithFallback(callable $callback)
    {
        $providersListJson = PengaturanDesa::get('ai_providers_list');
        $providers = [];

        if ($providersListJson && is_array($providersListJson)) {
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

        foreach ($providers as $prov) {
            try {
                $this->applyProviderConfig($prov);
                $providerInstance = $this->resolveProviderInstance($prov['provider_type']);
                
                $response = $callback($providerInstance);

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

        if ($lastException) {
            Log::error("Semua AI Provider gagal. Last error: " . $lastException->getMessage());
        }

        return null;
    }

    /**
     * Menerapkan konfigurasi provider AI (api_key, model, base_url) ke runtime Laravel
     * secara dinamis, termasuk kompatibilitas mundur untuk OpenAI dan Gemini.
     */
    protected function applyProviderConfig(array $prov): void
    {
        $type = $prov['provider_type'];
        
        config([
            "ai.providers.{$type}.key" => $prov['api_key'] ?? '',
            "ai.providers.{$type}.url" => $prov['base_url'] ?? null,
            "ai.providers.{$type}.model" => $prov['model'] ?? null,
            'ai.default' => $type,
        ]);

        // Kompatibilitas mundur
        if ($type === 'openai') {
            config([
                'services.ai.openai.api_key' => $prov['api_key'] ?? '',
                'services.ai.openai.model'   => $prov['model'] ?? 'gpt-4o-mini',
                'services.ai.openai.base_url'=> $prov['base_url'] ?? 'https://api.openai.com/v1',
            ]);
        } elseif ($type === 'gemini') {
            config([
                'services.gemini.api_key'    => $prov['api_key'] ?? '',
                'services.gemini.model'      => $prov['model'] ?? 'gemini-flash-lite-latest',
                'services.gemini.base_url'   => $prov['base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta',
            ]);
        }
    }

    /**
     * Meresolusi dan menginstansiasi adapter provider AI berdasarkan tipe yang diberikan,
     * membungkusnya dalam anonymous class yang mendelegasikan perintah ke Laravel AI SDK.
     */
    protected function resolveProviderInstance(string $type): AiProviderInterface
    {
        return new class($type, $this) implements AiProviderInterface {
            public function __construct(private string $driver, private FallbackAiService $service) {}

            public function generateResponse(string $userMessage, string $chatId, ?string $context = null): ?string
            {
                try {
                    $cachedResponse = $this->service->findSemanticCachedResponse($userMessage);
                    if ($cachedResponse) {
                        ChatbotLog::create([
                            'telegram_chat_id' => $chatId,
                            'pesan_masuk'      => $userMessage,
                            'balasan_ai'       => $cachedResponse,
                            'tokens_used'      => 0,
                        ]);
                        return $cachedResponse;
                    }

                    $systemPrompt = $context
                        ? $this->service->getRAGSystemPrompt($context)
                        : $this->service->getSystemPrompt();

                    $fullPrompt = $systemPrompt . "\n\nUser: " . $userMessage;
                    $aiResponse = Ai::driver($this->driver)->chat($fullPrompt);

                    if ($aiResponse) {
                        ChatbotLog::create([
                            'telegram_chat_id' => $chatId,
                            'pesan_masuk'      => $userMessage,
                            'balasan_ai'       => $aiResponse,
                            'tokens_used'      => null,
                        ]);
                    }

                    return $aiResponse ?: null;
                } catch (\Exception $e) {
                    Log::error("AiProvider ({$this->driver}) generateResponse Error: " . $e->getMessage());
                    return null;
                }
            }

            public function fixCopywriting(string $text, ?string $title = null): ?string
            {
                try {
                    $trimmedText = trim(strip_tags($text));

                    if (empty($trimmedText)) {
                        $prompt = 'Buatlah satu artikel berita atau pengumuman desa yang lengkap, natural, mengalir dengan baik, informatif, dan sangat bagus berdasarkan judul berikut: "' . ($title ?? 'Informasi Desa') . '". Gunakan bahasa Indonesia yang baik, benar, formal namun tetap ramah dibaca oleh warga desa. Format artikel menggunakan tag HTML standar (seperti tag p, strong, em, ul, li, br, dll). Jangan berikan penjelasan atau pengantar tambahan apapun, balas HANYA dengan kode HTML artikel tersebut secara langsung.';
                    } else {
                        $prompt = "Perbaiki dan sempurnakan copywriting tulisan artikel berita desa berikut dari segi ejaan (EYD/PUEBI), tata bahasa, kesantunan, kejelasan, dan alur keterbacaan agar formal, menarik, dan rapi untuk dibaca warga desa. Pertahankan tag HTML (seperti p, strong, em, ul, li, br, dll) yang sudah ada di dalam teks asli. Jangan berikan penjelasan, komentar, atau pengantar tambahan apapun, balas HANYA dengan teks artikel yang sudah diperbaiki secara langsung.\n\nTeks Asli:\n" . $text;
                    }

                    return strip_tags(Ai::driver($this->driver)->chat($prompt)) ?: null;
                } catch (\Exception $e) {
                    Log::error("AiProvider ({$this->driver}) fixCopywriting Error: " . $e->getMessage());
                    return null;
                }
            }

            public function generateSeoMetadata(string $title, string $content): ?array
            {
                try {
                    $prompt = "Sebagai pakar SEO, buatkan meta description dan 5-8 kata kunci (keywords) yang relevan untuk artikel berikut.\nJudul: {$title}\nKonten: " . strip_tags($content) . "\n\nBalas HANYA dengan format JSON valid seperti ini tanpa ada teks tambahan atau markdown block:\n{\"meta_description\": \"...\", \"kata_kunci\": \"...\"}";

                    $response = Ai::driver($this->driver)->chat($prompt);
                    if (!$response) return null;

                    $jsonStr = str_replace(['```json', '```'], '', $response);
                    $data = json_decode(trim($jsonStr), true);

                    if (json_last_error() === JSON_ERROR_NONE && isset($data['meta_description'])) {
                        return $data;
                    }
                    return null;
                } catch (\Exception $e) {
                    Log::error("AiProvider ({$this->driver}) generateSeoMetadata Error: " . $e->getMessage());
                    return null;
                }
            }

            public function checkHealth(): bool
            {
                try {
                    $response = Ai::driver($this->driver)->chat('ping');
                    return !empty($response);
                } catch (\Exception $e) {
                    return false;
                }
            }
        };
    }

    public function generateResponse(string $userMessage, string $chatId, ?string $context = null): ?string
    {
        return $this->runWithFallback(function (AiProviderInterface $provider) use ($userMessage, $chatId, $context) {
            return $provider->generateResponse($userMessage, $chatId, $context);
        });
    }

    public function fixCopywriting(string $text, ?string $title = null): ?string
    {
        return $this->runWithFallback(function (AiProviderInterface $provider) use ($text, $title) {
            return $provider->fixCopywriting($text, $title);
        });
    }

    public function generateSeoMetadata(string $title, string $content): ?array
    {
        return $this->runWithFallback(function (AiProviderInterface $provider) use ($title, $content) {
            return $provider->generateSeoMetadata($title, $content);
        });
    }

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

    // --- Helper Methods ---

    public function getRAGSystemPrompt(string $context): string
    {
        return "Anda adalah asisten virtual resmi Desa.\nJawablah pertanyaan warga secara sopan dan formal HANYA berdasarkan dokumen referensi berikut:\n\n---\nDOKUMEN REFERENSI:\n{$context}\n---\n\nJika informasi tidak ada dalam dokumen referensi di atas, jawab dengan sopan bahwa Anda tidak memiliki informasi detail mengenai hal tersebut dan sarankan untuk menghubungi kantor desa.";
    }

    public function getSystemPrompt(): string
    {
        return "Anda adalah asisten virtual resmi sistem AvaraDesa.\n\nTUGAS ANDA:\n1. Menjawab pertanyaan warga tentang prosedur administrasi desa\n2. Memberikan informasi tentang persyaratan surat-menyurat\n3. Menjelaskan cara menggunakan sistem AvaraDesa\n4. Memberikan informasi umum tentang desa\n\nJENIS SURAT YANG TERSEDIA:\n- Surat Keterangan Domisili\n- Surat Keterangan Tidak Mampu (SKTM)\n- Surat Keterangan Usaha\n- Surat Pengantar KTP\n- Surat Keterangan Kelahiran\n\nPROSEDUR UMUM:\n1. Login ke PWA menggunakan NIK\n2. Pilih jenis surat yang dibutuhkan\n3. Isi formulir dan upload dokumen persyaratan\n4. Tunggu verifikasi dari perangkat desa\n5. Surat akan dikirim via Telegram jika disetujui\n\nMUTASI KEPENDUDUKAN:\n- Kelahiran: Upload akta kelahiran\n- Kematian: Upload surat kematian dari RS/Puskesmas\n- Kedatangan: Upload surat pindah dari desa asal\n- Kepindahan: Ajukan surat pindah\n\nATURAN KOMUNIKASI:\n- Gunakan bahasa Indonesia yang sopan dan formal\n- Jika tidak tahu jawaban, arahkan untuk menghubungi kantor desa\n- Jangan memberikan informasi pribadi warga\n- Fokus pada informasi prosedural dan administratif\n\nJawab pertanyaan berikut dengan jelas dan membantu:";
    }

    public function findSemanticCachedResponse(string $userMessage): ?string
    {
        $normalized = trim(strtolower($userMessage));
        $cacheKey = 'ai_exact_' . md5($normalized);
        
        $cachedValue = Cache::get($cacheKey);
        if ($cachedValue) {
            return $cachedValue;
        }

        $exactLog = ChatbotLog::whereRaw('LOWER(TRIM(pesan_masuk)) = ?', [$normalized])
            ->where('created_at', '>=', now()->subHours(24))
            ->whereNotNull('balasan_ai')
            ->orderBy('created_at', 'desc')
            ->first();
            
        if ($exactLog) {
            Cache::put($cacheKey, $exactLog->balasan_ai, 86400);
            return $exactLog->balasan_ai;
        }

        $recentLogs = Cache::remember('ai_recent_logs_semantic', 300, function () {
            return ChatbotLog::where('created_at', '>=', now()->subHours(48))
                ->whereNotNull('balasan_ai')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get(['pesan_masuk', 'balasan_ai'])
                ->unique('pesan_masuk')
                ->values();
        });

        if ($recentLogs->isEmpty()) return null;

        $bestScore = 0.0;
        $bestResponse = null;
        
        if (strlen($normalized) > 1000) return null;
        if (strlen($normalized) > 200) return null; // ponytail: skip fuzzy untuk input panjang

        $inputTokens = $this->tokenize($normalized);
        if (count($inputTokens) === 0) return null;

        foreach ($recentLogs as $log) {
            $logTokens = $this->tokenize(trim(strtolower($log->pesan_masuk)));
            if (count($logTokens) === 0) continue;

            $intersection = count(array_intersect($inputTokens, $logTokens));
            $union = count(array_unique(array_merge($inputTokens, $logTokens)));
            $score = $union > 0 ? ($intersection / $union) : 0;
            
            if (strlen($normalized) < 20) {
                $levDist = levenshtein($normalized, trim(strtolower($log->pesan_masuk)));
                $maxLen = max(strlen($normalized), strlen($log->pesan_masuk));
                $levScore = $maxLen > 0 ? (1 - ($levDist / $maxLen)) : 0;
                $score = max($score, $levScore);
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestResponse = $log->balasan_ai;
            }
        }

        if ($bestScore >= 0.80 && $bestResponse) {
            Cache::put($cacheKey, $bestResponse, 86400);
            return $bestResponse;
        }

        return null;
    }

    /**
     * Menghitung token dengan memecah teks menjadi kata-kata,
     * membersihkan karakter non-alfanumerik, dan menghapus stopword Bahasa Indonesia.
     */
    public function tokenize(string $text): array
    {
        $clean = preg_replace('/[^\w\s]/u', '', $text);
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $stopwords = ['yang', 'di', 'dan', 'itu', 'dengan', 'untuk', 'pada', 'ke', 'dari', 'ini', 'adalah', 'akan', 'atau', 'saya', 'anda', 'kami', 'kita'];
        
        return array_diff($words, $stopwords);
    }
}
