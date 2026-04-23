<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use App\Models\Asset;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('asset_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('property_tag')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(function () {
                        $year = now()->year;

                        $last = Asset::where('property_tag', 'like', 'SSI%')
                            ->orderByRaw('CAST(SUBSTRING(property_tag, 8) AS UNSIGNED) DESC')
                            ->value('property_tag');

                        $nextSequence = 1;

                        if ($last) {
                            $lastSequence = (int) substr($last, 7); // skip "SSI2026"
                            $nextSequence = $lastSequence + 1;
                        }

                        return "SSI{$year}" . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
                    }),

                TextInput::make('name')
                    ->required(),

                TextInput::make('brand'),

                TextInput::make('model'),

                TextInput::make('serial_number')
                    ->unique(ignoreRecord: true),

                TextInput::make('specifications')
                    ->columnSpanFull(),

                DatePicker::make('acquisition_date')
                    ->default(now())
                    ->required(),

                TextInput::make('acquisition_cost')
                    ->numeric()
                    ->prefix('₱'),

                TextInput::make('supplier'),

                TextInput::make('purchase_order_number'),

                DatePicker::make('warranty_expiry_date'),

                TextInput::make('useful_life_years')
                    ->numeric()
                    ->suffix('years'),

                TextInput::make('salvage_value')
                    ->numeric()
                    ->prefix('₱'),

                TextInput::make('location'),

                Select::make('condition')
                    ->options([
                        'new'        => 'New',
                        'good'       => 'Good',
                        'fair'       => 'Fair',
                        'poor'       => 'Poor',
                        'for_repair' => 'For repair',
                        'condemned'  => 'Condemned',
                    ])
                    ->default('new')
                    ->required(),

                Select::make('status')
                    ->options([
                        'available'         => 'Available',
                        'assigned'          => 'Assigned',
                        'under_maintenance' => 'Under maintenance',
                        'disposed'          => 'Disposed',
                    ])
                    ->default('available')
                    ->required(),

                Select::make('lifecycle_stage')
                    ->options([
                        'active'      => 'Active',
                        'end_of_life' => 'End of life',
                        'disposed'    => 'Disposed',
                    ])
                    ->default('active')
                    ->required(),

                FileUpload::make('image')
                    ->image()
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}