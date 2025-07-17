<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GetBookingListFormRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\Paginator;

class BookingController extends Controller
{
    public function index(GetBookingListFormRequest $request): AnonymousResourceCollection
    {
        $filters = $request->filters();

        /** @var Paginator $bookings $bookings */
        $bookings = Booking::query()
            ->with(['customer', 'service'])
            ->filterService($filters['service_id'])
            ->filterStatus($filters['status'])
            ->filterDateRange($filters['date_from'], $filters['date_to'])
            ->oldest('starts_at')
            ->simplePaginate();

        return BookingResource::collection($bookings);
    }
}
