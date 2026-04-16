<?php

namespace App\Filament\Resources\SmeItemVariants\Pages;

use App\Filament\Resources\SmeItemVariants\SmeItemVariantsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSmeItemVariants extends ListRecords
{
    protected static string $resource = SmeItemVariantsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
