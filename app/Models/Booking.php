<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    #[Scope]
    public function filterService(Builder $query, ?int $serviceId): Builder
    {
        return is_null($serviceId) ? $query : $query->where('service_id', $serviceId);
    }

    #[Scope]
    public function filterStatus(Builder $query, ?string $status): Builder
    {
        return is_null($status) ? $query : $query->where('status', $status);
    }

    #[Scope]
    public function filterDateRange(Builder $query, ?string $dateFrom, ?string $dateTo): Builder
    {
        if (is_null($dateFrom) && is_null($dateTo)) {
            return $query;
        }

        return $query->whereDate('starts_at', '>=', $dateFrom)
            ->whereDate('ends_at', '<=', $dateTo);
    }
}
