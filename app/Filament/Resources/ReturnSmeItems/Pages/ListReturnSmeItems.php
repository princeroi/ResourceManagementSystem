<?php

namespace App\Filament\Resources\ReturnSmeItems\Pages;

use App\Filament\Resources\ReturnSmeItems\ReturnSmeItemsResource;
use App\Models\ReturnSmeItemLog;
use App\Models\SmeItemVariants;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ListReturnSmeItems extends ListRecords
{
    protected static string $resource = ReturnSmeItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record) {
                    $record->loadMissing(
                        'returnSmeItemLine.smeItem',
                        'returnSmeItemLine.smeItemVariant'
                    );

                    // ── Always log creation ────────────────────────────────
                    ReturnSmeItemLog::create([
                        'return_sme_item_id' => $record->id,
                        'user_id'            => Auth::id(),
                        'action'             => 'created',
                        'status_from'        => null,
                        'status_to'          => $record->status,
                        'note'               => 'Return record created.',
                    ]);

                    // ── If created as returned, process stock + log ────────
                    if ($record->status === 'returned') {
                        $noteRows = [];

                        foreach ($record->returnSmeItemLine as $line) {
                            $qty        = (int) $line->quantity;
                            $addToStock = (bool) $line->add_to_stock;

                            if ($qty <= 0) continue;

                            // Ensure returned/remaining are correctly set
                            $line->update([
                                'returned_quantity'  => $qty,
                                'remaining_quantity' => 0,
                            ]);

                            $stockBefore = null;
                            $stockAfter  = null;

                            // ── Only add to stock if toggle is ON ─────────
                            if ($addToStock) {
                                $variant = SmeItemVariants::find($line->sme_item_variant_id);
                                if ($variant) {
                                    $stockBefore = (int) $variant->sme_item_quantity;
                                    $variant->increment('sme_item_quantity', $qty);
                                    $stockAfter = $stockBefore + $qty;
                                }
                            }

                            $noteRows[] = [
                                'label'        => ($line->smeItem?->sme_item_name ?? '—')
                                               . ' (' . ($line->smeItemVariant?->sme_item_size ?? '—') . ')'
                                               . ($line->employee_name ? ' — ' . $line->employee_name : ''),
                                'accepted'     => $qty,
                                'add_to_stock' => $addToStock,
                                'stock_before' => $stockBefore,
                                'stock_after'  => $stockAfter,
                            ];
                        }

                        // Ensure returned_at is set
                        $record->update([
                            'returned_at' => $record->returned_at ?? now()->toDateString(),
                        ]);

                        ReturnSmeItemLog::create([
                            'return_sme_item_id' => $record->id,
                            'user_id'            => Auth::id(),
                            'action'             => 'returned',
                            'status_from'        => 'pending',
                            'status_to'          => 'returned',
                            'note'               => json_encode($noteRows),
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