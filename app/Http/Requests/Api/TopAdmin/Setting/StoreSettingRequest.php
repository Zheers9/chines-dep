<?php

namespace App\Http\Requests\Api\TopAdmin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
            'academic_year' => ['required', 'unique:settings,academic_year', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'active' => 'required|boolean',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
        ];
    }
}
