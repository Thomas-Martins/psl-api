<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductsRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'reference' => 'sometimes|required|string|max:255|unique:products,reference,' . $this->product->id,
            'location' => 'sometimes|required|string|max:255|unique:products,location,' . $this->product->id,
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
