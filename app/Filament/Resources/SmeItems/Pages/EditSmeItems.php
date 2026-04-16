<?php

namespace App\Filament\Resources\SmeItems\Pages;

use App\Filament\Resources\SmeItems\SmeItemsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditSmeItems extends EditRecord
{
    protected static string $resource = SmeItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
