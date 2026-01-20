<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $quantity = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 10, 200);
        $total = $quantity * $price;

        return [
            'order_id' => null,
            'product_id' => null,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total
        ];
    }
}
