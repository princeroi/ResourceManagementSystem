<?php

namespace App\Filament\Resources\OfficeSupplyItemVariants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OfficeSupplyItemVariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('office_supply_item_id')
                    ->required()
                    ->numeric(),
                TextInput::make('office_supply_variant')
                    ->required(),
                TextInput::make('office_supply_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
