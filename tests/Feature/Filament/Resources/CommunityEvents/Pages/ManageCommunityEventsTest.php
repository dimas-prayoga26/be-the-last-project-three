<?php

namespace Tests\Feature\Filament\Resources\CommunityEvents\Pages;

use App\Enums\EventType;
use App\Filament\Resources\CommunityEvents\Pages\ManageCommunityEvents;
use App\Models\CommunityEvent;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageCommunityEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_filters_upcoming_events(): void
    {
        $upcoming = CommunityEvent::factory()->upcoming()->create();
        $past = CommunityEvent::factory()->past()->create();
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(ManageCommunityEvents::class)
            ->assertCanSeeTableRecords([$upcoming, $past])
            ->filterTable('when', 'upcoming')
            ->assertCanSeeTableRecords([$upcoming])
            ->assertCanNotSeeTableRecords([$past]);
    }

    public function test_admin_creates_an_event_with_a_slug_from_the_english_title(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(ManageCommunityEvents::class)
            ->callAction(TestAction::make('create'), [
                'title' => ['en' => 'Clay Workshop', 'id' => 'Lokakarya Tanah Liat'],
                'type' => EventType::Workshop->value,
                'starts_at' => now()->addWeek()->setTime(10, 0)->toDateTimeString(),
                'city' => 'Yogyakarta',
                'is_published' => true,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('community_events', [
            'slug' => 'clay-workshop',
            'type' => 'workshop',
            'city' => 'Yogyakarta',
        ]);
    }

    public function test_designer_gets_403(): void
    {
        $this->actingAs(User::factory()->designer()->create());

        $response = $this->get(route('filament.admin.resources.community-events.index'));

        $response->assertForbidden();
    }
}
