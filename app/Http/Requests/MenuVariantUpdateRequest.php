<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuVariantUpdateRequest extends FormRequest
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
            'edit_menu_id' => 'required|exists:menus,id',
            'edit_name' => 'required|string|max:255',
            'edit_price' => 'required|integer|min:0',
            'edit_is_available' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'edit_is_available' => $this->boolean('edit_is_available'),
        ]);
    }
}
