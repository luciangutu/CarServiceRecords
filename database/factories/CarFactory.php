<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'license_plate' => $this->faker->unique()->bothify('??##???'),
            'make' => $this->faker->company,
            'model' => $this->faker->word,
        ];
    }
}

