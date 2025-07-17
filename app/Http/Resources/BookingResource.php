<?php

namespace App\Http\Resources;

use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge([
            'id' => $this->id,
            'customer_full_name' => $this->whenLoaded('customer', fn() => "{$this->customer->first_name} {$this->customer->last_name}"),
            'service_name' => $this->whenLoaded('service', fn() => $this->service->name),
            'starts_at' => $this->starts_at->toIso8601String(),
            'ends_at' => $this->ends_at->toIso8601String(),
            'status' => $this->status,
        ], $this->whenConfirmed());
    }

    private function whenConfirmed(): array
    {
        $status = BookingStatus::from($this->status->value);

        if ($status === BookingStatus::CONFIRMED) {
            return [
                'total_price' => Number::currency($this->total_price_cents / 100, 'USD'),
            ];
        }

        return [];
    }
}
