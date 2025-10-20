<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\ServiceSlot;
use App\Interfaces\RedisInterfaces\RedisCacheInterface;
use App\Interfaces\RedisInterfaces\RedisLockInterface;
use App\Interfaces\RedisInterfaces\RedisQueueInterface;
use Illuminate\Support\Facades\DB;
use App\Exceptions\BookingFullException;
use App\Services\ServiceSlot\ServiceSlotService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookingService
{
    protected ServiceSlotService $slotService;
    protected RedisLockInterface $redisLock;
    protected RedisCacheInterface $redisCache;
    protected RedisQueueInterface $redisQueue;

    public function __construct(RedisLockInterface $redisLock, RedisCacheInterface $redisCache,
                                RedisQueueInterface $redisQueue)
    {
        $this->redisLock = $redisLock;
        $this->redisCache = $redisCache;
        $this->redisQueue = $redisQueue;
    }

    /**
     * Create a new booking with concurrency control using Redis locks.
     * 
     * @param array $data - Array containing booking details
     * @return Booking - The created booking instance
     * @throws BookingFullException - If the booking slot is full
     */
    public function createBooking(array $data): Booking
    {
        $slotId = (int) $data['service_slot_id'];
        $lockKey = "slot:{$slotId}:lock";
        
        return $this->redisLock->acquire($lockKey, 10, function () use ($data, $slotId){
            return DB::transaction(function () use ($data, $slotId){

                //1) Verify slot availability
                $slot = ServiceSlot::lockForUpdate()->find($slotId);
                if (!$slot) {
                    throw new ModelNotFoundException("Service slot not found.");
                }

                //2) Check if the slot is booked
                $bookedSlot = Booking::where('service_slot_id', $slotId)
                    ->where('status', 'confirmed')
                    ->exists();
                
                if($bookedSlot){
                    throw new \RuntimeException("Service slot already booked.");
                }

                //3) Create the booking
                $booking = Booking::create([
                    'service_id' => $data['service_id'],
                    'service_slot_id' => $slotId,
                    'customer_id' => $data['customer_id'],
                    'status' => $data['status'] ?? 'confirmed',
                    'payment_status' => $data['payment_status'] ?? 'unpaid',
                    'total_amount' => $data['total_amount'],
                    'notes' => $data['notes'] ?? null,
                ]);

                //4) Invalidate cache for the slot
                $this->invalidateAvailabilityCacheForSlot($slot);

                //5) Dispatch async job (e.g., send confirmation email)
                //$this->redisQueue->dispatch(new \App\Jobs\SendBookingConfirmationEmail($booking), 'emails');
                
                return $booking;
            });
        });
    }

    protected function invalidateAvailabilityCacheForSlot(ServiceSlot $slot): void
    {
        $dateKey = $slot->start_time;
        $this->redisCache->forget("availability:{$dateKey}");
        $this->redisCache->forget("availability:service:{$slot->service_id}:date:{$dateKey}");
    }

    public function updateBooking(Booking $booking, array $data): bool
    {
        return $booking->update($data);
    }

    public function deleteBooking(Booking $booking): bool
    {
        return $booking->delete();
    }
}