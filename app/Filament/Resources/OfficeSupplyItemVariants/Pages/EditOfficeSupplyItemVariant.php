<?php

namespace App\Filament\Resources\OfficeSupplyItemVariants\Pages;

use App\Filament\Resources\OfficeSupplyItemVariants\OfficeSupplyItemVariantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOfficeSupplyItemVariant extends EditRecord
{
    protected static string $resource = OfficeSupplyItemVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
