<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending',
        'picked_up',
        'in_transit',
        'delivered',
        'returned',
    ];
    public static function statusOptions(): array
    {
        return collect(self::STATUSES)
            ->mapWithKeys(fn (string $status): array => [$status => trans('statuses.' . $status)])
            ->toArray();
    }


    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'tracking_number',
        'sender_name',
        'sender_phone',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'status',
        'estimated_delivery',
        'weight_kg',
        'price',
        'proof_of_delivery',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'price' => 'decimal:2',
        'estimated_delivery' => 'date',
    ];

    // Relasi: Satu Paket punya BANYAK update status
    public function updates(): HasMany
    {
        return $this->hasMany(StatusUpdate::class)->orderBy('happened_at', 'desc');
    }

    // --- FITUR ADVANCED (Helper) ---
    
    // Ambil status terakhir secara otomatis
    public function getLastStatusAttribute()
    {
        return $this->updates->first()?->status ?? $this->status;
    }

    // Ambil lokasi terakhir
    public function getLastLocationAttribute()
    {
        return $this->updates->first()?->location ?? '-';
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(
            fn (): string => trans('statuses.' . ($this->status ?? 'pending')),
        );
    }
}