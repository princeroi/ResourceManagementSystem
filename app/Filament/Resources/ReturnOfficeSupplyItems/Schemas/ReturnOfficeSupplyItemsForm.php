<?php

namespace App\Filament\Resources\ReturnOfficeSupplyItems\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use App\Models\OfficeSupplyItem;
use App\Models\OfficeSupplyItemVariant;

class ReturnOfficeSupplyItemsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Who ───────────────────────────────────────────────────
                TextInput::make('returned_by')
                    ->label('Returned By')
                    ->required(),

                TextInput::make('received_by')
                    ->label('Received By')
                    ->required(),

                // ── Status ────────────────────────────────────────────────
                Select::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'returned' => 'Returned',
                    ])
                    ->required()
                    ->live()
                    ->default('pending')
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if ($state === 'pending') {
                            $set('pending_at',  now()->toDateString());
                            $set('returned_at', null);
                        }
                        if ($state === 'returned') {
                            $set('returned_at', now()->toDateString());
                            $set('pending_at',  null);
                        }

                        $lines = $get('returnOfficeSupplyItemLine') ?? [];
                        foreach ($lines as $key => $line) {
                            $qty = (int) ($line['quantity'] ?? 0);
                            if ($state === 'returned') {
                                $set("returnOfficeSupplyItemLine.{$key}.returned_quantity",  $qty);
                                $set("returnOfficeSupplyItemLine.{$key}.remaining_quantity", 0);
                            } else {
                                $set("returnOfficeSupplyItemLine.{$key}.returned_quantity",  0);
                                $set("returnOfficeSupplyItemLine.{$key}.remaining_quantity", $qty);
                            }
                        }
                    }),

                DatePicker::make('pending_at')
                    ->label('Pending Date')
                    ->default(now()->toDateString())
                    ->visible(fn ($get) => $get('status') === 'pending'),

                DatePicker::make('returned_at')
                    ->label('Returned Date')
                    ->visible(fn ($get) => $get('status') === 'returned'),

                // ── Notes ─────────────────────────────────────────────────
                Textarea::make('notes')
                    ->label('Notes')
                    ->nullable()
                    ->columnSpanFull(),

                // ── Line Items ────────────────────────────────────────────
                Repeater::make('returnOfficeSupplyItemLine')
                    ->label('Items to Return')
                    ->relationship('returnOfficeSupplyItemLine')
                    ->addActionLabel('+ Add Item')
                    ->minItems(1)
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([

                        TextInput::make('employee_name')
                            ->label('Employee Name')
                            ->nullable()
                            ->columnSpanFull(),

                        Select::make('office_supply_item_id')
                            ->label('Item')
                            ->options(OfficeSupplyItem::pluck('office_supply_name', 'id'))
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                $set('office_supply_item_variant_id', null);
                            }),

                        Select::make('office_supply_item_variant_id')
                            ->label('Variant')
                            ->options(function (callable $get) {
                                $itemId = $get('office_supply_item_id');
                                if (!$itemId) return [];
                                return OfficeSupplyItemVariant::where('office_supply_item_id', $itemId)
                                    ->pluck('office_supply_variant', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->live(),

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $qty    = (int) $state;
                                $status = $get('../../status') ?? 'pending';
                                if ($status === 'returned') {
                                    $set('returned_quantity',  $qty);
                                    $set('remaining_quantity', 0);
                                } else {
                                    $set('returned_quantity',  0);
                                    $set('remaining_quantity', $qty);
                                }
                            }),

                        TextInput::make('returned_quantity')
                            ->label('Returned Qty')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(true),

                        TextInput::make('remaining_quantity')
                            ->label('Remaining Qty')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(true),

                        Select::make('condition')
                            ->label('Condition')
                            ->options([
                                'good'      => 'Good',
                                'damaged'   => 'Damaged',
                                'defective' => 'Defective',
                            ])
                            ->default('good')
                            ->required(),

                        Select::make('reason')
                            ->label('Reason')
                            ->options([
                                'resignation'   => 'Resignation',
                                'replacement'   => 'Replacement',
                                'damaged'       => 'Damaged / Defective',
                                'end_of_use'    => 'End of Use',
                                'transfer'      => 'Transfer',
                                'other'         => 'Other',
                            ])
                            ->nullable(),

                        // ── Stock flag ────────────────────────────────────
                        Toggle::make('add_to_stock')
                            ->label('Add to Stock')
                            ->helperText('If ON, accepted quantity will be added back to inventory.')
                            ->default(true)
                            ->live()
                            ->columnSpanFull(),

                        Placeholder::make('stock_hint')
                            ->label('')
                            ->content(function (callable $get) {
                                $addToStock = $get('add_to_stock');
                                if ($addToStock) {
                                    return new HtmlString("
                                        <div style='padding:8px 12px;background:#f0fdf4;border:1px solid #bbf7d0;
                                            border-radius:8px;font-size:12px;color:#166534;display:flex;align-items:center;gap:8px;'>
                                            <span style='font-size:14px;'>✅</span>
                                            <span><strong>Stock will be updated</strong> — accepted quantity will be added to inventory when processed.</span>
                                        </div>
                                    ");
                                }
                                return new HtmlString("
                                    <div style='padding:8px 12px;background:#fef9c3;border:1px solid #fde68a;
                                        border-radius:8px;font-size:12px;color:#854d0e;display:flex;align-items:center;gap:8px;'>
                                        <span style='font-size:14px;'>⚠️</span>
                                        <span><strong>Stock will NOT be updated</strong> — this return is for record purposes only (e.g. damaged/disposal).</span>
                                    </div>
                                ");
                            })
                            ->columnSpanFull(),

                        TextInput::make('remarks')
                            ->label('Remarks')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}