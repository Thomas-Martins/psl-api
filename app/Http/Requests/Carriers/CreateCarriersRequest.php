<?php

namespace App\Http\Requests\Carriers;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCarriersRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:carriers',
            'email' => 'required|email|max:255|unique:carriers',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zipcode' => 'required|string|max:5',
            'contact_person_firstname' => 'nullable|string|max:50',
            'contact_person_lastname' => 'nullable|string|max:50',
            'contact_person_phone' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
            'image' => ['nullable', 'image', 'max:2048', 'mimes:jpeg,jpg,png'],
        ];
    }
}
