<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [ 'sometimes', 'string', 'max:255', Rule::unique('stores', 'name')->ignore($this->route('store')) ],
            'email' => [ 'sometimes', 'email', 'max:255', Rule::unique('stores','email')->ignore($this->route('store'))],
            'phone' => [ 'sometimes', 'string', 'max:20'],
            'address' => [ 'sometimes', 'string', 'max:255'],
            'zipcode' => [ 'sometimes', 'string', 'max:5'],
            'city' => [ 'sometimes', 'string', 'max:255'],
            'siret' => [ 'sometimes', 'string', 'max:14'],
        ];
    }
}
