<?php

namespace App\Http\Requests\Api\TopAdmin\Notion;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotionRequest extends FormRequest
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
            'name_en' => 'required|unique:notions,name_en|string|max:255',
            'name_ku' => 'required|unique:notions,name_ku|string|max:255',
            'name_ar' => 'required|unique:notions,name_ar|string|max:255',
        ];
    }
}
