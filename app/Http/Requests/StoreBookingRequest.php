<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\BookingPaymentStatusEnum;
use App\Enums\BookingStatusEnum;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'service_slot_id' => 'required|exists:service_slots,id',
            'customer_id' => 'required|exists:customers,id',
            'status' => ['required', Rule::enum(BookingStatusEnum::class)],
            'payment_status' => ['required', Rule::enum(BookingPaymentStatusEnum::class)],
            'notes' => 'string',
        ];
    }
}
