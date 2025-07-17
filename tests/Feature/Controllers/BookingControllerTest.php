<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Enums\BookingStatus;
use App\Http\Controllers\BookingController;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $authUser;
    private Service $service;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup related models
        $this->service = Service::factory()->create();
        $this->customer = Customer::factory()->create();
        $this->authUser = User::factory()->create();
    }

    public function test_it_returns_paginated_bookings_sorted_by_oldest(): void
    {
        Sanctum::actingAs($this->authUser);

        Booking::factory()->for($this->customer)->for($this->service)->create([
            'starts_at' => now()->subDays(2),
        ]);

        Booking::factory()->for($this->customer)->for($this->service)->create([
            'starts_at' => now()->subDay(),
        ]);

        $uri = action([BookingController::class, 'index']);

        $response = $this->getJson($uri);

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);

        $dates = collect($response->json('data'))->pluck('starts_at');
        $this->assertSame(
            $dates->sort()->values()->toArray(),
            $dates->values()->toArray(),
            'Bookings are not sorted by oldest starts_at'
        );
    }

    public function test_it_filters_by_service_id(): void
    {
        Sanctum::actingAs($this->authUser);

        $otherService = Service::factory()->create();

        Booking::factory()->for($this->customer)->for($this->service)->create();
        Booking::factory()->for($this->customer)->for($otherService)->create();

        $uri = action([BookingController::class, 'index'], ['service_id' => $this->service->id]);

        $response = $this->getJson($uri);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['service_name' => $this->service->name]);
    }

    public function test_it_filters_by_status(): void
    {
        Sanctum::actingAs($this->authUser);

        Booking::factory()->for($this->customer)->for($this->service)->create([
            'status' => BookingStatus::CONFIRMED,
        ]);

        Booking::factory()->for($this->customer)->for($this->service)->create([
            'status' => BookingStatus::CANCELLED,
        ]);

        $uri = action([BookingController::class, 'index'], ['status' => BookingStatus::CONFIRMED->value]);

        $response = $this->getJson($uri);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['status' => BookingStatus::CONFIRMED->value]);
    }

    public function test_it_filters_by_date_range(): void
    {
        Sanctum::actingAs($this->authUser);

        Booking::factory()->for($this->customer)->for($this->service)->create([
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(3),
        ]);

        Booking::factory()->for($this->customer)->for($this->service)->create([
            'starts_at' => now()->addDays(5),
            'ends_at' => now()->addDays(6),
        ]);

        $dateFrom = now()->addDays(1)->toDateString();
        $dateTo = now()->addDays(4)->toDateString();

        $uri = action([BookingController::class, 'index'], ['date_from' => $dateFrom, 'date_to' => $dateTo]);

        $response = $this->getJson($uri);

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_it_requires_date_to_if_date_from_is_present(): void
    {
        Sanctum::actingAs($this->authUser);

        $uri = action([BookingController::class, 'index'], ['date_from' => '2024-01-01']);

        $response = $this->getJson($uri);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('date_to');
    }

    public function test_it_requires_date_from_if_date_to_is_present(): void
    {
        Sanctum::actingAs($this->authUser);

        $uri = action([BookingController::class, 'index'], ['date_to' => '2024-01-10']);

        $response = $this->getJson($uri);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('date_from');
    }

    public function test_it_returns_empty_when_no_bookings_match(): void
    {
        Sanctum::actingAs($this->authUser);

        $uri = action([BookingController::class, 'index'], ['status' => BookingStatus::PENDING->value]);

        $response = $this->getJson($uri);

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_it_fails_with_invalid_status(): void
    {
        Sanctum::actingAs($this->authUser);

        $uri = action([BookingController::class, 'index'], ['status' => 'invalid-status']);

        $response = $this->getJson($uri);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    public function test_it_fails_with_nonexistent_service_id(): void
    {
        Sanctum::actingAs($this->authUser);

        $uri = action([BookingController::class, 'index'], ['service_id' => 9999]);

        $response = $this->getJson($uri);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('service_id');
    }

    public function test_it_fails_with_invalid_date_from_format(): void
    {
        Sanctum::actingAs($this->authUser);

        $uri = action([BookingController::class, 'index'], ['date_from' => '07-01-2025', 'date_to' => '2025-07-10']);

        $response = $this->getJson($uri);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('date_from');
    }

    public function test_it_fails_when_date_to_is_before_date_from(): void
    {
        Sanctum::actingAs($this->authUser);

        $uri = action([BookingController::class, 'index'], [
            'date_from' => '2025-07-10',
            'date_to' => '2025-07-01',
        ]);

        $response = $this->getJson($uri);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('date_to');
    }

    public function test_it_fails_without_authentication(): void
    {
        $uri = action([BookingController::class, 'index']);

        $response = $this->getJson($uri);

        $response->assertUnauthorized();
    }

    public function test_it_fails_with_non_integer_service_id(): void
    {
        Sanctum::actingAs($this->authUser);

        $uri = action([BookingController::class, 'index'], ['service_id' => 'abc']);

        $response = $this->getJson($uri);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('service_id');
    }
}
