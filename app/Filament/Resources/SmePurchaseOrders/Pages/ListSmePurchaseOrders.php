<?php

namespace App\Filament\Resources\SmePurchaseOrders\Pages;

use App\Filament\Resources\SmePurchaseOrders\SmePurchaseOrderResource;
use App\Models\SmePurchaseOrder;
use App\Models\SmePurchaseOrderLog;
use App\Models\SmeItemVariants;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListSmePurchaseOrders extends ListRecords
{
    protected static string $resource = SmePurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function (SmePurchaseOrder $record): void {

                    // ── Log: created ──
                    SmePurchaseOrderLog::create([
                        'sme_purchase_order_id' => $record->id,
                        'user_id'               => Auth::id(),
                        'action'                => 'created',
                        'status_from'           => null,
                        'status_to'             => $record->status,
                        'note'                  => null,
                    ]);

                    // ── If created as approved, deduct stock ──
                    if ($record->status !== 'approved') {
                        return;
                    }

                    DB::transaction(function () use ($record): void {
                        $record->load('purchaseOrderItems.smeItemVariant.smeItem');

                        $noteItems = [];

                        foreach ($record->purchaseOrderItems as $item) {
                            $variant = $item->smeItemVariant;

                            if ($variant->sme_item_quantity < $item->quantity) {
                                throw new \Exception(
                                    "Insufficient stock for variant ID {$variant->id}. " .
                                    "Available: {$variant->sme_item_quantity}, Required: {$item->quantity}"
                                );
                            }

                            $stockBefore = (int) $variant->sme_item_quantity;
                            $variant->decrement('sme_item_quantity', $item->quantity);
                            $stockAfter = $stockBefore - $item->quantity;

                            $noteItems[] = [
                                'label'        => ($item->smeItem?->sme_item_name ?? '—') . ' (' . ($item->smeItemVariant?->sme_item_size ?? '—') . ')',
                                'qty'          => $item->quantity,
                                'stock_before' => $stockBefore,
                                'stock_after'  => $stockAfter,
                            ];
                        }

                        $record->update(['approved_at' => now()]);

                        SmePurchaseOrderLog::create([
                            'sme_purchase_order_id' => $record->id,
                            'user_id'               => Auth::id(),
                            'action'                => 'approved',
                            'status_from'           => 'approved',
                            'status_to'             => 'approved',
                            'note'                  => $noteItems,
                        ]);
                    });
                }),
        ];
    }
}