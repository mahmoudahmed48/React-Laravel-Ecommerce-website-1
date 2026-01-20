<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $productName = fake()->words(3, true);
        $price = fake()->randomFloat(2, 10, 1000);
        $compare_price = fake()->boolean(30) ? $price * 1.2 : null;

        return [
            'name' => $productName,
            'slug' => Str::slug($productName),
            'description' => fake()->paragraphs(3, true),
            'price' => $price,
            'compare_price' => $compare_price,
            'quantity' => fake()->numberBetween(0, 100),
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'image' => 'product/' . fake()->randomElement(['product1.jpg', 'product2.jpg', 'product3.jpg', 'product4.jpg',]), 
            'images' => json_encode([
                'products/gallery1.jpg',
                'products/gallery2.jpg',
                'products/gallery3.jpg'
            ]),   

            'category_id' => null,
            'featured' => fake()->boolean(20),
            'status' => true
        ];

    }

    public function featured(): static 
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true
        ]);
    }

    public function outOfStock(): static 
    {
        return $this->state(fn (array $attributes) => [
            'quantity' => 0
        ]);
    }

    public function withDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'compare_price' => $attributes['price'] * 1.3
        ]);
    }
}
