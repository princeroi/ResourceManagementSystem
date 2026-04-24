<?php

namespace App\Filament\Resources\ReturnSmeItems;

use App\Filament\Resources\ReturnSmeItems\Pages\CreateReturnSmeItems;
use App\Filament\Resources\ReturnSmeItems\Pages\EditReturnSmeItems;
use App\Filament\Resources\ReturnSmeItems\Pages\ListReturnSmeItems;
use App\Filament\Resources\ReturnSmeItems\Schemas\ReturnSmeItemsForm;
use App\Filament\Resources\ReturnSmeItems\Tables\ReturnSmeItemsTable;
use App\Models\ReturnSmeItems;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReturnSmeItemsResource extends Resource
{
    protected static ?string $model = ReturnSmeItems::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowUturnLeft;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger'; // red
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Distributions';
    }

    public static function form(Schema $schema): Schema
    {
        return ReturnSmeItemsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReturnSmeItemsTable::configure($table);
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
            'index' => ListReturnSmeItems::route('/'),
        ];
    }
}
