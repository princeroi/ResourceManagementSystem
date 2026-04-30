<?php

namespace App\Filament\Resources\ReturnOfficeSupplyItems;

use App\Filament\Resources\ReturnOfficeSupplyItems\Pages\ListReturnOfficeSupplyItems;
use App\Filament\Resources\ReturnOfficeSupplyItems\Schemas\ReturnOfficeSupplyItemsForm;
use App\Filament\Resources\ReturnOfficeSupplyItems\Tables\ReturnOfficeSupplyItemsTable;
use App\Models\ReturnOfficeSupplyItems;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReturnOfficeSupplyItemsResource extends Resource
{
    protected static ?string $model = ReturnOfficeSupplyItems::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowUturnLeft;

    protected static ?string $navigationLabel = 'Return Office Supply';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Distributions';
    }

    public static function form(Schema $schema): Schema
    {
        return ReturnOfficeSupplyItemsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReturnOfficeSupplyItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReturnOfficeSupplyItems::route('/'),
        ];
    }
}