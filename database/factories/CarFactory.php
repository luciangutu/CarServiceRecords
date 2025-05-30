<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Car::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'make' => $this->faker->company,
            'model' => $this->faker->word, // Or a more specific vehicle model generator if available
            'license_plate' => $this->faker->bothify('???-####'), // Example license plate format
            'vin' => $this->faker->unique()->regexify('[A-HJ-NPR-Z0-9]{17}'), // Standard VIN format
            'user_id' => User::factory(),
        ];
    }
}
