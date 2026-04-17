<?php

namespace App\Filament\Resources\OfficeSupplyCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OfficeSupplyCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('office_supply_category_name')
                    ->required(),
            ]);
    }
}
