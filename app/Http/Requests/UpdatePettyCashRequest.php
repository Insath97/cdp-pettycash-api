<?php

namespace App\Http\Requests;

use App\Models\SystemSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePettyCashRequest extends FormRequest
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
            'reference_number' => 'sometimes|string|max:255|unique:petty_cashes,reference_number,' . $this->route('id'),
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'branch_id' => 'sometimes|exists:branches,id',
            'department_id' => 'sometimes|nullable|exists:departments,id',
            'date_needed' => 'sometimes|date',
            'category_id' => 'sometimes|exists:categories,id',
            'description' => 'sometimes|nullable|string',
            'type' => 'sometimes|in:new_purchase,reimbursement',
            'amount' => 'sometimes|numeric|min:0|max:' . (SystemSetting::getSetting('max_request_amount', 4000)),
            'receipt_image_path' => 'sometimes|file|max:20480',
            'account_number' => 'sometimes|string|max:50',
            'bank_name' => 'sometimes|string|max:100',
            'bank_branch' => 'sometimes|string|max:100',
            'status' => 'sometimes|in:pending,verified,approved,rejected',
            'payment_status' => 'sometimes|in:pending,onhold,paid',
            'verified_by' => 'sometimes|nullable|exists:users,id',
            'approved_by' => 'sometimes|nullable|exists:users,id',
            'paid_by' => 'sometimes|nullable|exists:users,id',
            'verified_description' => 'sometimes|nullable|string',
            'approved_description' => 'sometimes|nullable|string',
            'rejected_description' => 'sometimes|nullable|string',
            'payment_description' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $errorMessages = $validator->errors();

        $fieldErrors = collect($errorMessages->getMessages())->map(function ($messages, $field) {
            return [
                'field' => $field,
                'messages' => $messages,
            ];
        })->values();

        $message = $fieldErrors->count() > 1
            ? 'There are multiple validation errors. Please review the form and correct the issues.'
            : 'There is an issue with the input for ' . $fieldErrors->first()['field'] . '.';

        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $fieldErrors,
        ], 422));
    }
}
