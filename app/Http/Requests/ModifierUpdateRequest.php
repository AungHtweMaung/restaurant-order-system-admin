<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModifierUpdateRequest extends FormRequest
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
        $modifierId = $this->route('modifier')->id;

        return [
            'name' => 'required|string|max:255|unique:modifiers,name,' . $modifierId,
            'type' => 'required|in:avoid,addon,flavor',
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
            'name.required' => 'The name is required.',
            'name.unique' => 'The name must be unique.',
            'type.required' => 'The type is required.',
            'type.in' => 'The type must be one of: avoid, addon, flavor.',
        ];
    }
}
