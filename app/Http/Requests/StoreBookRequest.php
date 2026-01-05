<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
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
             'ISBN' => [
                'required',
                'string',
                'size:13',
                'regex:/^[0-9]+$/', // Only numbers (standard ISBN format)
                Rule::unique('books', 'ISBN'), // Must be unique in books table
            ],
            'title' => [
                'required',
                'string',
                'max:70'],

                'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99.99',
            ],
              'mortgage' => [
                'required',
                'numeric',
                'min:0',
                'max:9999.99',
                'gt:price', // Mortgage must be greater than price
            ],
            'authorship_date' => [
                'nullable',
                'date',
                'before_or_equal:today', // Can't be in the future
            ],
              'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'cover' => [
                'sometimes',
                'image', // Must be an image
                'mimes:jpeg,png,jpg,gif,webp,svg', // Allowed image types
                'max:2024', // 2MB max size (in KB)

            ],
        ];
    }
}
