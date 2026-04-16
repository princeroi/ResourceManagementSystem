<?php

namespace App\Filament\Resources\ForDeliveryReceipts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ForDeliveryReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('endorse_by')
                    ->required(),

                DatePicker::make('endorse_date'),

                Repeater::make('item_summary')
                    ->label('Item Summary')
                    ->schema([
                        TextInput::make('item')
                            ->label('Item')
                            ->required()
                            ->placeholder('Enter item description'),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->placeholder('e.g. 1'),
                        TextInput::make('remarks')
                            ->label('Remarks')
                            ->placeholder('Optional remarks'),
                    ])
                    ->columns(3)
                    ->addActionLabel('Add Item')
                    ->required()
                    ->minItems(1)
                    ->defaultItems(1)
                    ->collapsible()
                    ->cloneable(),

                TextInput::make('dr_number')
                    ->label('DR Number')
                    ->placeholder('Enter DR Number (auto-sets status to Done)')
                    ->maxLength(255)
                    ->dehydrated(false),

                Select::make('status')
                    ->options(['pending' => 'Pending', 'done' => 'Done', 'cancelled' => 'Cancelled'])
                    ->default('done')
                    ->hidden()
                    ->required(),

                DatePicker::make('done_date')
                    ->hidden()
                    ->default(now()),

                TextInput::make('remarks'),
            ]);
    }
}