<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
       // Get the book ID from route parameter (assuming route model binding)
        $bookId = $this->route('book') ? $this->route('book')->id : $this->route('id');

        return [
            'ISBN' => [
                'sometimes',
                'string',
                'size:13',
                'regex:/^[0-9]+$/',
                Rule::unique('books', 'ISBN')->ignore($bookId), // Ignore current book
            ],
            'title' => [
                'sometimes',
                'string',
                'max:70',

            ],
            'price' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:99.99',
            ],
            'mortgage' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:9999.99',
                'gt:price',
            ],
            'authorship_date' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],
            'category_id' => [
                'sometimes',
                'integer',
                'exists:categories,id',
            ],
            'cover' => [
                'sometimes',
                'image',
                'mimes:jpeg,png,jpg,gif,webp,svg',
                'max:2024',
            ],

        ];
    }
}
