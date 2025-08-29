<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
        if ($this->is('login')) { // or $this->routeIs('login')
            return [
                'email' => 'required|email',
                'password' => 'required|string',
            ];
        }

        if ($this->is('mfa')) { // or $this->routeIs('login')
            return [
                'otp' => 'required|numeric|min:6'
            ];
        }

        if ($this->is('register')) { // or $this->routeIs('register')
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be in a string',
            'name.max' => 'Name cannot exceed 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'otp' => 'Please enter valid OTP'
        ];
    }

    protected $stopOnFirstFailure = true;
}
