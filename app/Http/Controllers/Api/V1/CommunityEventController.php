<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\EventResource;
use App\Models\CommunityEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommunityEventController extends Controller
{
    /**
     * Published events: upcoming ones soonest first (default) or past ones newest first.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'when' => ['nullable', 'in:upcoming,past'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:48'],
        ]);

        $query = CommunityEvent::query()->published()->with('media');

        if (($filters['when'] ?? 'upcoming') === 'past') {
            $query->past();
        } else {
            $query->upcoming();
        }

        $events = $query->orderBy('id')
            ->paginate($filters['per_page'] ?? 12)
            ->withQueryString();

        return EventResource::collection($events);
    }

    /**
     * A published event.
     */
    public function show(CommunityEvent $communityEvent): EventResource
    {
        abort_unless($communityEvent->is_published, 404);

        $communityEvent->load('media');

        return EventResource::make($communityEvent);
    }
}
