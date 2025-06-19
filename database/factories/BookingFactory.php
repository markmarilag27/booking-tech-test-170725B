<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-1 month', '+1 month');
        $endsAt = fake()->dateTimeBetween($startsAt, $startsAt->format('Y-m-d H:i:s') . ' +4 hours');

        return [
            'customer_id' => Customer::factory(),
            'service_id' => Service::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => fake()->randomElement(BookingStatus::cases()),
            'total_price_cents' => fake()->numberBetween(5000, 50000),
        ];
    }
}
