<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Ensure the user is authenticated
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->is('category/category')) { // or $this->routeIs('login')
            return [
                'category-name' => 'required|string|max:255'
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'category-name.required' => 'Category name is required',
            'category-name.string' => 'Category name must be in string',
            'category-name.max' => 'Category name cannot exceed 255 characters',
        ];
    }

    protected $stopOnFirstFailure = true;
}
