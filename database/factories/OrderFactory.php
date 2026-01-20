<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 500);
        $shippingCost = fake()->randomFloat(2, 50, 20);
        $tax = $subtotal * 0.1;
        $discount = fake()->boolean(30) ? fake()->randomFloat(2, 5, 50) : 0;
        $total = $subtotal + $shippingCost + $tax - $discount;

        return [

            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => null,
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'total' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'discount' => $discount,
            'grand_total' => $total,
            'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'cash_on_delevery']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed']),
            'notes' => fake()->boolean(40) ? fake()->sentence() : null,

            'shipping_address' => json_encode([
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'country' => fake()->country(),
                'postal_code' => fake()->postcode(),
                'phone' => fake()->phoneNumber(),
            ]),

            'billing_address' => json_encode([
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'country' => fake()->country(),
                'postal_code' => fake()->postcode(),
            ]),


        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);
    }
}
