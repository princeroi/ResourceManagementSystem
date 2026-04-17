<?php

namespace App\Filament\Resources\SmePurchaseOrders;

use App\Filament\Resources\SmePurchaseOrders\Pages\CreateSmePurchaseOrder;
use App\Filament\Resources\SmePurchaseOrders\Pages\EditSmePurchaseOrder;
use App\Filament\Resources\SmePurchaseOrders\Pages\ListSmePurchaseOrders;
use App\Filament\Resources\SmePurchaseOrders\Schemas\SmePurchaseOrderForm;
use App\Filament\Resources\SmePurchaseOrders\Tables\SmePurchaseOrdersTable;
use App\Models\SmePurchaseOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SmePurchaseOrderResource extends Resource
{
    protected static ?string $model = SmePurchaseOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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
        return SmePurchaseOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmePurchaseOrdersTable::configure($table);
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
            'index' => ListSmePurchaseOrders::route('/'),
        ];
    }
}
