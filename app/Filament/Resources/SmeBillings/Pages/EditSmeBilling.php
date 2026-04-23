<?php

namespace App\Filament\Resources\SmeBillings\Pages;

use App\Filament\Resources\SmeBillings\SmeBillingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSmeBilling extends EditRecord
{
    protected static string $resource = SmeBillingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
