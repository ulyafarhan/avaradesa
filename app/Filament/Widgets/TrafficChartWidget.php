<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TrafficLog;
use Carbon\Carbon;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Isolate;

#[Lazy]
#[Isolate]
class TrafficChartWidget extends ChartWidget
{
    protected ?string $heading = 'Statistik Pengunjung (7 Hari Terakhir)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '30s';

    /**
     * Mengambil data traffic riil untuk divisualisasikan dalam chart.
     */
    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->translatedFormat('d M');

            // Hitung kunjungan riil hari ini (exclude bots agar lebih valid)
            $count = TrafficLog::whereDate('created_at', $date)
                ->where('is_bot', false)
                ->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Kunjungan Harian (Non-Bot)',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.05)',
                    'fill' => 'start',
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
