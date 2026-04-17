<?php

namespace App\Filament\Resources\OfficeSupplyItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OfficeSupplyItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('office_supply_category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('office_supply_name')
                    ->required(),
                Textarea::make('office_supply_description')
                    ->columnSpanFull(),
                TextInput::make('office_supply_price')
                    ->numeric()
                    ->prefix('$'),
                FileUpload::make('office_supply_image')
                    ->image(),
            ]);
    }
}
