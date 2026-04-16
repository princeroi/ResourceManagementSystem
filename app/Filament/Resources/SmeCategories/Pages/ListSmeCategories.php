<?php

namespace App\Filament\Resources\SmeCategories\Pages;

use App\Filament\Resources\SmeCategories\SmeCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSmeCategories extends ListRecords
{
    protected static string $resource = SmeCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
