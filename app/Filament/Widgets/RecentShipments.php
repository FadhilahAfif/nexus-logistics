<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentShipments extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

    protected static bool $isLazy = true;

    protected function getHeading(): ?string
    {
        return __('admin.widgets.recent_activity');
    }

    protected ?string $pollingInterval = '30s';

    protected function getTableQuery(): Builder
    {
        return Shipment::query()
            ->latest()
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('tracking_number')
                ->label(__('admin.table.tracking_number'))
                ->weight('bold')
                ->copyable()
                ->copyMessage(__('admin.table.copied')),
            Tables\Columns\TextColumn::make('sender_name')
                ->label(__('admin.table.sender'))
                ->wrap(),
            Tables\Columns\TextColumn::make('receiver_name')
                ->label(__('admin.table.receiver'))
                ->wrap(),
            Tables\Columns\TextColumn::make('status')
                ->label(__('admin.table.status'))
                ->formatStateUsing(fn (Shipment $record): string => $record->status_label)
                ->badge()
                ->color(fn (Shipment $record): string => match ($record->status) {
                    'pending' => 'gray',
                    'picked_up' => 'info',
                    'in_transit' => 'warning',
                    'delivered' => 'success',
                    'returned' => 'danger',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Diupdate')
                ->since()
                ->tooltip(fn (Shipment $record) => $record->updated_at?->format('d M Y H:i')),
        ];
    }
}

