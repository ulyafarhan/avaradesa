<?php

namespace App\Services;

use App\Models\BotKnowledge;
use Illuminate\Support\Facades\Cache;

/**
 * Service untuk pencarian basis pengetahuan bot Telegram.
 *
 * Mengelola FAQ statis dan konteks RAG dari database (BotKnowledge)
 * maupun konfigurasi lokal untuk menjawab pertanyaan warga.
 */
class TelegramKnowledgeService
{
    /**
     * Mengambil data pengetahuan aktif dari database dengan cache 24 jam.
     *
     * @return \Illuminate\Support\Collection  Koleksi model BotKnowledge yang aktif (is_aktif = true)
     */
    protected function getActiveKnowledges()
    {
        try {
            $cached = Cache::remember('telegram_bot_knowledges', 86400, function () {
                return BotKnowledge::where('is_aktif', true)->get()->toArray();
            });
            return collect($cached);
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Mencari jawaban statis (FAQ/greeting) berdasarkan input teks.
     *
     * Prioritas pencarian: database FAQ -> greeting config -> FAQ config.
     * Mengembalikan null jika tidak ada jawaban yang cocok.
     *
     * @param  string  $text  Teks pertanyaan dari pengguna (akan dinormalisasi ke lowercase)
     * @return string|null  Jawaban statis yang cocok, atau null jika tidak ditemukan
     */
    public function findStaticAnswer(string $text): ?string
    {
        $normalizedText = trim(strtolower($text));

        if (empty($normalizedText)) {
            return null;
        }

        $databaseFaq = $this->getActiveKnowledges()
            ->where('tipe', 'faq')
            ->first(function ($faq) use ($normalizedText) {
                $keywords = $faq['kata_kunci'] ?? [];
                return $this->matchesAnyKeyword($normalizedText, $keywords);
            });

        if ($databaseFaq) {
            return $databaseFaq['jawaban_atau_konten'];
        }

        $greetingsConfig = config('telegram_knowledge.greetings', []);
        $greetingKeywords = $greetingsConfig['keywords'] ?? [];
        
        if (in_array($normalizedText, $greetingKeywords) || (strlen($normalizedText) <= 6 && $this->matchesAnyKeyword($normalizedText, $greetingKeywords))) {
            return $greetingsConfig['response'] ?? null;
        }

        $faqs = config('telegram_knowledge.faqs', []);
        foreach ($faqs as $faq) {
            $keywords = $faq['keywords'] ?? [];
            if ($this->matchesAnyKeyword($normalizedText, $keywords)) {
                return $faq['response'] ?? null;
            }
        }

        return null;
    }

    /**
     * Mengambil konteks dokumen dari knowledge base (RAG).
     *
     * Mencari di database KB terlebih dahulu, lalu fallback ke konfigurasi lokal
     * berdasarkan kata kunci yang cocok dengan input pengguna.
     *
     * @param  string  $text  Teks pertanyaan pengguna untuk dicocokkan dengan knowledge base
     * @return string  Konteks dokumen yang relevan (gabungan beberapa blok knowledge base)
     */
    public function retrieveContext(string $text): string
    {
        $normalizedText = trim(strtolower($text));
        $relevantBlocks = [];

        $databaseKb = $this->getActiveKnowledges()
            ->where('tipe', 'kb')
            ->filter(function ($kb) use ($normalizedText) {
                $keywords = $kb['kata_kunci'] ?? [];
                return $this->matchesAnyKeyword($normalizedText, $keywords);
            })
            ->pluck('jawaban_atau_konten')
            ->toArray();

        if (!empty($databaseKb)) {
            $relevantBlocks = $databaseKb;
        }

        if (empty($relevantBlocks)) {
            $kb = config('telegram_knowledge.kb', []);
            $mappings = [
                'profil' => ['profil', 'sejarah', 'kepala desa', 'kepala desa', 'desa', 'desa', 'udeung', 'kecamatan', 'pidie jaya'],
                'pwa_sig' => ['pwa', 'aplikasi', 'sig-udeung', 'login', 'masuk', 'fitur', 'akun', 'nik', 'warga'],
                'syarat_umum' => ['syarat', 'berkas', 'dokumen', 'upload', 'unggah', 'persyaratan', 'valid'],
                'pengambilan_surat' => ['ambil', 'diterima', 'disetujui', 'unduh', 'download', 'pdf', 'cetak', 'qr code'],
                'layanan' => ['layanan', 'surat', 'buat', 'bikin', 'katalog', 'jenis'],
                'alasan_ditolak' => ['tolak', 'ditolak', 'gagal', 'salah', 'revisi', 'perbaiki'],
            ];

            foreach ($mappings as $key => $keywords) {
                if ($this->matchesAnyKeyword($normalizedText, $keywords)) {
                    if (isset($kb[$key])) {
                        $relevantBlocks[] = $kb[$key];
                    }
                }
            }

            if (empty($relevantBlocks)) {
                return ($kb['profil'] ?? '') . ' ' . ($kb['syarat_umum'] ?? '');
            }
        }

        return implode(' ', $relevantBlocks);
    }

    /**
     * Memeriksa apakah teks mengandung salah satu dari kata kunci.
     *
     * @param  string  $text  Teks yang akan diperiksa (sudah dinormalisasi ke lowercase)
     * @param  array  $keywords  Array kata kunci yang akan dicocokkan
     * @return bool  true jika minimal satu kata kunci ditemukan dalam teks
     */
    protected function matchesAnyKeyword(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, strtolower($keyword))) {
                return true;
            }
        }
        return false;
    }
}
