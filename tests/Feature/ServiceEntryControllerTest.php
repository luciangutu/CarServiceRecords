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

    #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_access_service_entry_routes()
    {
        $this->get(route('service-entries.index', ['car' => $this->userCar->id]))->assertRedirect(route('login'));
        $this->get(route('service-entries.create', ['car' => $this->userCar->id]))->assertRedirect(route('login'));
        $this->post(route('service-entries.store', ['car' => $this->userCar->id]))->assertRedirect(route('login'));
        // Dummy service entry ID for routes that require one
        // For shallow routes, 'car' parameter is not needed for show, edit, update, destroy
        $this->get(route('service-entries.show', ['service_entry' => 1]))->assertRedirect(route('login'));
        $this->get(route('service-entries.edit', ['service_entry' => 1]))->assertRedirect(route('login'));
        $this->put(route('service-entries.update', ['service_entry' => 1]))->assertRedirect(route('login'));
        $this->delete(route('service-entries.destroy', ['service_entry' => 1]))->assertRedirect(route('login'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_service_entries_index_for_their_car()
    {
        ServiceEntry::factory()->count(3)->create(['car_id' => $this->userCar->id, 'user_id' => $this->user->id]);
        ServiceEntry::factory()->count(2)->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);

        $response = $this->actingAs($this->user)->get(route('service-entries.index') . '?car_id=' . $this->userCar->id);

        $response->assertOk();
        $response->assertViewIs('service_entries.index');
        $response->assertViewHas('cars', function ($cars) {
            return $cars->contains($this->userCar);
        });
        $response->assertViewHas('entries', function ($entries) {
            return $entries->count() === 3;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_view_service_entries_index_for_another_users_car()
    {
        ServiceEntry::factory()->count(3)->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);

        $response = $this->actingAs($this->user)->get(route('service-entries.index') . '?car_id=' . $this->anotherUserCar->id);

        $response->assertForbidden(); // 403
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_create_service_entry_form_for_their_car()
    {
        $response = $this->actingAs($this->user)->get(route('service-entries.create'));
        $response->assertOk();
        $response->assertViewIs('service_entries.create');
        $response->assertViewHas('cars', function ($cars) {
            return $cars->contains($this->userCar);
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_store_a_new_service_entry_for_their_car()
    {
        $serviceEntryData = [
            'date' => now()->format('Y-m-d'),
            'kilometers' => 10000,
            'car_id' => $this->userCar->id,
            'service_name' => 'Oil Change',
            'service_action' => 'Changed oil and filter.',
            'parts_replaced' => 'Oil filter, Engine oil',
            'cost' => 50.99,
        ];

        $response = $this->actingAs($this->user)->post(route('service-entries.store'), $serviceEntryData);

        $response->assertRedirect(route('service-entries.index'));

        $this->assertDatabaseHas('service_entries', [
            'car_id' => $this->userCar->id,
            'user_id' => $this->user->id,
            'service_name' => 'Oil Change',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_store_service_entry_for_another_users_car()
    {
        $serviceEntryData = ServiceEntry::factory()->make()->toArray();
        $response = $this->actingAs($this->user)->post(route('service-entries.store', $this->anotherUserCar), $serviceEntryData);
        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_view_service_entry_for_their_car()
    {
        // Creeaza un service entry pentru masina userului autentificat
        $serviceEntry = ServiceEntry::factory()->create([
            'car_id' => $this->userCar->id,
            'user_id' => $this->user->id,
        ]);

        // Acceseaza pagina vizualizarii service entry-ului
        $response = $this->actingAs($this->user)->get(route('service-entries.show', $serviceEntry->id));

        // Verifica status 200 OK
        $response->assertOk();

        $response->assertViewHas('serviceEntry', function ($entry) use ($serviceEntry) {
            return $entry->id === $serviceEntry->id;
        });
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_view_service_entry_for_another_users_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        // Show route is shallow
        $response = $this->actingAs($this->user)->get(route('service-entries.show', $serviceEntry));
        $response->assertForbidden();
    }

     #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_view_service_entry_of_their_car_if_entry_belongs_to_another_car()
    {
        // This scenario should ideally not happen due to route structure, but good to test policy
        $serviceEntryForAnotherCar = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        // Show route is shallow
        $response = $this->actingAs($this->user)->get(route('service-entries.show', $serviceEntryForAnotherCar));
        $response->assertForbidden(); // Or NotFound, depending on policy implementation for mismatched car and entry
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_authenticated_user_can_view_edit_service_entry_form_for_their_car()
    {
        $serviceEntry = ServiceEntry::factory()->create([
            'car_id' => $this->userCar->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('service-entries.edit', $serviceEntry));

        $response->assertOk();
        $response->assertViewIs('service_entries.edit');
        $response->assertViewHas('serviceEntry', function ($entry) use ($serviceEntry) {
            return $entry->id === $serviceEntry->id;
        });
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_view_edit_form_for_service_entry_on_another_users_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        // Edit route is shallow
        $response = $this->actingAs($this->user)->get(route('service-entries.edit', $serviceEntry));
        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_update_service_entry_for_their_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->userCar->id, 'user_id' => $this->user->id]);
        $updateData = [
            'date' => now()->subDay()->format('Y-m-d'),
            'kilometers' => 12000,
            'car_id' => $this->userCar->id,
            'service_name' => 'Tyre Rotation',
            'service_action' => 'Rotated tyres.',
            'parts_replaced' => 'None',
            'cost' => 25.00,
        ];

        $response = $this->actingAs($this->user)->put(route('service-entries.update', $serviceEntry), $updateData);

        $response->assertRedirect(route('service-entries.index'));

        $this->assertDatabaseHas('service_entries', [
            'id' => $serviceEntry->id,
            'service_name' => 'Tyre Rotation',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_update_service_entry_for_another_users_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        $updateData = ServiceEntry::factory()->make()->toArray();
        // Update route is shallow
        $response = $this->actingAs($this->user)->put(route('service-entries.update', $serviceEntry), $updateData);
        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_authenticated_user_can_delete_service_entry_for_their_car()
    {
        $serviceEntry = ServiceEntry::factory()->create([
            'car_id' => $this->userCar->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('service-entries.destroy', $serviceEntry));

        $response->assertRedirect(route('service-entries.index'));
        $this->assertDatabaseMissing('service_entries', ['id' => $serviceEntry->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_cannot_delete_service_entry_for_another_users_car()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->anotherUserCar->id, 'user_id' => $this->anotherUser->id]);
        // Destroy route is shallow
        $response = $this->actingAs($this->user)->delete(route('service-entries.destroy', $serviceEntry));
        $response->assertForbidden();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function store_service_entry_requires_valid_data()
    {
        $response = $this->actingAs($this->user)->post(route('service-entries.store'), []);
        $response->assertSessionHasErrors(['date', 'kilometers', 'service_name']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_service_entry_requires_valid_data()
    {
        $serviceEntry = ServiceEntry::factory()->create(['car_id' => $this->userCar->id, 'user_id' => $this->user->id]);
        // Update route is shallow
        $response = $this->actingAs($this->user)->put(route('service-entries.update', $serviceEntry), []);
        $response->assertSessionHasErrors(['date', 'kilometers', 'service_name']);
    }
}