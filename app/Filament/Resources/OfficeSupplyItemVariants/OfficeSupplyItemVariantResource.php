<?php

namespace App\Filament\Resources\OfficeSupplyItemVariants;

use App\Filament\Resources\OfficeSupplyItemVariants\Pages\CreateOfficeSupplyItemVariant;
use App\Filament\Resources\OfficeSupplyItemVariants\Pages\EditOfficeSupplyItemVariant;
use App\Filament\Resources\OfficeSupplyItemVariants\Pages\ListOfficeSupplyItemVariants;
use App\Filament\Resources\OfficeSupplyItemVariants\Schemas\OfficeSupplyItemVariantForm;
use App\Filament\Resources\OfficeSupplyItemVariants\Tables\OfficeSupplyItemVariantsTable;
use App\Models\OfficeSupplyItemVariant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OfficeSupplyItemVariantResource extends Resource
{
    protected static ?string $model = OfficeSupplyItemVariant::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-chart-line';

    protected static ?string $navigationLabel = 'Office Supply Stock';

    public static function getNavigationGroup(): ?string
    {
        return 'Stock & Inventory';
    }

    public static function form(Schema $schema): Schema
    {
        return OfficeSupplyItemVariantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfficeSupplyItemVariantsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOfficeSupplyItemVariants::route('/'),
            'create' => CreateOfficeSupplyItemVariant::route('/create'),
            'edit' => EditOfficeSupplyItemVariant::route('/{record}/edit'),
        ];
    }
}
