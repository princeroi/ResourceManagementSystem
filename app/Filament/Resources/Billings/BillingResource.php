<?php

namespace App\Filament\Resources\Billings;

use App\Filament\Resources\Billings\Pages\CreateBilling;
use App\Filament\Resources\Billings\Pages\EditBilling;
use App\Filament\Resources\Billings\Pages\ListBillings;
use App\Filament\Resources\Billings\Schemas\BillingForm;
use App\Filament\Resources\Billings\Tables\BillingsTable;
use App\Models\Billing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BillingResource extends Resource
{
    protected static ?string $model = Billing::class;

    protected static BackedEnum|string|null $navigationIcon = 'fas-file-invoice';

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
        return 'Billing Management';
    }

    public static function form(Schema $schema): Schema
    {
        return BillingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillingsTable::configure($table);
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
            'index' => ListBillings::route('/'),
        ];
    }
}
