<?php

namespace App\Filament\Resources\OfficeSupplyRestocks\Pages;

use App\Filament\Resources\OfficeSupplyRestocks\OfficeSupplyRestocksResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOfficeSupplyRestocks extends EditRecord
{
    protected static string $resource = OfficeSupplyRestocksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
