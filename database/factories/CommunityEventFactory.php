<?php

namespace Database\Factories;

use App\Enums\EventType;
use App\Models\CommunityEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CommunityEvent>
 */
class CommunityEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(3, true));
        $startsAt = now()->addDays(fake()->numberBetween(3, 90))->setTime(19, 0);

        return [
            'title' => ['en' => $title, 'id' => $title],
            'type' => fake()->randomElement(EventType::cases()),
            'summary' => ['en' => fake()->sentence(), 'id' => fake()->sentence()],
            'description' => ['en' => fake()->paragraphs(2, true), 'id' => fake()->paragraphs(2, true)],
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHours(2),
            'venue' => fake()->company(),
            'city' => fake()->city(),
            'registration_url' => fake()->url(),
            'is_published' => true,
        ];
    }

    /**
     * Indicate that the event is still to come.
     */
    public function upcoming(int $daysFromNow = 14): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->addDays($daysFromNow)->setTime(19, 0),
            'ends_at' => now()->addDays($daysFromNow)->setTime(21, 0),
        ]);
    }

    /**
     * Indicate that the event is over.
     */
    public function past(int $daysAgo = 30): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subDays($daysAgo)->setTime(19, 0),
            'ends_at' => now()->subDays($daysAgo)->setTime(21, 0),
        ]);
    }

    /**
     * Indicate that the event is hidden from the public site.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
