<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\ServiceEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $anotherUser;
    protected Car $userCar;
    protected Car $anotherUserCar;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();

        $this->userCar = Car::factory()->create(['user_id' => $this->user->id]);
        $this->anotherUserCar = Car::factory()->create(['user_id' => $this->anotherUser->id]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_service_entry_routes()
    {
        $this->get(route('cars.service_entries.index', ['car' => $this->userCar->id]))->assertRedirect(route('login'));
        $this->get(route('cars.service_entries.create', ['car' => $this->userCar->id]))->assertRedirect(route('login'));
        $this->post(route('cars.service_entries.store', ['car' => $this->userCar->id]))->assertRedirect(route('login'));
        // Dummy service entry ID for routes that require one
        // For shallow routes, 'car' parameter is not needed for show, edit, update, destroy
        $this->get(route('service_entries.show', ['service_entry' => 1]))->assertRedirect(route('login'));
        $this->get(route('service_entries.edit', ['service_entry' => 1]))->assertRedirect(route('login'));
        $this->put(route('service_entries.update', ['service_entry' => 1]))->assertRedirect(route('login'));
        $this->delete(route('service_entries.destroy', ['service_entry' => 1]))->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_view_service_entries_index_for_their_car()
    {
        ServiceEntry::factory()->count(3)->create(['car_id' => $this->userCar->id, 'user_id' => $this->user->id]);
        // Entries for another user's car, should not be visible
        ServiceEntry::factory()->count(2)->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);

        $response = $this->actingAs($this->user)->get(route('cars.service_entries.index', $this->userCar));

        $response->assertOk();
        $response->assertViewIs('service_entries.index');
        $response->assertViewHas('car', $this->userCar);
        $response->assertViewHas('entries', function ($entries) { // Changed 'serviceEntries' to 'entries'
            return $entries->count() === 3;
        });
    }

    /** @test */
    public function authenticated_user_cannot_view_service_entries_index_for_another_users_car()
    {
        $response = $this->actingAs($this->user)->get(route('cars.service_entries.index', $this->anotherUserCar));
        $response->assertForbidden();
    }


    /** @test */
    public function authenticated_user_can_view_create_service_entry_form_for_their_car()
    {
        $response = $this->actingAs($this->user)->get(route('cars.service_entries.create', $this->userCar));

        $response->assertOk();
        $response->assertViewIs('service_entries.create');
        $response->assertViewHas('car', $this->userCar);
    }

    /** @test */
    public function authenticated_user_cannot_view_create_service_entry_form_for_another_users_car()
    {
        $response = $this->actingAs($this->user)->get(route('cars.service_entries.create', $this->anotherUserCar));
        $response->assertForbidden();
    }

    /** @test */
    public function authenticated_user_can_store_a_new_service_entry_for_their_car()
    {
        $serviceEntryData = [
            'date' => now()->format('Y-m-d'),
            'kilometers' => 10000,
            'service_name' => 'Oil Change',
            'service_action' => 'Changed oil and filter.',
            'parts_replaced' => 'Oil filter, Engine oil',
            'cost' => 50.99,
        ];

        $response = $this->actingAs($this->user)->post(route('cars.service_entries.store', $this->userCar), $serviceEntryData);

        $response->assertRedirect(route('cars.service_entries.index', $this->userCar));
        // Adjust date format for assertDatabaseHas if DB stores DATE as YYYY-MM-DD 00:00:00
        $dbServiceEntryData = $serviceEntryData;
        $dbServiceEntryData['date'] = now()->format('Y-m-d') . ' 00:00:00';

        $this->assertDatabaseHas('service_entries', array_merge($dbServiceEntryData, [
            'car_id' => $this->userCar->id,
            'user_id' => $this->user->id
        ]));
    }

    /** @test */
    public function authenticated_user_cannot_store_service_entry_for_another_users_car()
    {
        $serviceEntryData = ServiceEntry::factory()->make()->toArray();
        $response = $this->actingAs($this->user)->post(route('cars.service_entries.store', $this->anotherUserCar), $serviceEntryData);
        $response->assertForbidden();
    }

    /** @test */
    public function authenticated_user_can_view_service_entry_for_their_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->userCar->id, 'user_id' => $this->user->id]);

        // Show route is shallow, does not need $this->userCar in route parameters
        $response = $this->actingAs($this->user)->get(route('service_entries.show', $serviceEntry));

        $response->assertOk();
        $response->assertViewIs('service_entries.show');
        $response->assertViewHas('serviceEntry', $serviceEntry);
        $response->assertViewHas('car', $this->userCar);
    }

    /** @test */
    public function authenticated_user_cannot_view_service_entry_for_another_users_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        // Show route is shallow
        $response = $this->actingAs($this->user)->get(route('service_entries.show', $serviceEntry));
        $response->assertForbidden();
    }

     /** @test */
    public function authenticated_user_cannot_view_service_entry_of_their_car_if_entry_belongs_to_another_car()
    {
        // This scenario should ideally not happen due to route structure, but good to test policy
        $serviceEntryForAnotherCar = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        // Show route is shallow
        $response = $this->actingAs($this->user)->get(route('service_entries.show', $serviceEntryForAnotherCar));
        $response->assertForbidden(); // Or NotFound, depending on policy implementation for mismatched car and entry
    }

    /** @test */
    public function authenticated_user_can_view_edit_service_entry_form_for_their_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->userCar->id, 'user_id' => $this->user->id]);
        // Edit route is shallow
        $response = $this->actingAs($this->user)->get(route('service_entries.edit', $serviceEntry));

        $response->assertOk();
        $response->assertViewIs('service_entries.edit');
        $response->assertViewHas('serviceEntry', $serviceEntry);
        $response->assertViewHas('car', $this->userCar);
    }

    /** @test */
    public function authenticated_user_cannot_view_edit_form_for_service_entry_on_another_users_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        // Edit route is shallow
        $response = $this->actingAs($this->user)->get(route('service_entries.edit', $serviceEntry));
        $response->assertForbidden();
    }

    /** @test */
    public function authenticated_user_can_update_service_entry_for_their_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->userCar->id, 'user_id' => $this->user->id]);
        $updateData = [
            'date' => now()->subDay()->format('Y-m-d'),
            'kilometers' => 12000,
            'service_name' => 'Tyre Rotation',
            'service_action' => 'Rotated tyres.',
            'parts_replaced' => 'None',
            'cost' => 25.00,
        ];

        // Update route is shallow
        $response = $this->actingAs($this->user)->put(route('service_entries.update', $serviceEntry), $updateData);
        $response->assertRedirect(route('service_entries.show', $serviceEntry)); // Corrected redirect assertion

        // Adjust date format for assertDatabaseHas
        $dbUpdateData = $updateData;
        $dbUpdateData['date'] = now()->subDay()->format('Y-m-d') . ' 00:00:00';
        $this->assertDatabaseHas('service_entries', array_merge(['id' => $serviceEntry->id], $dbUpdateData));
    }

    /** @test */
    public function authenticated_user_cannot_update_service_entry_for_another_users_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        $updateData = ServiceEntry::factory()->make()->toArray();
        // Update route is shallow
        $response = $this->actingAs($this->user)->put(route('service_entries.update', $serviceEntry), $updateData);
        $response->assertForbidden();
    }

    /** @test */
    public function authenticated_user_can_delete_service_entry_for_their_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->userCar->id, 'user_id' => $this->user->id]);
        // Destroy route is shallow
        $response = $this->actingAs($this->user)->delete(route('service_entries.destroy', $serviceEntry));

        $response->assertRedirect(route('cars.service_entries.index', $this->userCar));
        $this->assertDatabaseMissing('service_entries', ['id' => $serviceEntry->id]);
    }

    /** @test */
    public function authenticated_user_cannot_delete_service_entry_for_another_users_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        // Destroy route is shallow
        $response = $this->actingAs($this->user)->delete(route('service_entries.destroy', $serviceEntry));
        $response->assertForbidden();
    }

    /** @test */
    public function store_service_entry_requires_valid_data()
    {
        $response = $this->actingAs($this->user)->post(route('cars.service_entries.store', $this->userCar), []);
        $response->assertSessionHasErrors(['date', 'kilometers', 'service_name', 'cost']);
    }

    /** @test */
    public function update_service_entry_requires_valid_data()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->userCar->id, 'user_id' => $this->user->id]);
        // Update route is shallow
        $response = $this->actingAs($this->user)->put(route('service_entries.update', $serviceEntry), []);
        $response->assertSessionHasErrors(['date', 'kilometers', 'service_name', 'cost']);
    }
}
