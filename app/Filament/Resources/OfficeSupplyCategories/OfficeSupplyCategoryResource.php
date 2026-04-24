<?php

namespace App\Filament\Resources\OfficeSupplyCategories;

use App\Filament\Resources\OfficeSupplyCategories\Pages\CreateOfficeSupplyCategory;
use App\Filament\Resources\OfficeSupplyCategories\Pages\EditOfficeSupplyCategory;
use App\Filament\Resources\OfficeSupplyCategories\Pages\ListOfficeSupplyCategories;
use App\Filament\Resources\OfficeSupplyCategories\Schemas\OfficeSupplyCategoryForm;
use App\Filament\Resources\OfficeSupplyCategories\Tables\OfficeSupplyCategoriesTable;
use App\Models\OfficeSupplyCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OfficeSupplyCategoryResource extends Resource
{
    protected static ?string $model = OfficeSupplyCategory::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-bookmark';

    public static function getNavigationGroup(): ?string
    {
        return 'Item Setup';
    }

    public static function form(Schema $schema): Schema
    {
        return OfficeSupplyCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfficeSupplyCategoriesTable::configure($table);
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
            'index' => ListOfficeSupplyCategories::route('/'),
        ];
    }
}
