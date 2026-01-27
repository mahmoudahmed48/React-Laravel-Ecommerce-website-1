<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0|max:999999.9999',
            'compare_price' => 'required|numeric|min:0|max:999999.9999|gt:price',
            'quantity' => 'required|integer|min:0|max:999999',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'image' => 'nullable|image|mimes:png,jpg,gif,webp,jpeg|max:5120',
            'images.*' => 'nullable|image|mimes:png,jpg,gif,webp,jpeg|max:5120',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'weigth' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|json',
            'featured' => 'boolean',
            'status' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'

        ];
    }

    // Get Custom Messages for Validator Errors 
    public function messages(): array 
    {
        return [
            'price.min' => 'Price Should Be Greater Than Zero',
            'compare_price.gt' => 'Compare Price Should Be Greater Than Price',
            'image.max' => 'Image Size Should Be Less Than 5 MB',
            'category_id.exists' => 'Selected Category Not Exists'
        ];
    }

    // Prepare The Data For Validation 
    protected function prepareForValidation(): void 
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'status' => $this->boolean('status'),
            'price' => (float) $this->price,
            'compare_price' => $this->compare_price ? (float) $this->compare_price : null,
            'quantity' => (int) $this->quantity,
            'slug' => Str::slug($this->name)
        ]);
    }
}
