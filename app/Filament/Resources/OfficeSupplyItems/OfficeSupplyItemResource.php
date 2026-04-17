<?php

namespace App\Filament\Resources\OfficeSupplyItems;

use App\Filament\Resources\OfficeSupplyItems\Pages\CreateOfficeSupplyItem;
use App\Filament\Resources\OfficeSupplyItems\Pages\EditOfficeSupplyItem;
use App\Filament\Resources\OfficeSupplyItems\Pages\ListOfficeSupplyItems;
use App\Filament\Resources\OfficeSupplyItems\Schemas\OfficeSupplyItemForm;
use App\Filament\Resources\OfficeSupplyItems\Tables\OfficeSupplyItemsTable;
use App\Models\OfficeSupplyItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfficeSupplyItemResource extends Resource
{
    protected static ?string $model = OfficeSupplyItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return OfficeSupplyItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfficeSupplyItemsTable::configure($table);
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
            'index' => ListOfficeSupplyItems::route('/'),
            'create' => CreateOfficeSupplyItem::route('/create'),
            'edit' => EditOfficeSupplyItem::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
