<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
 
   }
    public function test_the_application_cars(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/'); // Act as an authenticated user

        // The home route '/' redirects to 'cars.index'
        // So, we assert a redirect and then can optionally follow it
        $response->assertStatus(302); 
        $response->assertRedirectToRoute('service-entries.index');

        // Optionally, follow the redirect and assert the final status
        // $response = $this->followRedirects($response);
        // $response->assertStatus(200); // Assuming cars.index returns 200 for an auth user
    }
}
