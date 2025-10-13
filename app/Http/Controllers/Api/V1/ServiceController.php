<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceCollection;
use App\Http\Resources\ServiceResource;
use App\Interfaces\RedisCacheInterface;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use App\Models\Service;

class ServiceController extends Controller
{
    protected ServiceService $serviceService;
    protected RedisCacheInterface $redisCache;

    public function __construct(RedisCacheInterface $redisCache, ServiceService $serviceService)
    {
        $this->redisCache = $redisCache;
        $this->serviceService = $serviceService;
    }

    public function index(): ServiceCollection
    {
        $config = config("cache_keys.service_index");
        $cacheKey = $config['prefix'].request('page', 1);

        $services = $this->redisCache->remember($cacheKey, $config['ttl'], function(){
            return Service::query()
                ->paginate(10)
                ->withQueryString();
        });

        return (new ServiceCollection($services))
            ->additional([
                'success' => true,
                'cached' => true,
                'message' => 'Services retrieved successfully',
            ]);
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        try{
            $validatedRequest = $request->validated();
            $service = $this->serviceService->createService($validatedRequest);

            return (new ServiceResource($service))
                ->additional([
                    'success' => true,
                    'message' => 'Service created successfully',
                ])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Service $service): ServiceResource
    {
        $config = config("cache_keys.service_show");
        $cacheKey = $config["prefix"].$service->id;

        $serviceCached = $this->redisCache->rememberWithHit($cacheKey, $config["ttl"], function() use ($service){
            return $service;
        });

        return (new ServiceResource($serviceCached['data']))
            ->additional([
                'success' => true,
                'from_cache' => $serviceCached["from_cache"],
                'message' => 'Service retrieved successfully',
            ]);
    }

    public function update(UpdateServiceRequest $request, Service $service): ServiceResource
    {
        $validatedRequest = $request->validated();

        $this->serviceService->updateService($service, $validatedRequest);

        //invalidate cache
        $this->redisCache->forget("service:id:".$service->id);
        $this->redisCache->forgetPattern("services:page*");

        return (new ServiceResource($service))
            ->additional([
                'success' => true,
                'message' => 'Service updated successfully',
            ]);
    }

    public function destroy(Service $service): JsonResponse
    {
        $this->serviceService->deleteService($service);

        $this->redisCache->forget("service:id:".$service->id);
        $this->redisCache->forgetPattern("services:page*");

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
        ]);
    }
}
