<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuVariantStoreRequest extends FormRequest
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
            'menu_id' => 'required|exists:menus,id',
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'is_available' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_available' => $this->boolean('is_available'),
        ]);
    }
}
