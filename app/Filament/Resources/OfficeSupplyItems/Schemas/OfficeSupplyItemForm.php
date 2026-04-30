<?php

namespace App\Filament\Resources\OfficeSupplyItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OfficeSupplyItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('office_supply_category_id')
                    ->relationship('category', 'office_supply_category_name')
                    ->required(),
                TextInput::make('office_supply_name')
                    ->unique(
                        table: 'office_supply_items',
                        column: 'office_supply_name',
                        ignoreRecord: true,
                    )
                    ->required(),
                Textarea::make('office_supply_description')
                    ->columnSpanFull(),
                TextInput::make('office_supply_price')
                    ->numeric()
                    ->prefix('$'),
                FileUpload::make('office_supply_image')
                    ->image()
                    ->directory('office-supply-items')
                    ->nullable(),
                Repeater::make('office_supply_variants')
                    ->relationship('variants')
                    ->schema([
                        Hidden::make('id'),

                        TextInput::make('office_supply_variant')
                            ->required(),

                        TextInput::make('office_supply_quantity')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->collapsible(),
            ]);
    }
}