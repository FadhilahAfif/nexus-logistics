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

    protected static ?string $heading = 'Aktivitas Terbaru';

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
                ->label('Resi')
                ->weight('bold')
                ->copyable()
                ->copyMessage('Nomor resi disalin'),
            Tables\Columns\TextColumn::make('sender_name')
                ->label('Pengirim')
                ->wrap(),
            Tables\Columns\TextColumn::make('receiver_name')
                ->label('Penerima')
                ->wrap(),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
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

