<?php

namespace App\Filament\Resources\SmeRestocks\Pages;

use App\Filament\Resources\SmeRestocks\SmeRestocksResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\SmeItemVariants;
use App\Models\SmeRestockLogs;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ListSmeRestocks extends ListRecords
{
    protected static string $resource = SmeRestocksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->extraAttributes([
                    'style' => 'color: #ffffff;'
                ])
                ->after(function ($record) {
                    $record->loadMissing(
                        'smeRestockItem.smeItem',
                        'smeRestockItem.smeItemVariant'
                    );

                    // ── Always log creation ────────────────────────────────
                    SmeRestockLogs::create([
                        'sme_restock_id' => $record->id,
                        'user_id'        => Auth::id(),
                        'action'         => 'created',
                        'status_from'    => null,
                        'status_to'      => $record->status,
                        'note'           => 'Restock order created.',
                    ]);

                    // ── If created as delivered, add stock + log delivery ──
                    if ($record->status === 'delivered') {
                        $noteRows = [];

                        foreach ($record->smeRestockItem as $item) {
                            $qty = (int) $item->quantity;
                            if ($qty <= 0) continue;

                            $variant = SmeItemVariants::find($item->sme_item_variant_id);

                            if ($variant) {
                                $stockBefore = (int) $variant->sme_item_quantity;
                                $variant->increment('sme_item_quantity', $qty);
                                $stockAfter = $stockBefore + $qty;
                            } else {
                                $stockBefore = null;
                                $stockAfter  = null;
                            }

                            // Ensure delivered/remaining are correctly set
                            $item->update([
                                'delivered_quantity' => $qty,
                                'remaining_quantity' => 0,
                            ]);

                            $noteRows[] = [
                                'label'        => "{$item->smeItem?->sme_item_name} ({$item->smeItemVariant?->sme_item_size})",
                                'delivered'    => $qty,
                                'stock_before' => $stockBefore,
                                'stock_after'  => $stockAfter,
                            ];
                        }

                        // Ensure delivered_at is set
                        $record->update([
                            'delivered_at' => $record->delivered_at ?? now()->toDateString(),
                        ]);

                        SmeRestockLogs::create([
                            'sme_restock_id' => $record->id,
                            'user_id'        => Auth::id(),
                            'action'         => 'delivered',
                            'status_from'    => 'pending',
                            'status_to'      => 'delivered',
                            'note'           => json_encode($noteRows),
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