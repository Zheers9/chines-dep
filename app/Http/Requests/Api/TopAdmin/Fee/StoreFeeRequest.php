<?php

namespace App\Http\Requests\Api\TopAdmin\Fee;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeRequest extends FormRequest
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
            'exam_sub_type_id' => [
                'required',
                'exists:exam_sub_types,id',
                \Illuminate\Validation\Rule::unique('fees', 'exam_sub_type_id')->where('setting_id', $this->setting_id)
            ],
            'setting_id' => 'required|exists:settings,id',
            'payment_amount' => 'nullable|string',
        ];
    }
}
