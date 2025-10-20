<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Services\Customer\CustomerService;
use App\Interfaces\RedisInterfaces\RedisCacheInterface;
use Illuminate\Http\JsonResponse;
use App\Models\Customer;

class CustomerController extends Controller
{
    protected CustomerService $customerService;
    protected RedisCacheInterface $redisCache;

    public function __construct(CustomerService $customerService, RedisCacheInterface $redisCache)
    {
        $this->customerService = $customerService;
        $this->redisCache = $redisCache;
    }

    public function index(): CustomerCollection
    {
        $config = config('cache_keys.customer_index');
        $cacheKey = $config['prefix'].request('page', 1);
        
        $customers = $this->redisCache->remember($cacheKey, $config['ttl'], function (){
            return Customer::query()
                ->with('bookings')
                ->paginate(10)
                ->withQueryString();
        });
        
        return (new CustomerCollection($customers))
            ->additional([
                'success' => true,
                'cached' => true,
                'message' => 'Customers retrieved successfully',
            ]);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        try {
            $validatedRequest = $request->validated();

            $customer = $this->customerService->createCustomer($validatedRequest);

            $this->redisCache->forgetPattern("customers:page:*");
            
            return (new CustomerResource($customer))
                ->additional([
                    'success' => true,
                    'message' => 'Customer created successfully',
                ])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Customer $customer): CustomerResource
    {
        $config = config('cache_keys.customer_show');
        $cacheKey = $config['prefix'].$customer->id;

        $customerData = $this->redisCache->rememberWithHit($cacheKey, $config['ttl'], function () use ($customer){
            return $customer->load('bookings');
        });

        return (new CustomerResource($customerData['data']))
            ->additional([
                'success' => true,
                'from_cache' => $customerData['from_cache'] ?? false,
                'message' => 'Customer retrieved successfully',
            ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        $validatedRequest = $request->validated();

        $this->customerService->updateCustomer($customer, $validatedRequest);

        $this->redisCache->forget("customer:{$customer->id}");
        $this->redisCache->forgetPattern("customer:page:*");

        return (new CustomerResource($customer))
            ->additional([
                'success' => true,
                'message' => 'Customer updated successfully',
            ]);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->deleteCustomer($customer);

        $this->redisCache->forget("customer:{$customer->id}");
        $this->redisCache->forgetPattern("customers:page:*");

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ]);
    }
}
