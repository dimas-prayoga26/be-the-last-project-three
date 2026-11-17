<?php

namespace App\Http\Resources\Api\V1;

use App\Models\CommunityEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A talk, workshop, exhibition or meetup of the collective.
 *
 * @mixin CommunityEvent
 */
class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cover = $this->getFirstMedia('cover');

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'type' => $this->type->value,
            'summary' => $this->summary,
            'description' => $this->description,
            'starts_at' => $this->starts_at->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'is_past' => $this->isPast(),
            'venue' => $this->venue,
            'city' => $this->city,
            'registration_url' => $this->registration_url,
            'cover' => $cover ? MediaResource::make($cover) : null,
        ];
    }
}
