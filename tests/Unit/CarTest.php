<?php

namespace Tests\Unit;

use App\Models\Car;
use App\Models\User;
use App\Models\ServiceEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function car_factory_can_create_a_car()
    {
        $car = Car::factory()->create();
        $this->assertInstanceOf(Car::class, $car);
    }

    /** @test */
    public function car_has_fillable_attributes()
    {
        $fillable = ['make', 'model', 'license_plate', 'vin', 'user_id'];
        $car = new Car();
        $this->assertEquals($fillable, $car->getFillable());
    }

    /** @test */
    public function car_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $car->user);
        $this->assertEquals($user->id, $car->user->id);
    }

    /** @test */
    public function car_has_many_service_entries()
    {
        $car = Car::factory()->create();
        ServiceEntry::factory()->count(3)->create(['car_id' => $car->id, 'user_id' => $car->user_id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $car->serviceEntries);
        $this->assertCount(3, $car->serviceEntries);
        foreach ($car->serviceEntries as $entry) {
            $this->assertInstanceOf(ServiceEntry::class, $entry);
        }
    }
}
