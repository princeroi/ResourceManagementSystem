<?php

namespace App\Filament\Resources\SmeItems\Pages;

use App\Filament\Resources\SmeItems\SmeItemsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSmeItems extends ListRecords
{
    protected static string $resource = SmeItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
