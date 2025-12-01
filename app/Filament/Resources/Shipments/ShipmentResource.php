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
use Filament\Forms\Components\ViewField;
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
use Illuminate\Support\Str;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    public static function getNavigationIcon(): ?string { return 'heroicon-o-truck'; }
    public static function getNavigationLabel(): string { return 'Kelola Paket'; }
    public static function getNavigationGroup(): ?string { return 'Operasional'; }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Informasi Resi')
                            ->description('Nomor resi, status pengiriman, berat, dan ongkir.')
                            ->schema([
                                TextInput::make('tracking_number')
                                    ->label('Nomor Resi')
                                    ->default(fn (?Shipment $record): string => $record?->tracking_number ?? 'NXS-' . strtoupper(Str::random(8)))
                                    ->unique(table: 'shipments', ignoreRecord: true)
                                    ->maxLength(32)
                                    ->readOnlyOn('edit')
                                    ->required(),
                                Select::make('status')
                                    ->label('Status Paket')
                                    ->options(Shipment::statusOptions())
                                    ->required()
                                    ->default('pending'),
                                DatePicker::make('estimated_delivery')
                                    ->label('Estimasi Tiba')
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->nullable(),
                                TextInput::make('weight_kg')
                                    ->label('Berat (kg)')
                                    ->numeric()
                                    ->suffix('Kg')
                                    ->step(0.1)
                                    ->minValue(0.1)
                                    ->nullable(),
                                TextInput::make('price')
                                    ->label('Ongkir')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->nullable(),
                            ])
                            ->columns(2),
                        Section::make('Data Pengirim')
                            ->schema([
                                TextInput::make('sender_name')
                                    ->label('Nama Pengirim')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('sender_phone')
                                    ->label('Telepon Pengirim')
                                    ->tel()
                                    ->maxLength(30)
                                    ->required(),
                            ])
                            ->columns(2),
                        Section::make('Data Penerima')
                            ->schema([
                                TextInput::make('receiver_name')
                                    ->label('Nama Penerima')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('receiver_phone')
                                    ->label('Telepon Penerima')
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
                Section::make('Riwayat Tracking Kurir')
                    ->schema([
                        Repeater::make('updates')
                            ->relationship()
                            ->label('Update Status')
                            ->collapsible()
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options(Shipment::statusOptions())
                                    ->required()
                                    ->live(),
                                TextInput::make('location')
                                    ->label('Lokasi')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->label('Catatan Kurir')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                FileUpload::make('proof_of_delivery')
                                    ->label('Bukti Foto (POD)')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->disk('public')
                                    ->directory('pod-images')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->columnSpanFull()
                                    ->visible(fn ($get) => $get('status') === 'delivered')
                                    ->saveUploadedFileUsing(function ($file, $state) {
                                        // Convert uploaded file to Base64
                                        $fileContent = file_get_contents($file->getRealPath());
                                        $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode($fileContent);
                                        return $base64;
                                    })
                                    ->getUploadedFileNameForStorageUsing(function () {
                                        return null; // Don't save to disk
                                    }),
                                DateTimePicker::make('happened_at')
                                    ->label('Waktu Terjadi')
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
                    ->label('Resi')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nomor resi tersalin')
                    ->copyMessageDuration(1500)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender_name')
                    ->label('Pengirim')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('receiver_name')
                    ->label('Penerima')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
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
                    ->label('Ongkir')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn (?string $state): string => filled($state)
                        ? 'Rp ' . number_format((float) $state, 0, ',', '.')
                        : '-'),
                Tables\Columns\TextColumn::make('weight_kg')
                    ->label('Berat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (?string $state): string => filled($state)
                        ? rtrim(rtrim(number_format((float) $state, 2, ',', '.'), '0'), ',') . ' kg'
                        : '-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
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