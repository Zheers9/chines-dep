<?php

namespace App\Http\Requests\Api\TopAdmin\SubExamType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubExamTypeRequest extends FormRequest
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
                \Illuminate\Validation\Rule::unique('exam_sub_types', 'name')
                    ->where('type_exam_id', $this->type_exam_id)
                    ->ignore($this->route('id'))
            ],
            'type_exam_id' => 'required|exists:type_exams,id',
        ];
    }
}
