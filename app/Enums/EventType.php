<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * The kinds of gatherings the collective organises.
 */
enum EventType: string implements HasLabel
{
    case Talk = 'talk';
    case Workshop = 'workshop';
    case Exhibition = 'exhibition';
    case Meetup = 'meetup';

    public function getLabel(): string
    {
        return match ($this) {
            self::Talk => 'Talk',
            self::Workshop => 'Workshop',
            self::Exhibition => 'Exhibition',
            self::Meetup => 'Meetup',
        };
    }
}
