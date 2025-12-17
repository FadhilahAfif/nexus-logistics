<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StatusUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'location',
        'description',
        'proof_of_delivery',
        'status',
        'happened_at',
    ];

    // Pastikan kolom 'happened_at' dibaca sebagai format Tanggal yang benar oleh sistem
    protected $casts = [
        'happened_at' => 'datetime',
    ];

    protected function statusLabel(): Attribute
    {
        return Attribute::get(
            fn (): string => trans('statuses.' . ($this->status ?? 'pending')),
        );
    }

    protected function proofOfDeliveryUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string => $this->proof_of_delivery 
                ? Storage::disk('s3')->url($this->proof_of_delivery) 
                : null,
        );
    }

    // Relasi: Status ini milik SIAPA? Milik Shipment.
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}