<?php

namespace App\Filament\Resources\SmeItemVariants\Pages;

use App\Filament\Resources\SmeItemVariants\SmeItemVariantsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSmeItemVariants extends EditRecord
{
    protected static string $resource = SmeItemVariantsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
