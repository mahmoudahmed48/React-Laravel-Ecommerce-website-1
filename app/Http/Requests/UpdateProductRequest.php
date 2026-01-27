<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateProductRequest extends FormRequest
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

        $productId = $this->route('product');

        return [
            'name' => 'required|string|max:255|unique:products,name,' . $productId,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0|max:999999.9999',
            'compare_price' => 'required|numeric|min:0|max:999999.9999|gt:price',
            'quantity' => 'required|integer|min:0|max:999999',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $productId,
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $productId,
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

    protected function prepareForValidation(): void 
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'status' => $this->boolean('status'),
            'price' => $this->has('price') ? (float) $this->price : null,
            'compare_price' => $this->compare_price ? (float) $this->compare_price : null,
            'quantity' => $this->has('quantity') ? (int) $this->quantity : null,
        ]);
    }
}
