<?php

namespace App\Filament\Resources\OfficeSupplyRestocks;

use App\Filament\Resources\OfficeSupplyRestocks\Pages\ListOfficeSupplyRestocks;
use App\Filament\Resources\OfficeSupplyRestocks\Schemas\OfficeSupplyRestocksForm;
use App\Filament\Resources\OfficeSupplyRestocks\Tables\OfficeSupplyRestocksTable;
use App\Models\OfficeSupplyRestock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OfficeSupplyRestocksResource extends Resource
{
    protected static ?string $model = OfficeSupplyRestock::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-cart-plus';

    protected static ?string $navigationLabel = 'Office Supply Restocks';

    public static function getNavigationGroup(): ?string
    {
        return 'Stock & Inventory';
    }

    public static function form(Schema $schema): Schema
    {
        return OfficeSupplyRestocksForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfficeSupplyRestocksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOfficeSupplyRestocks::route('/'),
        ];
    }
}