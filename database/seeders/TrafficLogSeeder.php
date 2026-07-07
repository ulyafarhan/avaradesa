<?php

/**
 * SEEDER — Traffic Log (Statistik Pengunjung)
 *
 * Membuat data log kunjungan website yang realistis untuk keperluan
 * dasbor statistik. Data mencakup:
 * - Kunjungan harian selama 6 bulan terakhir
 * - Pola kunjungan berdasarkan hari (weekday vs weekend)
 * - Jam sibuk dan jam sepi
 * - Berbagai device (mobile, desktop, tablet)
 * - Beberapa bot/crawler
 */

namespace Database\Seeders;

use App\Models\TrafficLog;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TrafficLogSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // User Agents realistis
        $userAgents = [
            // Mobile (60% traffic)
            'Mozilla/5.0 (Linux; Android 13; SM-A536B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 12; Redmi Note 11) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 13; SM-M135F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (Linux; Android 12; RMX3161) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 15_7_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6.1 Mobile/15E148 Safari/604.1',

            // Desktop (30% traffic)
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',

            // Tablet (10% traffic)
            'Mozilla/5.0 (iPad; CPU OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 13; SM-X110) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];

        // Bot/Crawler
        $bots = [
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
            'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)',
            'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        ];

        // Path yang realistis untuk website desa
        $paths = [
            '/' => 30,                    // Homepage paling sering
            '/berita' => 15,             // Halaman berita
            '/layanan' => 10,            // Layanan administrasi
            '/profil' => 8,              // Profil desa
            '/login' => 12,              // Login warga
            '/admin/login' => 5,         // Login admin
            '/kontak' => 4,              // Kontak
            '/galeri' => 6,              // Galeri
            '/statistik' => 3,           // Statistik desa
            '/layanan/surat' => 7,       // Pengajuan surat
            '/warga/dashboard' => 8,     // Dashboard warga
        ];

        $pathList = array_keys($paths);
        $pathWeights = array_values($paths);

        // IP addresses realistis (range Indonesia)
        $ipPrefixes = [
            '103.28.', '103.29.', '103.30.', '103.31.',
            '114.124.', '114.125.', '114.126.',
            '182.2.', '182.3.', '182.4.',
            '36.66.', '36.67.', '36.68.',
            '111.94.', '111.95.', '111.96.',
            '125.163.', '125.164.', '125.165.',
        ];

        $logs = [];
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        // Generate log untuk setiap hari
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek;
            $isWeekend = in_array($dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY]);

            // Base traffic per hari (lebih ramai weekday untuk website pemerintah)
            if ($isWeekend) {
                $baseTraffic = $faker->numberBetween(15, 35);
            } else {
                $baseTraffic = $faker->numberBetween(30, 65);
            }

            // Tambah variasi traffic
            $dailyTraffic = (int) ($baseTraffic * $faker->numberBetween(80, 120) / 100);

            // Generate log entries untuk hari ini
            for ($j = 0; $j < $dailyTraffic; $j++) {
                // Jam kunjungan (lebih ramai siang dan malam)
                $hour = $this->getRealisticHour($faker);
                $minute = $faker->numberBetween(0, 59);
                $second = $faker->numberBetween(0, 59);

                $visitTime = $date->copy()->setTime($hour, $minute, $second);

                // Pilih user agent
                $isBot = $faker->numberBetween(1, 100) <= 5; // 5% bot
                $userAgent = $isBot
                    ? $faker->randomElement($bots)
                    : $faker->randomElement($userAgents);

                // Pilih path
                $path = $this->weightedRandom($pathList, $pathWeights);

                // Generate IP
                $ip = $faker->randomElement($ipPrefixes) . $faker->numberBetween(1, 254) . '.' . $faker->numberBetween(1, 254);

                // Method
                $method = $faker->randomElement(['GET', 'GET', 'GET', 'GET', 'POST']);

                // Referer
                $referer = null;
                if ($faker->boolean(30)) {
                    $referer = $faker->randomElement([
                        'https://www.google.com/',
                        'https://www.facebook.com/',
                        'https://www.instagram.com/',
                        'https://search.yahoo.com/',
                        null,
                    ]);
                }

                $logs[] = [
                    'id' => Str::ulid(),
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'path' => $path,
                    'method' => $method,
                    'referer' => $referer,
                    'is_bot' => $isBot,
                    'created_at' => $visitTime,
                ];
            }
        }

        // Batch insert untuk performa
        $chunks = array_chunk($logs, 500);
        foreach ($chunks as $chunk) {
            TrafficLog::insert($chunk);
        }
    }

    /**
     * Generate jam kunjungan yang realistis.
     *
     * Pola:
     * - Jam 0-5: Sepi (0-3% traffic)
     * - Jam 6-8: Mulai ramai (5-8%)
     * - Jam 9-11: Puncak pagi (12-15%)
     * - Jam 12-13: Siang (10-12%)
     * - Jam 14-17: Puncak sore (12-15%)
     * - Jam 18-20: Malam ramai (10-12%)
     * - Jam 21-23: Mulai sepi (5-8%)
     */
    private function getRealisticHour($faker): int
    {
        $weights = [
            0 => 1, 1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 2,
            6 => 5, 7 => 8, 8 => 10,
            9 => 12, 10 => 14, 11 => 13,
            12 => 10, 13 => 11,
            14 => 12, 15 => 13, 16 => 14, 17 => 12,
            18 => 10, 19 => 11, 20 => 10,
            21 => 7, 22 => 5, 23 => 3,
        ];

        $total = array_sum($weights);
        $random = $faker->numberBetween(1, $total);

        $cumulative = 0;
        foreach ($weights as $hour => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $hour;
            }
        }

        return 12; // fallback
    }

    /**
     * Weighted random selection.
     */
    private function weightedRandom(array $items, array $weights): string
    {
        $total = array_sum($weights);
        $random = mt_rand(1, $total);

        $cumulative = 0;
        foreach ($items as $index => $item) {
            $cumulative += $weights[$index];
            if ($random <= $cumulative) {
                return $item;
            }
        }

        return $items[0]; // fallback
    }
}
