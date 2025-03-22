<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => [ 'string', 'max:255', 'unique:stores'],
            'email' => [ 'email', 'max:255', 'unique:stores'],
            'phone' => [ 'string', 'max:20'],
            'address' => [ 'string', 'max:255'],
            'zipcode' => [ 'string', 'max:5'],
            'city' => [ 'string', 'max:255'],
            'siret' => [ 'string', 'max:14'],
        ];
    }
}
