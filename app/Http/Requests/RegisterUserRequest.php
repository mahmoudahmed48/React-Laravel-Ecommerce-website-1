<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required','string','confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s]+$/',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ];
    }

    // Get Custom Message For Validation Errors 

    public function messages(): array 
    {
        return [
            'name.required' => 'Name Is Required',
            'email.required' => 'Email Is Required',
            'email.unique' => 'This Email Already Exists.',
            'password.required' => 'Password Is Required',
            'password.confirmed' => 'Passwords Does not Match',
            'phone.regex' => 'Phone Number Not Valid'
        ];

    }

    // Get Custom Attributes For Validation Errors 

    public function attributes(): array 
    {
        return [
            'name' => 'name',
            'email' => 'email',
            'password' => 'password',
            'phone' => 'phone'
        ];
    }
}
