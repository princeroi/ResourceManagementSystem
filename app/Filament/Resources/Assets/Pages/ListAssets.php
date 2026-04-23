<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getListeners(): array
    {
        return [
            'openPropertyTagPrint' => 'handlePropertyTagPrint',
        ];
    }

    public function handlePropertyTagPrint(string $ids): void
    {
        $url = route('assets.property-tags.bulk', ['ids' => $ids]);

        $this->js("window.open('{$url}', '_blank')");
    }
}