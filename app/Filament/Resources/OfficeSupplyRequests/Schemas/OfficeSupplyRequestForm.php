<?php

namespace App\Filament\Resources\OfficeSupplyRequests\Schemas;

use App\Models\OfficeSupplyItem;
use App\Models\OfficeSupplyItemVariant;
use App\Models\OfficeSupplyRequest;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OfficeSupplyRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('request_number')
                    ->label('Request #')
                    ->content(fn($record) => $record?->request_number
                        ?? OfficeSupplyRequest::generateRequestNumber() . ' (preview)')
                    ->helperText('Auto-generated on save'),

                TextInput::make('requested_by')
                    ->required(),

                DatePicker::make('request_date')
                    ->required()
                    ->default(now()->timezone('Asia/Manila')),

                Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'approved'  => 'Approved',
                        'completed' => 'Completed',
                        'rejected'  => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),

                Textarea::make('note')
                    ->columnSpanFull(),

                Repeater::make('request_items')
                    ->relationship('items')
                    ->schema([
                        Hidden::make('id'),

                        Select::make('item_id')
                            ->label('Item')
                            ->options(OfficeSupplyItem::pluck('office_supply_name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn($set) => $set('item_variant_id', null)),

                        Select::make('item_variant_id')
                            ->label('Variant')
                            ->options(function ($get) {
                                $itemId = $get('item_id');
                                if (!$itemId) return [];

                                return OfficeSupplyItemVariant::where('office_supply_item_id', $itemId)
                                    ->get()
                                    ->mapWithKeys(fn($v) => [
                                        $v->id => "{$v->office_supply_variant} (Stock: {$v->office_supply_quantity})"
                                    ]);
                            })
                            ->required()
                            ->reactive(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1),
                    ])
                    ->columns(3)
                    ->columnSpan('full')
                    ->collapsible()
                    ->label('Requested Items'),
            ]);
    }
}