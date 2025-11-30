<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ShipmentStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 1;

    protected ?string $heading = 'Trend Pengiriman 7 Hari';

    protected ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $labels = collect(range(6, 0))
            ->map(fn (int $dayDiff) => Carbon::today()->subDays($dayDiff))
            ->map(fn (Carbon $date) => $date->translatedFormat('d M'))
            ->all();

        $baseQuery = Shipment::query()
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM-DD') as date, COUNT(*) as total")
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->pluck('total', 'date');

        $dataset = collect(range(6, 0))
            ->map(fn (int $dayDiff) => Carbon::today()->subDays($dayDiff)->toDateString())
            ->map(fn (string $date) => $baseQuery[$date] ?? 0)
            ->all();

        $deliveredCounts = Shipment::query()
            ->selectRaw("TO_CHAR(updated_at, 'YYYY-MM-DD') as date, COUNT(*) as total")
            ->where('updated_at', '>=', Carbon::today()->subDays(6))
            ->where('status', 'delivered')
            ->groupBy('date')
            ->pluck('total', 'date');

        $deliveredDataset = collect(range(6, 0))
            ->map(fn (int $dayDiff) => Carbon::today()->subDays($dayDiff)->toDateString())
            ->map(fn (string $date) => $deliveredCounts[$date] ?? 0)
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Pengiriman Baru',
                    'data' => $dataset,
                    'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                    'borderColor' => 'rgba(79, 70, 229, 1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Selesai (Delivered)',
                    'data' => $deliveredDataset,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'fill' => true,
                    'tension' => 0.4,
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

