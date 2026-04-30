<?php

namespace App\Filament\Resources\SmeRestocks;

use App\Filament\Resources\SmeRestocks\Pages\ListSmeRestocks;
use App\Filament\Resources\SmeRestocks\Schemas\SmeRestocksForm;
use App\Filament\Resources\SmeRestocks\Tables\SmeRestocksTable;
use App\Models\SmeRestocks;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SmeRestocksResource extends Resource
{
    protected static ?string $model = SmeRestocks::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-cart-plus';

    protected static ?string $navigationLabel = 'SME Restocks';

    public static function getNavigationGroup(): ?string
    {
        return 'Stock & Inventory';
    }

    public static function form(Schema $schema): Schema
    {
        return SmeRestocksForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmeRestocksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmeRestocks::route('/'),
        ];
    }
}