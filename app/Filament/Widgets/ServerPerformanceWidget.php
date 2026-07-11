<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Isolate;

#[Lazy]
#[Isolate]
class ServerPerformanceWidget extends Widget
{
    protected string $view = 'filament.widgets.server-performance';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '10s';

    /**
     * Mengambil data performa dan kapasitas server secara real-time.
     */
    protected function getViewData(): array
    {
        // 1. Kapasitas Penyimpanan (Disk Space)
        $diskPath = base_path();
        $diskTotal = @disk_total_space($diskPath) ?: 1;
        $diskFree = @disk_free_space($diskPath) ?: 0;
        $diskUsed = $diskTotal - $diskFree;
        $diskPercentage = round(($diskUsed / $diskTotal) * 100, 1);

        // 2. Kapasitas Memori (RAM)
        $ramTotal = 0;
        $ramUsed = 0;
        $ramFree = 0;
        $ramPercentage = 0;

        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            @exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value', $output);
            $freeMemKb = 0;
            $totalMemKb = 0;
            foreach ($output as $line) {
                if (strpos($line, 'FreePhysicalMemory') !== false) {
                    $parts = explode('=', $line);
                    $freeMemKb = isset($parts[1]) ? (int)trim($parts[1]) : 0;
                }
                if (strpos($line, 'TotalVisibleMemorySize') !== false) {
                    $parts = explode('=', $line);
                    $totalMemKb = isset($parts[1]) ? (int)trim($parts[1]) : 0;
                }
            }
            if ($totalMemKb > 0) {
                $ramTotal = $totalMemKb * 1024; // KB to Bytes
                $ramFree = $freeMemKb * 1024;
                $ramUsed = $ramTotal - $ramFree;
                $ramPercentage = round(($ramUsed / $ramTotal) * 100, 1);
            }
        } else {
            // Linux / Unix
            if (@is_readable('/proc/meminfo')) {
                $meminfo = file_get_contents('/proc/meminfo');
                preg_match('/MemTotal:\s+(\d+)/', $meminfo, $totalMatches);
                preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $availMatches);
                if (isset($totalMatches[1])) {
                    $ramTotal = (int)$totalMatches[1] * 1024; // KB to Bytes
                    $ramFree = isset($availMatches[1]) ? (int)$availMatches[1] * 1024 : 0;
                    $ramUsed = $ramTotal - $ramFree;
                    $ramPercentage = round(($ramUsed / $ramTotal) * 100, 1);
                }
            }
        }

        // Fallback jika pembacaan RAM gagal
        if ($ramTotal === 0) {
            $ramTotal = memory_get_usage(true);
            $ramUsed = memory_get_usage();
            $ramFree = max(0, $ramTotal - $ramUsed);
            $ramPercentage = round(($ramUsed / $ramTotal) * 100, 1);
        }

        // Info Sistem Tambahan
        $osInfo = PHP_OS . ' (' . PHP_OS_FAMILY . ')';
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        $serverIp = request()->server('SERVER_ADDR') ?: gethostbyname(gethostname());

        return [
            'disk' => [
                'total' => $this->formatBytes($diskTotal),
                'used' => $this->formatBytes($diskUsed),
                'free' => $this->formatBytes($diskFree),
                'percentage' => $diskPercentage,
            ],
            'ram' => [
                'total' => $this->formatBytes($ramTotal),
                'used' => $this->formatBytes($ramUsed),
                'free' => $this->formatBytes($ramFree),
                'percentage' => $ramPercentage,
            ],
            'system' => [
                'os' => $osInfo,
                'php' => $phpVersion,
                'laravel' => $laravelVersion,
                'ip' => $serverIp,
            ]
        ];
    }

    /**
     * Konversi ukuran Bytes ke format yang mudah dibaca (KB, MB, GB, TB).
     */
    private function formatBytes(float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
