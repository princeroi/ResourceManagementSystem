<?php

namespace App\Filament\Resources\SmeBillings\Pages;

use App\Filament\Resources\SmeBillings\SmeBillingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSmeBillings extends ListRecords
{
    protected static string $resource = SmeBillingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
