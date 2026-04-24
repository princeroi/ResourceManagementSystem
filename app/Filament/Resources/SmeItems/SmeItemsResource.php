<?php

namespace App\Filament\Resources\SmeItems;

use App\Filament\Resources\SmeItems\Pages\CreateSmeItems;
use App\Filament\Resources\SmeItems\Pages\EditSmeItems;
use App\Filament\Resources\SmeItems\Pages\ListSmeItems;
use App\Filament\Resources\SmeItems\Schemas\SmeItemsForm;
use App\Filament\Resources\SmeItems\Tables\SmeItemsTable;
use App\Models\SmeItems;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SmeItemsResource extends Resource
{
    protected static ?string $model = SmeItems::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-boxes-stacked';

    public static function getNavigationGroup(): ?string
    {
        return 'Item Setup';
    }

    public static function form(Schema $schema): Schema
    {
        return SmeItemsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmeItemsTable::configure($table);
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
            'index' => ListSmeItems::route('/'),
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
