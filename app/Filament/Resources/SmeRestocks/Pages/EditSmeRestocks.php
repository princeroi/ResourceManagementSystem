<?php

namespace App\Filament\Resources\SmeRestocks\Pages;

use App\Filament\Resources\SmeRestocks\SmeRestocksResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSmeRestocks extends EditRecord
{
    protected static string $resource = SmeRestocksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
