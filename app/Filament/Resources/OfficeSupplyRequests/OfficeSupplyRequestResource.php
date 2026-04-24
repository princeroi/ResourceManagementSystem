<?php

namespace App\Filament\Resources\OfficeSupplyRequests;

use App\Filament\Resources\OfficeSupplyRequests\Pages\CreateOfficeSupplyRequest;
use App\Filament\Resources\OfficeSupplyRequests\Pages\EditOfficeSupplyRequest;
use App\Filament\Resources\OfficeSupplyRequests\Pages\ListOfficeSupplyRequests;
use App\Filament\Resources\OfficeSupplyRequests\Schemas\OfficeSupplyRequestForm;
use App\Filament\Resources\OfficeSupplyRequests\Tables\OfficeSupplyRequestsTable;
use App\Models\OfficeSupplyRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OfficeSupplyRequestResource extends Resource
{
    protected static ?string $model = OfficeSupplyRequest::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-bell';

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
        return OfficeSupplyRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OfficeSupplyRequestsTable::configure($table);
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
            'index' => ListOfficeSupplyRequests::route('/'),
            'create' => CreateOfficeSupplyRequest::route('/create'),
            'edit' => EditOfficeSupplyRequest::route('/{record}/edit'),
        ];
    }
}
