<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication(): void
    {
        $this->getJson('/api/users?search=ab')->assertUnauthorized();
    }

    public function test_it_finds_people_by_name_or_email(): void
    {
        User::factory()->create(['name' => 'Sara Mansour', 'email' => 'sara@example.com']);
        User::factory()->create(['name' => 'Omar Khalil', 'email' => 'omar@example.com']);
        Sanctum::actingAs(User::factory()->create(['name' => 'Layla']));

        $names = fn (string $term) => collect($this->getJson('/api/users?search='.$term)->assertOk()->json('data'))->pluck('name')->all();

        $this->assertSame(['Sara Mansour'], $names('sar'));
        $this->assertSame(['Omar Khalil'], $names('omar@exam'));
    }

    public function test_it_exposes_only_what_is_needed_to_pick_a_person(): void
    {
        User::factory()->create(['name' => 'Sara Mansour', 'google_id' => 'g-secret-123']);
        Sanctum::actingAs(User::factory()->create());

        $person = $this->getJson('/api/users?search=Sara')->assertOk()->json('data.0');

        $this->assertSame(['email', 'id', 'name'], collect($person)->keys()->sort()->values()->all());
        $this->assertStringNotContainsString('g-secret-123', json_encode($person));
    }

    public function test_it_needs_a_real_search_term_instead_of_listing_everyone(): void
    {
        User::factory()->count(5)->create();
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/users')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/users?search=')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/users?search=a')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_it_caps_the_results_and_leaves_out_the_requester(): void
    {
        User::factory()->count(15)->create(['email' => fn () => 'match'.fake()->unique()->numberBetween(1, 999).'@example.com']);
        $me = User::factory()->create(['name' => 'Match Me', 'email' => 'matchme@example.com']);
        Sanctum::actingAs($me);

        $results = $this->getJson('/api/users?search=match')->assertOk()->json('data');

        $this->assertCount(10, $results);
        $this->assertNotContains($me->id, collect($results)->pluck('id')->all());
    }
}
