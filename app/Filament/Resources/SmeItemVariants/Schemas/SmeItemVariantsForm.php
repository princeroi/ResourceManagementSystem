<?php

namespace App\Filament\Resources\SmeItemVariants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SmeItemVariantsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sme_item_id')
                    ->required()
                    ->numeric(),
                TextInput::make('sme_item_size')
                    ->required(),
                TextInput::make('sme_item_quantity')
                    ->required()
                    ->numeric(),
            ]);
    }
}
