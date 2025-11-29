<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShipmentStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $total = Shipment::count();
        $delivered = Shipment::where('status', 'delivered')->count();
        $inTransit = Shipment::where('status', 'in_transit')->count();
        $pending = Shipment::where('status', 'pending')->count();

        $deliveredRate = $total > 0 ? round(($delivered / $total) * 100) : 0;
        $inTransitRate = $total > 0 ? round(($inTransit / $total) * 100) : 0;

        return [
            Stat::make('Total Pengiriman', number_format($total))
                ->description('Semua paket terdaftar')
                ->descriptionIcon('heroicon-m-truck', IconPosition::Before)
                ->chart($this->sparkline('total'))
                ->color('primary'),
            Stat::make('Terkirim', number_format($delivered))
                ->description("{$deliveredRate}% dari total")
                ->descriptionIcon('heroicon-m-check-badge', IconPosition::Before)
                ->chart($this->sparkline('delivered'))
                ->color('success'),
            Stat::make('Sedang Jalan', number_format($inTransit))
                ->description("{$inTransitRate}% masih di perjalanan")
                ->descriptionIcon('heroicon-m-paper-airplane', IconPosition::Before)
                ->chart($this->sparkline('in_transit'))
                ->color('warning'),
            Stat::make('Menunggu Kurir', number_format($pending))
                ->description('Belum dijemput')
                ->descriptionIcon('heroicon-m-clock', IconPosition::Before)
                ->chart($this->sparkline('pending'))
                ->color('gray'),
        ];
    }

    /**
     * Generate a simple faux sparkline to keep the widget lively without heavy queries.
     *
     * @return array<int, int>
     */
    private function sparkline(string $seed): array
    {
        $base = crc32($seed);

        return collect(range(1, 7))
            ->map(fn (int $index) => (($base >> $index) & 15) + ($index * 2))
            ->all();
    }
}

