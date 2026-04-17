<?php

namespace App\Filament\Resources\OfficeSupplyRequestItems;

use App\Filament\Resources\OfficeSupplyRequestItems\Pages\CreateOfficeSupplyRequestItem;
use App\Filament\Resources\OfficeSupplyRequestItems\Pages\EditOfficeSupplyRequestItem;
use App\Filament\Resources\OfficeSupplyRequestItems\Pages\ListOfficeSupplyRequestItems;
use App\Filament\Resources\OfficeSupplyRequestItems\Schemas\OfficeSupplyRequestItemForm;
use App\Filament\Resources\OfficeSupplyRequestItems\Tables\OfficeSupplyRequestItemsTable;
use App\Models\OfficeSupplyRequestItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OfficeSupplyRequestItemResource extends Resource
{
    protected static ?string $model = OfficeSupplyRequestItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return OfficeSupplyRequestItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfficeSupplyRequestItemsTable::configure($table);
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
            'index' => ListOfficeSupplyRequestItems::route('/'),
            'create' => CreateOfficeSupplyRequestItem::route('/create'),
            'edit' => EditOfficeSupplyRequestItem::route('/{record}/edit'),
        ];
    }
}
