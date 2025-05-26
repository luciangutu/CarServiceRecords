<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user = User::factory()->create();

        $car = \App\Models\Car::create([
            'make' => 'Dacia',
            'model' => 'Logan',
            'license_plate' => 'B123XYZ',
            'vin' => 'VF1ABCDE123456789',
            'user_id' => $user->id,
        ]);

        \App\Models\ServiceEntry::create([
            'date' => now(),
            'kilometers' => 120000,
            'service_name' => 'Revizie generala',
            'service_action' => 'Schimb ulei, filtre, verificari',
            'parts_replaced' => 'Filtru ulei, filtru aer',
            'cost' => 650.50,
            'user_id' => $user->id,
            'car_id' => $car->id,
        ]);


    }
}
