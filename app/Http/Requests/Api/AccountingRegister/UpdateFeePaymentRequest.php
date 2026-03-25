<?php

namespace App\Http\Requests\Api\AccountingRegister;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeePaymentRequest extends FormRequest
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
            'fee_id' => 'required|exists:fees,id',
            'user_id' => 'required|exists:users,id',
            'exam_sub_type_id' => 'required|exists:exam_sub_types,id',
            'pay' => 'required',
        ];
    }
}
