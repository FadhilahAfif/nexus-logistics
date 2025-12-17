<?php

namespace App\Filament\Resources\Shipments;

use App\Filament\Resources\Shipments\Pages;
use App\Models\Shipment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    public static function getNavigationIcon(): ?string { return 'heroicon-o-truck'; }
    public static function getNavigationLabel(): string { return __('admin.nav.shipments'); }
    public static function getNavigationGroup(): ?string { return __('admin.nav.operational'); }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('admin.shipment.info_section'))
                            ->description(__('admin.shipment.info_description'))
                            ->schema([
                                TextInput::make('tracking_number')
                                    ->label(__('admin.shipment.tracking_number'))
                                    ->default(fn (?Shipment $record): string => $record?->tracking_number ?? 'NXS-' . strtoupper(Str::random(8)))
                                    ->unique(table: 'shipments', ignoreRecord: true)
                                    ->maxLength(32)
                                    ->readOnlyOn('edit')
                                    ->required(),
                                Select::make('status')
                                    ->label(__('admin.shipment.status'))
                                    ->options(Shipment::statusOptions())
                                    ->required()
                                    ->default('pending'),
                                DatePicker::make('estimated_delivery')
                                    ->label(__('admin.shipment.estimated_delivery'))
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->nullable(),
                                TextInput::make('weight_kg')
                                    ->label(__('admin.shipment.weight'))
                                    ->numeric()
                                    ->suffix('Kg')
                                    ->step(0.1)
                                    ->minValue(0.1)
                                    ->nullable(),
                                TextInput::make('price')
                                    ->label(__('admin.shipment.price'))
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->nullable(),
                            ])
                            ->columns(2),
                        Section::make(__('admin.shipment.sender_info'))
                            ->schema([
                                TextInput::make('sender_name')
                                    ->label(__('admin.shipment.sender_name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('sender_phone')
                                    ->label(__('admin.shipment.sender_phone'))
                                    ->tel()
                                    ->maxLength(30)
                                    ->required(),
                            ])
                            ->columns(2),
                        Section::make(__('admin.shipment.receiver_info'))
                            ->schema([
                                TextInput::make('receiver_name')
                                    ->label(__('admin.shipment.receiver_name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('receiver_phone')
                                    ->label(__('admin.shipment.receiver_phone'))
                                    ->tel()
                                    ->maxLength(30)
                                    ->required(),
                                Textarea::make('receiver_address')
                                    ->label('Alamat Lengkap')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
                Section::make(__('admin.shipment.updates.title'))
                    ->schema([
                        Repeater::make('updates')
                            ->relationship()
                            ->label(__('admin.shipment.updates.label'))
                            ->collapsible()
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => isset($state['status']) ? (Shipment::statusOptions()[$state['status']] ?? $state['status']) : null)
                            ->schema([
                                Select::make('status')
                                    ->label(__('admin.shipment.status'))
                                    ->options(Shipment::statusOptions())
                                    ->required()
                                    ->live(),
                                TextInput::make('location')
                                    ->label(__('admin.shipment.updates.location'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Jakarta, Indonesia'),
                                Textarea::make('description')
                                    ->label(__('admin.shipment.updates.description'))
                                    ->rows(2)
                                    ->columnSpanFull(),
                                FileUpload::make('proof_of_delivery')
                                    ->label(__('admin.shipment.updates.proof_of_delivery'))
                                    ->image()
                                    ->maxSize(2048)
                                    ->disk('s3')
                                    ->directory('pod')
                                    ->visibility('public')
                                    ->columnSpanFull()
                                    ->visible(fn ($get) => $get('status') === 'delivered')
                                    ->fetchFileInformation(false)
                                    ->helperText(__('admin.shipment.updates.pod_helper')),
                                DateTimePicker::make('happened_at')
                                    ->label(__('admin.shipment.updates.happened_at'))
                                    ->seconds(false)
                                    ->required()
                                    ->default(now()),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label(__('admin.table.tracking_number'))
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage(__('admin.table.copied'))
                    ->copyMessageDuration(1500)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_name')
                    ->label(__('admin.table.sender'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('receiver_name')
                    ->label(__('admin.table.receiver'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('admin.table.status'))
                    ->badge()
                    ->color(fn (Shipment $record): string => match ($record->status) {
                        'pending' => 'gray',
                        'picked_up' => 'blue',
                        'in_transit' => 'warning',
                        'delivered' => 'success',
                        'returned' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (Shipment $record): string => $record->status_label)
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('admin.table.total_price'))
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn (?string $state): string => filled($state)
                        ? 'Rp ' . number_format((float) $state, 0, ',', '.')
                        : '-'),
                Tables\Columns\TextColumn::make('weight_kg')
                    ->label(__('admin.table.weight'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (?string $state): string => filled($state)
                        ? rtrim(rtrim(number_format((float) $state, 2, ',', '.'), '0'), ',') . ' kg'
                        : '-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.table.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options(Shipment::statusOptions())
                    ->searchable()
                    ->multiple(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()->label('Detail'),
                EditAction::make(),
                \Filament\Actions\Action::make('printLabel')
                    ->label(__('admin.shipment.print_label'))
                    ->icon('heroicon-o-printer')
                    ->modalContent(fn (Shipment $record) => view('shipments.label-preview', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalWidth('5xl'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'tracking_number',
            'sender_name',
            'receiver_name',
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}