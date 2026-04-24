<?php

namespace App\Filament\Resources\ReturnUniformItems;

use App\Filament\Resources\ReturnUniformItems\Pages\CreateReturnUniformItems;
use App\Filament\Resources\ReturnUniformItems\Pages\EditReturnUniformItems;
use App\Filament\Resources\ReturnUniformItems\Pages\ListReturnUniformItems;
use App\Filament\Resources\ReturnUniformItems\Schemas\ReturnUniformItemsForm;
use App\Filament\Resources\ReturnUniformItems\Tables\ReturnUniformItemsTable;
use App\Models\ReturnUniformItems;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ReturnUniformItemsResource extends Resource
{
    protected static ?string $model = ReturnUniformItems::class;

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
        return ReturnUniformItemsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReturnUniformItemsTable::configure($table);
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
            'index' => ListReturnUniformItems::route('/'),
        ];
    }
}
