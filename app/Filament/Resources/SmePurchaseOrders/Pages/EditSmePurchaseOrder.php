<?php

namespace App\Filament\Resources\SmePurchaseOrders\Pages;

use App\Filament\Resources\SmePurchaseOrders\SmePurchaseOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSmePurchaseOrder extends EditRecord
{
    protected static string $resource = SmePurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
