<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class ExampleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_renders_the_landing_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('welcome'));
    }

    public function test_authenticated_users_can_view_the_landing_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('welcome')
                ->where('auth.user.id', $user->id)
            );
    }
}
