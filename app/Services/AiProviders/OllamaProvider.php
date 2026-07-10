<?php

namespace App\Services\AiProviders;

use App\Services\Contracts\AiProviderInterface;
use App\Models\ChatbotLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaProvider extends BaseAiProvider implements AiProviderInterface
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.ai.ollama.api_key', '');
        $this->model = config('services.ai.ollama.model', 'llama3');
        $this->baseUrl = config('services.ai.ollama.base_url', 'http://localhost:11434/v1');
    }

    public function generateResponse(string $userMessage, string $chatId, ?string $context = null): ?string
    {
        try {
            $cachedResponse = $this->findSemanticCachedResponse($userMessage);
            if ($cachedResponse) {
                ChatbotLog::create([
                    'telegram_chat_id' => $chatId,
                    'pesan_masuk' => $userMessage,
                    'balasan_ai' => $cachedResponse,
                    'tokens_used' => 0,
                ]);
                return $cachedResponse;
            }

            if (empty($this->apiKey) && !str_starts_with($this->baseUrl, 'http://localhost')) {
                Log::warning('Ollama API Key is not configured and base_url is not local.');
                return null;
            }

            $systemPrompt = $context ? $this->getRAGSystemPrompt($context) : $this->getSystemPrompt();

            $payload = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => 0.5,
                'max_tokens' => 1024,
            ];

            $headers = ['Content-Type' => 'application/json'];
            if (!empty($this->apiKey)) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            $response = Http::withHeaders($headers)
                ->timeout(60)
                ->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $aiResponse = $data['choices'][0]['message']['content'] ?? null;
                $tokensUsed = $data['usage']['total_tokens'] ?? 0;

                ChatbotLog::create([
                    'telegram_chat_id' => $chatId,
                    'pesan_masuk' => $userMessage,
                    'balasan_ai' => $aiResponse,
                    'tokens_used' => $tokensUsed,
                ]);

                return $aiResponse;
            }

            Log::error('Ollama API Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Ollama Provider Error: ' . $e->getMessage());
            return null;
        }
    }

    public function fixCopywriting(string $text, ?string $title = null): ?string
    {
        try {
            $trimmedText = trim(strip_tags($text));
            if (empty($trimmedText)) {
                $prompt = "Buatlah satu artikel berita atau pengumuman desa yang lengkap, natural, mengalir dengan baik, informatif, dan sangat bagus berdasarkan judul berikut: \"" . ($title ?? 'Informasi Desa') . "\". Gunakan bahasa Indonesia yang baik, benar, formal namun tetap ramah dibaca oleh warga desa/desa. Format artikel menggunakan tag HTML standar (seperti tag p, strong, em, ul, li, br, dll). Jangan berikan penjelasan atau pengantar tambahan apapun, balas HANYA dengan kode HTML artikel tersebut secara langsung.";
            } else {
                $prompt = "Perbaiki dan sempurnakan copywriting tulisan artikel berita desa berikut dari segi ejaan (EYD/PUEBI), tata bahasa, kesantunan, kejelasan, dan alur keterbacaan agar formal, menarik, dan rapi untuk dibaca warga desa. Pertahankan tag HTML (seperti p, strong, em, ul, li, br, dll) yang sudah ada di dalam teks asli. Jangan berikan penjelasan, komentar, atau pengantar tambahan apapun, balas HANYA dengan teks artikel yang sudah diperbaiki secara langsung.\n\nTeks Asli:\n" . $text;
            }

            $payload = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 2048,
            ];

            $headers = ['Content-Type' => 'application/json'];
            if (!empty($this->apiKey)) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            $response = Http::withHeaders($headers)
                ->timeout(120)
                ->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::error('Ollama fixCopywriting API Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Ollama Provider fixCopywriting Error: ' . $e->getMessage());
            return null;
        }
    }

    public function generateSeoMetadata(string $title, string $content): ?array
    {
        try {
            $prompt = "Tolong buatkan meta deskripsi (sangat penting: HARUS kurang dari 150 karakter terhitung spasi, jangan sampai melebihi 150 karakter, padat, profesional, ramah SEO, merangkum isi berita, tanpa emoji) dan kata kunci SEO (5-7 kata kunci dipisahkan dengan koma) berdasarkan judul dan konten berita desa berikut. Balas HANYA dengan format JSON valid seperti ini tanpa markdown/formatting tambahan: {\"meta_description\": \"...\", \"kata_kunci\": \"...\"}\n\nJudul: {$title}\nKonten: " . strip_tags($content);

            $payload = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 1024,
            ];

            $headers = ['Content-Type' => 'application/json'];
            if (!empty($this->apiKey)) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            $response = Http::withHeaders($headers)
                ->timeout(60)
                ->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? '';

                $text = trim($text);
                if (str_starts_with($text, '```json')) {
                    $text = substr($text, 7);
                }
                if (str_ends_with($text, '```')) {
                    $text = substr($text, 0, -3);
                }
                $text = trim($text);

                $decoded = json_decode($text, true);
                if (is_array($decoded) && isset($decoded['meta_description']) && isset($decoded['kata_kunci'])) {
                    $metaDesc = trim($decoded['meta_description']);
                    if (mb_strlen($metaDesc) > 160) {
                        $metaDesc = mb_substr($metaDesc, 0, 157) . '...';
                    }
                    $decoded['meta_description'] = $metaDesc;
                    return $decoded;
                }
            }

            Log::error('Ollama generateSeoMetadata API Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Ollama Provider generateSeoMetadata Error: ' . $e->getMessage());
            return null;
        }
    }

    public function checkHealth(): bool
    {
        try {
            $healthUrl = preg_replace('#/v1$#', '', $this->baseUrl);
            $response = Http::timeout(5)->get($healthUrl . '/api/tags');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
