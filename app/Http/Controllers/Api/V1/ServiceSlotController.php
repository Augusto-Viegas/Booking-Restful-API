<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use App\Models\ServiceSlot;
use App\Services\SlotGeneratorService;
use App\Interfaces\RedisCacheInterface;
use App\Http\Requests\GenerateServiceSlotRequest;
use App\Http\Requests\StoreServiceSlotRequest;
use App\Http\Requests\UpdateServiceSlotRequest;
use App\Http\Resources\GeneratedServiceSlotResource;
use App\Http\Resources\ServiceSlotCollection;
use App\Http\Resources\ServiceSlotResource;
use App\Services\ServiceSlotService;

class ServiceSlotController extends Controller
{ 
    protected RedisCacheInterface $redisCache;
    protected ServiceSlotService $serviceSlotService;

    public function __construct(RedisCacheInterface $redisCache, ServiceSlotService $serviceSlotService)
    {
        $this->redisCache = $redisCache;
        $this->serviceSlotService = $serviceSlotService;
    }

    /**
     * Function to generate slots for services.
     * You can see an example of how to use it at
     * readme.MD or you can take a deep look at App\Services\ServiceSlotGenerator.php
     * in case you didn't understand how to use. 
     */
    public function multipleStore(GenerateServiceSlotRequest $request, SlotGeneratorService $generator)
    {
        try{
            $slots = $generator->generate($request->validated());
            return response()->json([
                'success' => true,
                'message' => count($slots).' service slots generated successfully',
                'data' => GeneratedServiceSlotResource::collection($slots)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create slots', 
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    public function getAvailableSlots($serviceId)
    {
        $cacheKey = "service:{$serviceId}:slots";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($serviceId){
            return ServiceSlot::where('service_id', $serviceId)
                ->where('status', 'available')
                ->get();
        });
    }

    public function index(): ServiceSlotCollection
    {
        $config = config("cache_keys.service_slot_index");
        $cacheKey = $config['prefix'].request('page', 1);

        $serviceSlots = $this->redisCache->remember($cacheKey, $config["ttl"], function(){
            return ServiceSlot::query()
                ->with(["service"])
                ->paginate(10)
                ->withQueryString();

        });

        return (new ServiceSlotCollection($serviceSlots))
            ->additional([
                'success' => true,
                "cached" => true,
                'message' => 'ServiceSlots retrieved successfully'
            ]);
    }

    public function store(StoreServiceSlotRequest $request): JsonResponse
    {
        try{
            $validatedRequest = $request->validated();
            $serviceSlot = $this->serviceSlotService->createServiceSlot($validatedRequest);

            return (new ServiceSlotResource($serviceSlot))
                ->additional([
                    'success' => true,
                    'message' => 'ServiceSlot created successfully',
                ])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create a service slot',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(ServiceSlot $serviceSlot): ServiceSlotResource
    {
        $config = config("cache_keys.service_slot_show");
        $cacheKey = $config["prefix"].$serviceSlot->id;
        
        $serviceSlotCached = $this->redisCache->rememberWithHit($cacheKey, $config["ttl"], function() use ($serviceSlot){
            return $serviceSlot;
        });
        
        return (new ServiceSlotResource($serviceSlot))
            ->additional([
                'success' => true,
                'from_cache' => $serviceSlotCached["from_cache"],
                'message' => 'ServiceSlot retrieved successfully',
            ]);
    }

    public function update(UpdateServiceSlotRequest $request, ServiceSlot $serviceSlot): ServiceSlotResource
    {
        $validatedRequest = $request->validated();

        $this->serviceSlotService->updateServiceSlot($serviceSlot, $validatedRequest);
        
        $this->redisCache->forget("service_slot:id:$serviceSlot->id");
        $this->redisCache->forgetPattern("service:page:*");

        return (new ServiceSlotResource($serviceSlot))
            ->additional([
                'success' => true,
                'message' => 'Service slot updated successfully',
            ]);
    }

    public function destroy(ServiceSlot $serviceSlot): JsonResponse
    {
        $this->serviceSlotService->deleteServiceSlot($serviceSlot);

        $this->redisCache->forget("service_slot:id:$serviceSlot->id");
        $this->redisCache->forgetPattern("service:page:*");
        return response()->json([
            'success' => true,
            'message' => 'Service slot deleted successfully',
        ], 204);
    }
}
