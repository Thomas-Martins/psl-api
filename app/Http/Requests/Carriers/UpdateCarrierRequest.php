<?php

namespace App\Http\Requests\Carriers;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCarrierRequest extends FormRequest
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
            'name' => [
                'string',
                'max:255',
                Rule::unique('carriers')->ignore($this->route('carrier')->id)
            ],
            'email' => [
                'email',
                'max:255',
                Rule::unique('carriers')->ignore($this->route('carrier')->id)
            ],
            'phone' => [ 'string', 'max:20'],
            'address' => [ 'string', 'max:255'],
            'zipcode' => [ 'string', 'max:5'],
            'city' => [ 'string', 'max:255'],
            'contact_person_firstname' => [ 'string', 'max:50'],
            'contact_person_lastname' => [ 'string', 'max:50'],
            'contact_person_phone' => [ 'string', 'max:20'],
            'contact_person_email' => [ 'email', 'max:255'],
        ];
    }
}
