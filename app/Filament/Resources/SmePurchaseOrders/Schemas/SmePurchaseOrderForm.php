<?php

namespace App\Filament\Resources\SmePurchaseOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class SmePurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('site_id')
                    ->relationship('site', 'site_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),

                TextInput::make('po_number')
                    ->unique(ignoreRecord: true),

                DatePicker::make('po_date'),

                FileUpload::make('po_file_path')
                    ->label('PO file')
                    ->directory('purchase-orders/po')
                    ->acceptedFileTypes(['application/pdf', 'image/*']),

                Textarea::make('note')
                    ->columnSpanFull(),

                Repeater::make('purchaseOrderItems')
                    ->relationship('purchaseOrderItems')
                    ->label('Order items')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('sme_item_id')
                            ->label('Item')
                            ->relationship('smeItem', 'sme_item_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('sme_item_variant_id', null)),

                        Select::make('sme_item_variant_id')
                            ->label('Variant')
                            ->relationship('smeItemVariant', 'sme_item_size')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('quantity')
                            ->integer()
                            ->minValue(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $released = (int) $get('released_quantity');
                                $set('remaining_quantity', max(0, (int) $state - $released));
                            }),

                        TextInput::make('released_quantity')
                            ->integer()
                            ->default(0)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $quantity = (int) $get('quantity');
                                $set('remaining_quantity', max(0, $quantity - (int) $state));
                            }),

                        TextInput::make('remaining_quantity')
                            ->integer()
                            ->default(0)
                            ->minValue(0)
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->addActionLabel('Add item'),
            ]);
    }
}