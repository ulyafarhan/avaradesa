<?php

namespace App\Services\AiProviders;

use App\Services\Contracts\AiProviderInterface;
use App\Models\ChatbotLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Facades\Ai;

/**
 * Provider AI untuk layanan OpenAI API.
 *
 * Mengimplementasikan AiProviderInterface menggunakan Laravel AI SDK (laravel/ai)
 * sebagai lapisan transport yang stabil menggantikan HTTP kustom.
 *
 * Fitur yang dipertahankan:
 * - Semantic caching (Exact Match + Jaccard Similarity)
 * - RAG (Retrieval-Augmented Generation) dengan knowledge base
 * - Logging percakapan ke tabel chatbot_logs
 */
class OpenAiProvider implements AiProviderInterface
{
    /**
     * Menghasilkan respons AI untuk pesan pengguna dengan dukungan cache semantik dan RAG.
     *
     * @param  string  $userMessage  Pesan dari pengguna
     * @param  string  $chatId  ID chat Telegram atau session ID untuk logging
     * @param  string|null  $context  Konteks dokumen dari knowledge base untuk RAG (opsional)
     * @return string|null  Respons AI yang dihasilkan, atau null jika gagal
     */
    public function generateResponse(string $userMessage, string $chatId, ?string $context = null): ?string
    {
        try {
            // Cek semantic cache terlebih dahulu sebelum memanggil API
            $cachedResponse = $this->findSemanticCachedResponse($userMessage);
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
                ? $this->getRAGSystemPrompt($context)
                : $this->getSystemPrompt();

            // laravel/ai menggunakan driver 'openai' yang dikonfigurasi via config/ai.php
            $aiResponse = Ai::driver('openai')->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ]);

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
            Log::error('OpenAiProvider (laravel/ai) generateResponse Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Memperbaiki dan menyempurnakan copywriting artikel berita desa.
     *
     * Jika teks kosong, akan membuat artikel baru berdasarkan judul.
     * Jika teks tersedia, akan memperbaiki ejaan, tata bahasa, dan alur keterbacaan.
     *
     * @param  string  $text  Teks artikel yang akan diperbaiki (bisa kosong untuk buat baru)
     * @param  string|null  $title  Judul artikel untuk konteks (wajib jika $text kosong)
     * @return string|null  Teks artikel yang sudah diperbaiki, atau null jika gagal
     */
    public function fixCopywriting(string $text, ?string $title = null): ?string
    {
        try {
            $trimmedText = trim(strip_tags($text));

            if (empty($trimmedText)) {
                $prompt = 'Buatlah satu artikel berita atau pengumuman desa yang lengkap, natural, mengalir dengan baik, informatif, dan sangat bagus berdasarkan judul berikut: "' . ($title ?? 'Informasi Desa') . '". Gunakan bahasa Indonesia yang baik, benar, formal namun tetap ramah dibaca oleh warga desa. Format artikel menggunakan tag HTML standar (seperti tag p, strong, em, ul, li, br, dll). Jangan berikan penjelasan atau pengantar tambahan apapun, balas HANYA dengan kode HTML artikel tersebut secara langsung.';
            } else {
                $prompt = "Perbaiki dan sempurnakan copywriting tulisan artikel berita desa berikut dari segi ejaan (EYD/PUEBI), tata bahasa, kesantunan, kejelasan, dan alur keterbacaan agar formal, menarik, dan rapi untuk dibaca warga desa. Pertahankan tag HTML (seperti p, strong, em, ul, li, br, dll) yang sudah ada di dalam teks asli. Jangan berikan penjelasan, komentar, atau pengantar tambahan apapun, balas HANYA dengan teks artikel yang sudah diperbaiki secara langsung.\n\nTeks Asli:\n" . $text;
            }

            return Ai::driver('openai')->chat($prompt) ?: null;

        } catch (\Exception $e) {
            Log::error('OpenAiProvider (laravel/ai) fixCopywriting Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Menghasilkan meta deskripsi dan kata kunci SEO untuk artikel berita.
     *
     * Meta deskripsi dibatasi maksimal 160 karakter untuk optimasi SEO.
     * Response dikembalikan dalam format JSON yang sudah di-parse.
     *
     * @param  string  $title  Judul artikel berita
     * @param  string  $content  Konten artikel berita (HTML akan di-strip)
     * @return array|null  Assoc array dengan 'meta_description' dan 'kata_kunci', atau null jika gagal
     */
    public function generateSeoMetadata(string $title, string $content): ?array
    {
        try {
            $prompt = 'Tolong buatkan meta deskripsi (sangat penting: HARUS kurang dari 150 karakter terhitung spasi, jangan sampai melebihi 150 karakter, padat, profesional, ramah SEO, merangkum isi berita, tanpa emoji) dan kata kunci SEO (5-7 kata kunci dipisahkan dengan koma) berdasarkan judul dan konten berita desa berikut. Balas HANYA dengan format JSON valid seperti ini tanpa markdown/formatting tambahan: {"meta_description": "...", "kata_kunci": "..."}\n\nJudul: ' . $title . "\nKonten: " . strip_tags($content);

            $raw = Ai::driver('openai')->chat($prompt);

            if (!$raw) {
                return null;
            }

            $cleaned = trim($raw);
            if (str_starts_with($cleaned, '```json')) {
                $cleaned = substr($cleaned, 7);
            }
            if (str_ends_with($cleaned, '```')) {
                $cleaned = substr($cleaned, 0, -3);
            }
            $cleaned = trim($cleaned);

            $decoded = json_decode($cleaned, true);
            if (is_array($decoded) && isset($decoded['meta_description'], $decoded['kata_kunci'])) {
                $metaDesc = trim($decoded['meta_description']);
                if (mb_strlen($metaDesc) > 160) {
                    $metaDesc = mb_substr($metaDesc, 0, 157) . '...';
                }
                $decoded['meta_description'] = $metaDesc;
                return $decoded;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('OpenAiProvider (laravel/ai) generateSeoMetadata Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Memeriksa ketersediaan layanan API OpenAI.
     *
     * @return bool  true jika driver OpenAI tersedia dan merespons
     */
    public function checkHealth(): bool
    {
        try {
            $response = Ai::driver('openai')->chat('ping');
            return !empty($response);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Menyusun system prompt dengan konteks RAG dari dokumen referensi.
     *
     * @param  string  $context  Konteks dokumen dari knowledge base
     * @return string  System prompt lengkap dengan instruksi dan dokumen referensi
     */
    protected function getRAGSystemPrompt(string $context): string
    {
        $namaDesa = \App\Models\PengaturanDesa::get('nama_desa', 'Udeung');
        $kecamatan = \App\Models\PengaturanDesa::get('kecamatan', 'Bandar Baru');
        $kabupaten = \App\Models\PengaturanDesa::get('kabupaten', 'Pidie Jaya');
        $provinsi  = \App\Models\PengaturanDesa::get('provinsi', 'Aceh');

        return "Anda adalah asisten virtual resmi Desa {$namaDesa}, Kecamatan {$kecamatan}, {$kabupaten}, {$provinsi}.\nJawablah pertanyaan warga secara sopan dan formal HANYA berdasarkan dokumen referensi berikut:\n\n---\nDOKUMEN REFERENSI:\n{$context}\n---\n\nJika informasi tidak ada dalam dokumen referensi di atas, jawab dengan sopan bahwa Anda tidak memiliki informasi detail mengenai hal tersebut dan sarankan untuk menghubungi kantor desa {$namaDesa}.";
    }

    /**
     * Menyusun system prompt default untuk percakapan umum dengan warga.
     *
     * @return string  System prompt default untuk chatbot
     */
    protected function getSystemPrompt(): string
    {
        $namaDesa = \App\Models\PengaturanDesa::get('nama_desa', 'Udeung');
        $kecamatan = \App\Models\PengaturanDesa::get('kecamatan', 'Bandar Baru');
        $kabupaten = \App\Models\PengaturanDesa::get('kabupaten', 'Pidie Jaya');
        $provinsi  = \App\Models\PengaturanDesa::get('provinsi', 'Aceh');

        return "Anda adalah asisten virtual resmi Desa {$namaDesa}, Kecamatan {$kecamatan}, Kabupaten {$kabupaten}, Provinsi {$provinsi}.\n\nTUGAS ANDA:\n1. Menjawab pertanyaan warga tentang prosedur administrasi desa\n2. Memberikan informasi tentang persyaratan surat-menyurat\n3. Menjelaskan cara menggunakan sistem AvaraDesa\n4. Memberikan informasi umum tentang Desa {$namaDesa}\n\nJENIS SURAT YANG TERSEDIA:\n- Surat Keterangan Domisili\n- Surat Keterangan Tidak Mampu (SKTM)\n- Surat Keterangan Usaha\n- Surat Pengantar KTP\n- Surat Keterangan Kelahiran\n\nPROSEDUR UMUM:\n1. Login ke PWA menggunakan NIK\n2. Pilih jenis surat yang dibutuhkan\n3. Isi formulir dan upload dokumen persyaratan\n4. Tunggu verifikasi dari perangkat desa\n5. Surat akan dikirim via Telegram jika disetujui\n\nMUTASI KEPENDUDUKAN:\n- Kelahiran: Upload akta kelahiran\n- Kematian: Upload surat kematian dari RS/Puskesmas\n- Kedatangan: Upload surat pindah dari desa asal\n- Kepindahan: Ajukan surat pindah\n\nATURAN KOMUNIKASI:\n- Gunakan bahasa Indonesia yang sopan dan formal\n- Jika tidak tahu jawaban, arahkan untuk menghubungi kantor desa\n- Jangan memberikan informasi pribadi warga\n- Fokus pada informasi prosedural dan administratif\n\nJawab pertanyaan berikut dengan jelas dan membantu:";
    }

    /**
     * Menemukan respons cache berdasarkan kecocokan semantik dengan pertanyaan sebelumnya.
     *
     * @param  string  $userMessage  Pesan pengguna yang akan dicocokkan
     * @return string|null  Respons dari cache jika ditemukan kecocokan (score >= 0.80), atau null
     */
    protected function findSemanticCachedResponse(string $userMessage): ?string
    {
        $normalized = trim(strtolower($userMessage));

        $cacheKey    = 'ai_exact_' . md5($normalized);
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

        if ($recentLogs->isEmpty()) {
            return null;
        }

        $bestScore    = 0.0;
        $bestResponse = null;
        $inputTokens  = $this->tokenize($normalized);

        if (count($inputTokens) === 0) {
            return null;
        }

        foreach ($recentLogs as $log) {
            $logTokens = $this->tokenize(trim(strtolower($log->pesan_masuk)));
            if (count($logTokens) === 0) {
                continue;
            }

            $intersection = count(array_intersect($inputTokens, $logTokens));
            $union        = count(array_unique(array_merge($inputTokens, $logTokens)));
            $score        = $union > 0 ? ($intersection / $union) : 0;

            if (strlen($normalized) < 20) {
                $levDist  = levenshtein($normalized, trim(strtolower($log->pesan_masuk)));
                $maxLen   = max(strlen($normalized), strlen($log->pesan_masuk));
                $levScore = $maxLen > 0 ? (1 - ($levDist / $maxLen)) : 0;
                $score    = max($score, $levScore);
            }

            if ($score > $bestScore) {
                $bestScore    = $score;
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
     * Melakukan tokenisasi dan filtering stopwords untuk pencocokan semantik.
     *
     * @param  string  $text  Teks yang akan ditokenisasi
     * @return array  Array kata-kata setelah filtering stopwords
     */
    protected function tokenize(string $text): array
    {
        $clean = preg_replace('/[^\w\s]/u', '', $text);
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

        $stopwords = ['yang', 'di', 'dan', 'itu', 'dengan', 'untuk', 'pada', 'ke', 'dari', 'ini', 'adalah', 'akan', 'atau', 'saya', 'anda', 'kami', 'kita'];
        return array_diff($words, $stopwords);
    }
}
