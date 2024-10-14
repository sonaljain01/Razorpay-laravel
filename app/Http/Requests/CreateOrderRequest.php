<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'amount' => 'required|numeric|min:1',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Amount is required',
            'amount.min' => 'Amount must be greater than or equal to 1',
            'amount.numeric' => 'Amount must be a number',
            'name.required' => 'Name is required',
            'name.max' => 'Name must not exceed 255 characters',
            'name.string' => 'Name must be a string',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',

        ];
    }
}
