<?php

namespace App\Filament\Resources\ReturnUniformItems\Pages;

use App\Filament\Resources\ReturnUniformItems\ReturnUniformItemsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReturnUniformItems extends EditRecord
{
    protected static string $resource = ReturnUniformItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
