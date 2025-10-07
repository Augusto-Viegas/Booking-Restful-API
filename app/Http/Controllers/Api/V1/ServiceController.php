<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceCollection;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\JsonResponse;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ServiceCollection
    {
        $services = Service::query()
            ->paginate(10)
            ->withQueryString();
        return (new ServiceCollection($services))
            ->additional([
                'success' => true,
                'message' => 'Services retrieved successfully',
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        try{
            $service = Service::create($request->validated());

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

    /**
     * Display the specified resource.
     */
    public function show(Service $service): ServiceResource
    {
        return (new ServiceResource($service))
            ->additional([
                'success' => true,
                'message' => 'Service retrieved successfully',
            ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): ServiceResource
    {
        $service->update($request->validated());

        return (new ServiceResource($service))
            ->additional([
                'success' => true,
                'message' => 'Service updated successfully',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
        ]);
    }
}
