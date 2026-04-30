<?php

namespace App\Filament\Resources\ReturnOfficeSupplyItems\Pages;

use App\Filament\Resources\ReturnOfficeSupplyItems\ReturnOfficeSupplyItemsResource;
use App\Models\ReturnOfficeSupplyItemLog;
use App\Models\OfficeSupplyItemVariant;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ListReturnOfficeSupplyItems extends ListRecords
{
    protected static string $resource = ReturnOfficeSupplyItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record) {
                    $record->loadMissing(
                        'returnOfficeSupplyItemLine.officeSupplyItem',
                        'returnOfficeSupplyItemLine.officeSupplyItemVariant'
                    );

                    // ── Always log creation ────────────────────────────────
                    ReturnOfficeSupplyItemLog::create([
                        'return_office_supply_item_id' => $record->id,
                        'user_id'                      => Auth::id(),
                        'action'                       => 'created',
                        'status_from'                  => null,
                        'status_to'                    => $record->status,
                        'note'                         => 'Return record created.',
                    ]);

                    // ── If created as returned, process stock + log ────────
                    if ($record->status === 'returned') {
                        $noteRows = [];

                        foreach ($record->returnOfficeSupplyItemLine as $line) {
                            $qty        = (int) $line->quantity;
                            $addToStock = (bool) $line->add_to_stock;

                            if ($qty <= 0) continue;

                            $line->update([
                                'returned_quantity'  => $qty,
                                'remaining_quantity' => 0,
                            ]);

                            $stockBefore = null;
                            $stockAfter  = null;

                            if ($addToStock) {
                                $variant = OfficeSupplyItemVariant::find($line->office_supply_item_variant_id);
                                if ($variant) {
                                    $stockBefore = (int) $variant->office_supply_quantity;
                                    $variant->increment('office_supply_quantity', $qty);
                                    $stockAfter = $stockBefore + $qty;
                                }
                            }

                            $noteRows[] = [
                                'label'        => ($line->officeSupplyItem?->office_supply_name ?? '—')
                                               . ' (' . ($line->officeSupplyItemVariant?->office_supply_variant ?? '—') . ')'
                                               . ($line->employee_name ? ' — ' . $line->employee_name : ''),
                                'accepted'     => $qty,
                                'add_to_stock' => $addToStock,
                                'stock_before' => $stockBefore,
                                'stock_after'  => $stockAfter,
                            ];
                        }

                        $record->update([
                            'returned_at' => $record->returned_at ?? now()->toDateString(),
                        ]);

                        ReturnOfficeSupplyItemLog::create([
                            'return_office_supply_item_id' => $record->id,
                            'user_id'                      => Auth::id(),
                            'action'                       => 'returned',
                            'status_from'                  => 'pending',
                            'status_to'                    => 'returned',
                            'note'                         => json_encode($noteRows),
                        ]);

                        $stockUpdated = collect($noteRows)->where('add_to_stock', true)->count();

                        Notification::make()
                            ->title('Return Created & Processed')
                            ->body("All items accepted." . ($stockUpdated > 0 ? " {$stockUpdated} item(s) added to inventory." : ' No stock changes applied.'))
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}