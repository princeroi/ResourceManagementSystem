<?php

namespace App\Filament\Resources\SmeBillings;

use App\Filament\Resources\SmeBillings\Pages\CreateSmeBilling;
use App\Filament\Resources\SmeBillings\Pages\EditSmeBilling;
use App\Filament\Resources\SmeBillings\Pages\ListSmeBillings;
use App\Filament\Resources\SmeBillings\Schemas\SmeBillingForm;
use App\Filament\Resources\SmeBillings\Tables\SmeBillingsTable;
use App\Models\SmeBilling;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SmeBillingResource extends Resource
{
    protected static ?string $model = SmeBilling::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SmeBillingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmeBillingsTable::configure($table);
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
            'index' => ListSmeBillings::route('/'),
        ];
    }
}
