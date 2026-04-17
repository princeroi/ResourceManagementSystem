<?php

namespace App\Filament\Resources\OfficeSupplyRequestItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OfficeSupplyRequestItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('office_supply_request_id')
                    ->required()
                    ->numeric(),
                Select::make('item_id')
                    ->relationship('item', 'id')
                    ->required(),
                TextInput::make('item_variant_id')
                    ->required()
                    ->numeric(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
            ]);
    }
}
