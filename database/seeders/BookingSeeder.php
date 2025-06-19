<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $services = Service::all();

        Booking::factory()->count(30)->create([
            'customer_id' => fn() => $customers->random()->id,
            'service_id' => fn() => $services->random()->id,
        ]);
    }
}
