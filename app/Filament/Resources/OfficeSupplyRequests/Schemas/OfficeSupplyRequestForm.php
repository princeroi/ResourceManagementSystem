<?php

namespace App\Filament\Resources\OfficeSupplyRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OfficeSupplyRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('requested_by')
                    ->required(),
                DatePicker::make('request_date'),
                Textarea::make('note')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'completed' => 'Completed'])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
