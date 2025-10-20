<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\Payment\FakePaymentGateway;
use App\Services\Payment\PaymentService;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentService(new FakePaymentGateway());
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $payment): JsonResponse
    {
        try{
            $validatedRequest = $payment->validated();
            $payment = $this->paymentService->createPayment($validatedRequest);

            return (new PaymentResource($payment))
                ->additional([
                    'success' => true,
                    'message' => 'Payment successfully processed'
                ])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Payment failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
