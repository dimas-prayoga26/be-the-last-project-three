<?php

namespace Tests\Feature\Policies;

use App\Models\CommunityEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityEventPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_events(): void
    {
        $admin = User::factory()->admin()->create();
        $event = CommunityEvent::factory()->create();

        $this->assertTrue($admin->can('viewAny', CommunityEvent::class));
        $this->assertTrue($admin->can('create', CommunityEvent::class));
        $this->assertTrue($admin->can('update', $event));
        $this->assertTrue($admin->can('delete', $event));
        $this->assertTrue($admin->can('deleteAny', CommunityEvent::class));
    }

    public function test_designer_cannot_manage_events(): void
    {
        $user = User::factory()->designer()->create();
        $event = CommunityEvent::factory()->create();

        $this->assertFalse($user->can('viewAny', CommunityEvent::class));
        $this->assertFalse($user->can('create', CommunityEvent::class));
        $this->assertFalse($user->can('update', $event));
        $this->assertFalse($user->can('delete', $event));
    }
}
