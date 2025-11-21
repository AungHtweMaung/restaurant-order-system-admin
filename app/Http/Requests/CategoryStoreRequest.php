<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
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
            'eng_name' => 'required|string|max:255|unique:categories,eng_name',
            'mm_name' => 'required|string|max:255|unique:categories,mm_name',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'eng_name.required' => 'The English name is required.',
            'eng_name.unique' => 'The English name must be unique.',
            'mm_name.required' => 'The Myanmar name is required.',
            'mm_name.unique' => 'The Myanmar name must be unique.',
        ];
    }
}
