<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        // Allow clients to create orders
        if ($user->role === Role::CLIENT) {
            // Additional check to ensure the user can only create orders for themselves
            return $this->input('user_id') == $user->id;
        }
        
        // Allow admins and managers to create orders for any user
        return in_array($user->role, [Role::ADMIN, Role::GESTIONNAIRE]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|max:100',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1|max:1000',
            'products.*.price' => 'required|numeric|min:0|max:9999.99',
            'complementary_info' => 'nullable|string|max:1000',
            'locale' => 'nullable|string|in:fr,en',
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
            'user_id.required' => 'L\'identifiant de l\'utilisateur est requis',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas',
            'products.required' => 'La liste des produits est requise',
            'products.array' => 'La liste des produits doit être un tableau',
            'products.*.id.required' => 'L\'identifiant du produit est requis',
            'products.*.id.exists' => 'Un des produits spécifiés n\'existe pas',
            'products.*.quantity.required' => 'La quantité est requise pour chaque produit',
            'products.*.quantity.integer' => 'La quantité doit être un nombre entier',
            'products.*.quantity.min' => 'La quantité doit être au moins 1',
            'products.*.quantity.max' => 'La quantité doit être au maximum 1000',
            'products.*.price.required' => 'Le prix est requis pour chaque produit',
            'products.*.price.numeric' => 'Le prix doit être un nombre',
            'products.*.price.min' => 'Le prix ne peut pas être négatif',
            'products.*.price.max' => 'Le prix doit être au maximum 9999999.99',
            'locale.string' => 'La locale doit être une chaîne de caractères',
            'locale.in' => 'La locale doit être soit fr soit en',
        ];
    }
}
