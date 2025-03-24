<?php

namespace App\Http\Requests\Users;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'lastname' => ['required', 'string', 'max:100'],
            'firstname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:5'],
            'city' => ['required', 'string', 'max:100'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'store_id' => ['nullable', 'integer', 'exists:stores,id'],
            'image' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,jpg,png'],
        ];
    }
}
