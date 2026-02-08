<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    Protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'session_id' => $this->faker->uuid,
            'subtotal' => $this->faker->randomFloat(2, 50, 500),
            'tax' => $this->faker->randomFloat(2, 5, 50),
            'shipping' => $this->faker->randomFloat(2, 0, 20),
            'discount' => $this->faker->randomFloat(2, 0, 50),
            'total' => $this->faker->randomFloat(2, 50 , 600),
        ];
    }
}
