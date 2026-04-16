<?php

namespace App\Filament\Resources\ReturnSmeItems\Pages;

use App\Filament\Resources\ReturnSmeItems\ReturnSmeItemsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReturnSmeItems extends EditRecord
{
    protected static string $resource = ReturnSmeItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
