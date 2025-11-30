<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;

class ShipmentStatusDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Status Paket';

    protected static ?int $sort = 4;

    protected static bool $isLazy = true;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $data = Shipment::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Pastikan data tidak kosong untuk mencegah error JS di chart
        if (empty($data)) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Paket',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#94a3b8', // pending - gray
                        '#3b82f6', // picked_up - blue
                        '#f59e0b', // in_transit - orange
                        '#10b981', // delivered - green
                        '#ef4444', // returned - red
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => array_map(fn ($status) => trans('statuses.' . $status), array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
