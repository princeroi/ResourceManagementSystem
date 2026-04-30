<?php

namespace App\Filament\Resources\OfficeSupplyRestocks\Pages;

use App\Filament\Resources\OfficeSupplyRestocks\OfficeSupplyRestocksResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\OfficeSupplyItemVariant;
use App\Models\OfficeSupplyRestockLog;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ListOfficeSupplyRestocks extends ListRecords
{
    protected static string $resource = OfficeSupplyRestocksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->extraAttributes([
                    'style' => 'color: #ffffff;'
                ])
                ->after(function ($record) {
                    $record->loadMissing(
                        'officeSupplyRestockItem.officeSupplyItem',
                        'officeSupplyRestockItem.officeSupplyItemVariant'
                    );

                    // ── Always log creation ────────────────────────────────
                    OfficeSupplyRestockLog::create([
                        'office_supply_restock_id' => $record->id,
                        'user_id'                  => Auth::id(),
                        'action'                   => 'created',
                        'status_from'              => null,
                        'status_to'                => $record->status,
                        'note'                     => 'Restock order created.',
                    ]);

                    // ── If created as delivered, add stock + log delivery ──
                    if ($record->status === 'delivered') {
                        $noteRows = [];

                        foreach ($record->officeSupplyRestockItem as $item) {
                            $qty = (int) $item->quantity;
                            if ($qty <= 0) continue;

                            $variant = OfficeSupplyItemVariant::find($item->office_supply_item_variant_id);

                            if ($variant) {
                                $stockBefore = (int) $variant->office_supply_quantity;
                                $variant->increment('office_supply_quantity', $qty);
                                $stockAfter = $stockBefore + $qty;
                            } else {
                                $stockBefore = null;
                                $stockAfter  = null;
                            }

                            $item->update([
                                'delivered_quantity' => $qty,
                                'remaining_quantity' => 0,
                            ]);

                            $noteRows[] = [
                                'label'        => "{$item->officeSupplyItem?->office_supply_name} ({$item->officeSupplyItemVariant?->office_supply_variant})",
                                'delivered'    => $qty,
                                'stock_before' => $stockBefore,
                                'stock_after'  => $stockAfter,
                            ];
                        }

                        $record->update([
                            'delivered_at' => $record->delivered_at ?? now()->toDateString(),
                        ]);

                        OfficeSupplyRestockLog::create([
                            'office_supply_restock_id' => $record->id,
                            'user_id'                  => Auth::id(),
                            'action'                   => 'delivered',
                            'status_from'              => 'pending',
                            'status_to'                => 'delivered',
                            'note'                     => json_encode($noteRows),
                        ]);

                        Notification::make()
                            ->title('Restock Created & Delivered')
                            ->body('All items have been added to inventory.')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}