<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $anotherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_access_car_routes()
    {
        $this->get(route('cars.index'))->assertRedirect(route('login'));
        $this->get(route('cars.create'))->assertRedirect(route('login'));
        // Attempt to access a non-existent car ID for store, show, edit, update, destroy
        // as we don't know an ID without being authenticated and having created one.
        $this->post(route('cars.store'))->assertRedirect(route('login'));
        $this->get(route('cars.show', ['car' => 1]))->assertRedirect(route('login'));
        $this->get(route('cars.edit', ['car' => 1]))->assertRedirect(route('login'));
        $this->put(route('cars.update', ['car' => 1]))->assertRedirect(route('login'));
        $this->delete(route('cars.destroy', ['car' => 1]))->assertRedirect(route('login'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_their_cars_index()
    {
        Car::factory()->count(3)->create(['user_id' => $this->user->id]);
        Car::factory()->count(2)->create(['user_id' => $this->anotherUser->id]);

        $response = $this->actingAs($this->user)->get(route('cars.index'));

        $response->assertOk();
        $response->assertViewIs('cars.index');
        $response->assertViewHas('cars', function ($cars) {
            return $cars->count() === 3;
        });
        // Ensure cars of another user are not present
        $response->assertViewHas('cars', function ($cars) {
            foreach ($cars as $car) {
                if ($car->user_id === $this->anotherUser->id) {
                    return false;
                }
            }
            return true;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_create_car_form()
    {
        $response = $this->actingAs($this->user)->get(route('cars.create'));

        $response->assertOk();
        $response->assertViewIs('cars.create');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_store_a_new_car()
    {
        $carData = [
            'make' => 'Toyota',
            'model' => 'Corolla',
            'license_plate' => 'ABC-1234',
            'vin' => '1234567890ABCDEFG',
        ];

        $response = $this->actingAs($this->user)->post(route('cars.store'), $carData);

        $response->assertRedirect(route('cars.index'));
        $this->assertDatabaseHas('cars', array_merge($carData, ['user_id' => $this->user->id]));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_their_own_car()
    {
        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('cars.show', $car));

        $response->assertOk();
        $response->assertViewIs('cars.show');
        $response->assertViewHas('car', $car);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_view_another_users_car()
    {
        $otherCar = Car::factory()->create(['user_id' => $this->anotherUser->id]);

        $response = $this->actingAs($this->user)->get(route('cars.show', $otherCar));

        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_edit_form_for_their_own_car()
    {
        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('cars.edit', $car));

        $response->assertOk();
        $response->assertViewIs('cars.edit');
        $response->assertViewHas('car', $car);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_view_edit_form_for_another_users_car()
    {
        $otherCar = Car::factory()->create(['user_id' => $this->anotherUser->id]);

        $response = $this->actingAs($this->user)->get(route('cars.edit', $otherCar));

        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_update_their_own_car()
    {
        $car = Car::factory()->create(['user_id' => $this->user->id]);
        $updatedData = [
            'make' => 'Honda',
            'model' => 'Civic',
            'license_plate' => 'NEW-9876',
            'vin' => 'GFEDCBA0987654321',
        ];

        $response = $this->actingAs($this->user)->put(route('cars.update', $car), $updatedData);

        $response->assertRedirect(route('cars.index'));
        $this->assertDatabaseHas('cars', array_merge(['id' => $car->id], $updatedData));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_update_another_users_car()
    {
        $otherCar = Car::factory()->create(['user_id' => $this->anotherUser->id]);
        $updatedData = [
            'make' => 'Subaru',
            'model' => 'Impreza',
        ];

        $response = $this->actingAs($this->user)->put(route('cars.update', $otherCar), $updatedData);

        $response->assertForbidden();
        $this->assertDatabaseMissing('cars', array_merge(['id' => $otherCar->id], $updatedData));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_delete_their_own_car()
    {
        $car = Car::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('cars.destroy', $car));

        $response->assertRedirect(route('cars.index'));
        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_delete_another_users_car()
    {
        $otherCar = Car::factory()->create(['user_id' => $this->anotherUser->id]);

        $response = $this->actingAs($this->user)->delete(route('cars.destroy', $otherCar));

        $response->assertForbidden();
        $this->assertDatabaseHas('cars', ['id' => $otherCar->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_car_requires_valid_data()
    {
        $response = $this->actingAs($this->user)->post(route('cars.store'), []);
        $response->assertSessionHasErrors(['make', 'model', 'license_plate']); // Removed 'vin'
        $response->assertSessionDoesntHaveErrors(['vin']); // Explicitly assert 'vin' is not an error
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_car_requires_valid_data()
    {
        $car = Car::factory()->create(['user_id' => $this->user->id]);
        $response = $this->actingAs($this->user)->put(route('cars.update', $car), []);
        $response->assertSessionHasErrors(['make', 'model', 'license_plate']); // Removed 'vin'
        $response->assertSessionDoesntHaveErrors(['vin']); // Explicitly assert 'vin' is not an error
    }
}