<?php

namespace Database\Factories;

use App\Models\ServiceEntry;
use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Ensure a car exists to associate with the service entry
        $car = Car::factory()->create();

        return [
            'date' => $this->faker->date(),
            'kilometers' => $this->faker->numberBetween(1000, 300000),
            'service_name' => $this->faker->words(3, true), // Generates a string of 3 random words
            'service_action' => $this->faker->sentence,
            'parts_replaced' => $this->faker->words(5, true), // Generates a string of 5 random words
            'cost' => $this->faker->randomFloat(2, 20, 1000), // Generates a float with 2 decimal places between 20 and 1000
            'car_id' => $car->id,
            'user_id' => $car->user_id, // Associate with the same user as the car
        ];
    }
}
