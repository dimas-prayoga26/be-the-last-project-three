<?php

namespace App\Filament\Resources\CommunityEvents\Pages;

use App\Filament\Resources\CommunityEvents\CommunityEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCommunityEvents extends ManageRecords
{
    protected static string $resource = CommunityEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
