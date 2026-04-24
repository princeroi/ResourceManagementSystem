<?php

namespace App\Filament\Resources\SmeItemVariants;

use App\Filament\Resources\SmeItemVariants\Pages\CreateSmeItemVariants;
use App\Filament\Resources\SmeItemVariants\Pages\EditSmeItemVariants;
use App\Filament\Resources\SmeItemVariants\Pages\ListSmeItemVariants;
use App\Filament\Resources\SmeItemVariants\Schemas\SmeItemVariantsForm;
use App\Filament\Resources\SmeItemVariants\Tables\SmeItemVariantsTable;
use App\Models\SmeItemVariants;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SmeItemVariantsResource extends Resource
{
    protected static ?string $model = SmeItemVariants::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-chart-line';

    protected static ?string $navigationLabel = 'SME Stock';

    public static function getNavigationGroup(): ?string
    {
        return 'Stock & Inventory';
    }

    public static function form(Schema $schema): Schema
    {
        return SmeItemVariantsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmeItemVariantsTable::configure($table);
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
            'index' => ListSmeItemVariants::route('/'),
            'create' => CreateSmeItemVariants::route('/create'),
            'edit' => EditSmeItemVariants::route('/{record}/edit'),
        ];
    }
}
