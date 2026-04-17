<?php

namespace App\Filament\Resources\OfficeSupplyRequestItems\Pages;

use App\Filament\Resources\OfficeSupplyRequestItems\OfficeSupplyRequestItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOfficeSupplyRequestItem extends EditRecord
{
    protected static string $resource = OfficeSupplyRequestItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
