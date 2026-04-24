<?php

namespace App\Filament\Resources\SmeCategories;

use App\Filament\Resources\SmeCategories\Pages\CreateSmeCategory;
use App\Filament\Resources\SmeCategories\Pages\EditSmeCategory;
use App\Filament\Resources\SmeCategories\Pages\ListSmeCategories;
use App\Filament\Resources\SmeCategories\Schemas\SmeCategoryForm;
use App\Filament\Resources\SmeCategories\Tables\SmeCategoriesTable;
use App\Models\SmeCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SmeCategoryResource extends Resource
{
    protected static ?string $model = SmeCategory::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-bookmark';

    public static function getNavigationGroup(): ?string
    {
        return 'Item Setup';
    }

    public static function form(Schema $schema): Schema
    {
        return SmeCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmeCategoriesTable::configure($table);
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
            'index' => ListSmeCategories::route('/'),
        ];
    }
}
