<?php

namespace App\Http\Requests\Api\TopAdmin\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'second_name' => 'required|string|max:255',
            'third_name' => 'required|string|max:255',
            'fourth_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'code_id' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'notion_id' => 'required|exists:notions,id',
            'specialization' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'place' => 'nullable|string|max:255',
            'nation_code' => 'required|string|max:255|unique:users,nation_code',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ];
    }
}
