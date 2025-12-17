<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShipmentStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = true;

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
            Stat::make(__('admin.widgets.total_shipments'), number_format($total))
                ->description(__('admin.widgets.all_packages'))
                ->descriptionIcon('heroicon-m-truck', IconPosition::Before)
                ->chart($this->sparkline('total'))
                ->color('primary'),
            Stat::make(__('admin.widgets.delivered'), number_format($delivered))
                ->description(__('admin.widgets.delivered_desc', ['rate' => $deliveredRate]))
                ->descriptionIcon('heroicon-m-check-badge', IconPosition::Before)
                ->chart($this->sparkline('delivered'))
                ->color('success'),
            Stat::make(__('admin.widgets.in_transit'), number_format($inTransit))
                ->description(__('admin.widgets.in_transit_desc', ['rate' => $inTransitRate]))
                ->descriptionIcon('heroicon-m-paper-airplane', IconPosition::Before)
                ->chart($this->sparkline('in_transit'))
                ->color('warning'),
            Stat::make(__('admin.widgets.pending'), number_format($pending))
                ->description(__('admin.widgets.pending_desc'))
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

