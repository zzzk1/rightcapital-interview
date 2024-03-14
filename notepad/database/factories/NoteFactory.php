<?php

namespace Database\Factories;

use App\Models\Note;
use Faker\Provider\Text;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 *
 * Factory of note object.
 */
class NoteFactory extends Factory
{
    /**
     * Create a fake note object.
     *
     * @return array<string, text>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'content' => fake()->text(),
        ];
    }
}
