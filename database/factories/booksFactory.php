<?php

namespace Database\Factories;

use App\Models\books;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\books>
 */
class booksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn10' => $this->faker->unique()->numerify('###-#######'),
            'isbn13' => $this->faker->unique()->numerify('###-#########'),
            'publisher' => $this->faker->company(),
            'publication_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
        ];
    }
}
