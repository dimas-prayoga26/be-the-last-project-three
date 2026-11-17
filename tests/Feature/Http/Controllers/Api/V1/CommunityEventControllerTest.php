<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Enums\EventType;
use App\Models\CommunityEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityEventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_upcoming_published_events_soonest_first_by_default(): void
    {
        $later = CommunityEvent::factory()->upcoming(30)->create();
        $sooner = CommunityEvent::factory()->upcoming(5)->create();
        CommunityEvent::factory()->upcoming(10)->unpublished()->create();
        CommunityEvent::factory()->past()->create();

        $response = $this->getJson(route('api.v1.events.index'));

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.slug', $sooner->slug)
            ->assertJsonPath('data.0.is_past', false)
            ->assertJsonPath('data.1.slug', $later->slug)
            ->assertJsonPath('meta.total', 2);
    }

    public function test_lists_past_events_newest_first(): void
    {
        $older = CommunityEvent::factory()->past(60)->create();
        $newer = CommunityEvent::factory()->past(7)->create();
        CommunityEvent::factory()->past(3)->unpublished()->create();
        CommunityEvent::factory()->upcoming()->create();

        $response = $this->getJson(route('api.v1.events.index', ['when' => 'past']));

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.slug', $newer->slug)
            ->assertJsonPath('data.0.is_past', true)
            ->assertJsonPath('data.1.slug', $older->slug);
    }

    public function test_an_event_that_is_running_now_counts_as_upcoming(): void
    {
        $running = CommunityEvent::factory()->create([
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(5),
        ]);

        $upcoming = $this->getJson(route('api.v1.events.index'));
        $past = $this->getJson(route('api.v1.events.index', ['when' => 'past']));

        $upcoming->assertOk()->assertJsonPath('data.0.slug', $running->slug);
        $past->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_returns_422_for_an_unknown_time_filter(): void
    {
        $response = $this->getJson(route('api.v1.events.index', ['when' => 'tomorrow']));

        $response->assertUnprocessable()->assertJsonValidationErrors(['when']);
    }

    public function test_shows_a_published_event_in_the_requested_language(): void
    {
        $event = CommunityEvent::factory()->upcoming()->create([
            'title' => ['en' => 'Material Talk', 'id' => 'Bincang Material'],
            'type' => EventType::Talk,
            'city' => 'Jakarta',
        ]);

        $response = $this->getJson(route('api.v1.events.show', $event), ['Accept-Language' => 'id']);

        $response->assertOk()
            ->assertJsonPath('data.slug', 'material-talk')
            ->assertJsonPath('data.title', 'Bincang Material')
            ->assertJsonPath('data.type', 'talk')
            ->assertJsonPath('data.city', 'Jakarta');
    }

    public function test_returns_404_for_an_unpublished_event(): void
    {
        $event = CommunityEvent::factory()->unpublished()->create();

        $this->getJson(route('api.v1.events.show', $event))->assertNotFound();
    }
}
