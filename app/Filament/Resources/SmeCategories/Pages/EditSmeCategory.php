<?php

namespace App\Filament\Resources\SmeCategories\Pages;

use App\Filament\Resources\SmeCategories\SmeCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSmeCategory extends EditRecord
{
    protected static string $resource = SmeCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
