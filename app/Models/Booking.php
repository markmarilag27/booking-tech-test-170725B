<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_id',
        'starts_at',
        'ends_at',
        'status',
        'total_price_cents',
    ];

    protected $casts = [
        'status' => BookingStatus::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Get the customer that owns this booking.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the service for this booking.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
