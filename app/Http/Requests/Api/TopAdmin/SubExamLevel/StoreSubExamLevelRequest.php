<?php

namespace App\Http\Requests\Api\TopAdmin\SubExamLevel;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubExamLevelRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('exam_sub_levels', 'name')
                    ->where('exam_sub_type_id', $this->exam_sub_type_id)
            ],
            'exam_sub_type_id' => 'required|exists:exam_sub_types,id',
        ];
    }
}
