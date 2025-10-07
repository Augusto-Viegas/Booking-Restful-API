<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingCollection;
use App\Services\BookingService;
use App\Interfaces\RedisCacheInterface;
use Illuminate\Http\JsonResponse;
use App\Models\Booking;

class BookingController extends Controller
{
    protected BookingService $bookingService;
    protected RedisCacheInterface $redisCache;

    public function __construct(BookingService $bookingService, RedisCacheInterface $redisCache)
    {
        $this->bookingService = $bookingService;
        $this->redisCache = $redisCache;
    }

    public function availability()
    {
        //TODO: Implement availability logic
    }

    public function index(): BookingCollection
    {
        $config = config('cache_keys.booking_index');
        $cacheKey = $config['prefix'].request('page', 1);
        
        $booking = $this->redisCache->remember($cacheKey, $config['ttl'], function() {
            return Booking::query()
                ->with(['customer', 'service', 'serviceSlot'])
                ->paginate(10)
                ->withQueryString();
        });

        return (new BookingCollection($booking))
            ->additional([
                'success' => true,
                'cached' => true,
                'message' => 'Booking list retrieved successfully'
            ]);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->createBooking($request->validated());

            return (new BookingResource($booking))
                ->additional([
                    'success' => true,
                    'message' => 'Booking created successfully'
                ])
                ->response()
                ->setStatusCode(201);

        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to create a booking',
                'error' => $e->getMessage(),
                'details' => $e->getTrace(),
            ], 500);
        }
    }

    public function show(Booking $booking): BookingResource
    {
        $cacheKey = "booking:{$booking->id}";

        $bookingCached = $this->redisCache->rememberWithHit($cacheKey, 60, function() use ($booking){
            return $booking->load(['customer', 'service', 'serviceSlot']);
        });

        return (new BookingResource($bookingCached))
            ->additional([
                'success' => true,
                'from_cache' => $bookingCached['from_cache'],
                'message' => 'Booking retrived successfully',
            ]);
    }

    public function update(UpdateBookingRequest $request, Booking $booking): BookingResource
    {
        $booking->update($request->validated());

        //Invalidate cache
        $this->redisCache->forget("booking:{$booking->id}");
        $this->redisCache->forget("bookings:page:1");

        return (new BookingResource($booking))
            ->additional([
                'success' => true,
                'message' => 'Booking updated successfully'
            ]);
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $booking->delete();

        $this->redisCache->forget("booking:{$booking->id}");
        $this->redisCache->forget("bookings:page:1");

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully',
        ]);
    }
}
