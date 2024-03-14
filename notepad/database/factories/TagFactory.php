<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 *
 * Factory of tag object.
 */
class TagFactory extends Factory
{
    /**
     * Create a fake tag object.
     *
     * @return array<string>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
        ];
    }
}
