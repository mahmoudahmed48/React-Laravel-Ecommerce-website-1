<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $categoryName = fake()->unique()->words(2, true);

        return [
            
            'name' => $categoryName,
            'slug' => Str::slug($categoryName),
            'description' => fake()->paragraph(2),
            'image' => 'categories/' . fake()->randomElement([
                'electronics.jpg',
                'clothing.jpg',
                'book.jpg',
                'home.jpg',
                'sports.jpg'
            ]),
            'parent_id' => null,
            'status' => true
        ];
    }

    public function parent():static 
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null
        ]);
    }

    public function child(int $parentId): static 
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId
        ]);
    }
}
