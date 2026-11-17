<?php

namespace App\Models;

use App\Enums\EventType;
use Database\Factories\CommunityEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

/**
 * A talk, workshop, exhibition or meetup organised by the collective.
 *
 * Named CommunityEvent so it never clashes with the Event facade; the site and the admin call it "event".
 */
#[Fillable([
    'title', 'slug', 'type', 'summary', 'description', 'starts_at', 'ends_at',
    'venue', 'city', 'registration_url', 'is_published',
])]
class CommunityEvent extends Model implements HasMedia
{
    /** @use HasFactory<CommunityEventFactory> */
    use HasFactory, HasSlug, HasTranslations, InteractsWithMedia;

    /**
     * @var list<string>
     */
    public array $translatable = ['title', 'summary', 'description'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => EventType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (CommunityEvent $event): string => $event->getTranslation('title', 'en'))
            ->saveSlugsTo('slug')
            ->preventOverwrite()
            ->doNotGenerateSlugsOnUpdate();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('card')->fit(Fit::Crop, 800, 1000)->format('webp')->quality(82);
        $this->addMediaConversion('thumb')->fit(Fit::Max, 640, 640)->format('webp')->quality(80);
        $this->addMediaConversion('large')->fit(Fit::Max, 2000, 2000)->format('webp')->quality(85);
    }

    /**
     * Whether the event is over: its end time (or start time, without one) has passed.
     */
    public function isPast(): bool
    {
        return ($this->ends_at ?? $this->starts_at)->isPast();
    }

    /**
     * @param  Builder<CommunityEvent>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * Events still to come or running now, soonest first.
     *
     * @param  Builder<CommunityEvent>  $query
     */
    #[Scope]
    protected function upcoming(Builder $query): void
    {
        $query->where(fn (Builder $when) => $when
            ->where('starts_at', '>=', now())
            ->orWhere('ends_at', '>=', now()))
            ->orderBy('starts_at');
    }

    /**
     * Events that are over, newest first.
     *
     * @param  Builder<CommunityEvent>  $query
     */
    #[Scope]
    protected function past(Builder $query): void
    {
        $query->where('starts_at', '<', now())
            ->where(fn (Builder $when) => $when->whereNull('ends_at')->orWhere('ends_at', '<', now()))
            ->orderByDesc('starts_at');
    }
}
