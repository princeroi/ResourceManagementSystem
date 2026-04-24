<?php

namespace App\Filament\Resources\UniformItemVariants;

use App\Filament\Resources\UniformItemVariants\Pages\CreateUniformItemVariants;
use App\Filament\Resources\UniformItemVariants\Pages\EditUniformItemVariants;
use App\Filament\Resources\UniformItemVariants\Pages\ListUniformItemVariants;
use App\Filament\Resources\UniformItemVariants\Schemas\UniformItemVariantsForm;
use App\Filament\Resources\UniformItemVariants\Tables\UniformItemVariantsTable;
use App\Models\UniformItemVariants;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UniformItemVariantsResource extends Resource
{
    protected static ?string $model = UniformItemVariants::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-chart-line';

    protected static ?string $navigationLabel = 'Uniform Stock';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->get()
            ->filter(function ($record) {
                return $record->uniform_item_quantity == 0
                    || $record->uniform_item_quantity <= $record->moq;
            })
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger'; // red
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Stock & Inventory';
    }

    public static function form(Schema $schema): Schema
    {
        return UniformItemVariantsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UniformItemVariantsTable::configure($table);
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
            'index' => ListUniformItemVariants::route('/'),
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
