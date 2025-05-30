<?php

namespace Tests\Unit;

use App\Models\Car;
use App\Models\ServiceEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ServiceEntryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function service_entry_factory_can_create_a_service_entry()
    {
        $serviceEntry = ServiceEntry::factory()->create();
        $this->assertInstanceOf(ServiceEntry::class, $serviceEntry);
    }

    /** @test */
    public function service_entry_has_fillable_attributes()
    {
        $fillable = [
            'user_id',
            'date',
            'kilometers',
            'car_id',
            'service_name',
            'service_action',
            'parts_replaced',
            'cost'
        ];
        $serviceEntry = new ServiceEntry();
        $this->assertEquals($fillable, $serviceEntry->getFillable());
    }

    /** @test */
    public function service_entry_date_attribute_is_cast_to_carbon_instance()
    {
        $serviceEntry = ServiceEntry::factory()->create();
        $this->assertInstanceOf(Carbon::class, $serviceEntry->date);
    }

    /** @test */
    public function service_entry_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $user->id]);
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $car->id, 'user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $serviceEntry->user);
        $this->assertEquals($user->id, $serviceEntry->user->id);
    }

    /** @test */
    public function service_entry_belongs_to_a_car()
    {
        $car = Car::factory()->create();
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $car->id, 'user_id' => $car->user_id]);

        $this->assertInstanceOf(Car::class, $serviceEntry->car);
        $this->assertEquals($car->id, $serviceEntry->car->id);
    }
}
