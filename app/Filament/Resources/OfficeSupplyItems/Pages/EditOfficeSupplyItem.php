<?php

namespace App\Filament\Resources\OfficeSupplyItems\Pages;

use App\Filament\Resources\OfficeSupplyItems\OfficeSupplyItemResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditOfficeSupplyItem extends EditRecord
{
    protected static string $resource = OfficeSupplyItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
