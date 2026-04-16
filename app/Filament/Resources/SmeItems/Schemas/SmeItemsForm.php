<?php

namespace App\Filament\Resources\SmeItems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use App\Rules\UniqueVariantSize;

class SmeItemsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sme_category_id')
                    ->relationship('category', 'sme_category_name')
                    ->required(),
                TextInput::make('sme_item_name')
                    ->unique(
                        table: 'sme_items',
                        column: 'sme_item_name',
                        ignoreRecord: true,
                    )
                    ->required(),
                TextInput::make('sme_item_brand')
                    ->required(),
                Textarea::make('sme_item_description')
                    ->columnSpanFull(),
                TextInput::make('sme_item_price')
                    ->required()
                    ->numeric(),
                FileUpload::make('sme_item_image')
                    ->image()
                    ->directory('sme-items')
                    ->nullable(),
                Repeater::make('sme_item_variants')
                    ->relationship('itemVariant')
                    ->schema([
                        // Remove Hidden::make('id') entirely

                        TextInput::make('sme_item_size')
                            ->rules([
                                fn($get, $record) => new UniqueVariantSize(
                                    itemName: $get('../../sme_item_name'),
                                    allSizes: collect($get('../../sme_item_variants'))
                                        ->pluck('sme_item_size')
                                        ->filter()
                                        ->values()
                                        ->toArray(),
                                    currentVariantId: $record?->id, // use $record instead of $get('id')
                                )
                            ])
                            ->required(),

                        TextInput::make('sme_item_quantity')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->columnSpan('full')
                    ->collapsible()
            ]);
    }
}