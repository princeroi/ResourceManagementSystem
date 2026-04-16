<?php

namespace App\Filament\Resources\ReturnUniformItems\Pages;

use App\Filament\Resources\ReturnUniformItems\ReturnUniformItemsResource;
use App\Models\ReturnUniformItemLog;
use App\Models\UniformItemVariants;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ListReturnUniformItems extends ListRecords
{
    protected static string $resource = ReturnUniformItemsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function ($record) {
                    $record->loadMissing(
                        'returnUniformItemLine.uniformItem',
                        'returnUniformItemLine.uniformItemVariant'
                    );

                    // ── Always log creation ────────────────────────────────
                    ReturnUniformItemLog::create([
                        'return_uniform_item_id' => $record->id,
                        'user_id'                => Auth::id(),
                        'action'                 => 'created',
                        'status_from'            => null,
                        'status_to'              => $record->status,
                        'note'                   => 'Return record created.',
                    ]);

                    // ── If created as returned, process stock + log ────────
                    if ($record->status === 'returned') {
                        $noteRows = [];

                        foreach ($record->returnUniformItemLine as $line) {
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
                                $variant = UniformItemVariants::find($line->uniform_item_variant_id);
                                if ($variant) {
                                    $stockBefore = (int) $variant->uniform_item_quantity;
                                    $variant->increment('uniform_item_quantity', $qty);
                                    $stockAfter = $stockBefore + $qty;
                                }
                            }

                            $noteRows[] = [
                                'label'        => ($line->uniformItem?->uniform_item_name ?? '—')
                                               . ' (' . ($line->uniformItemVariant?->uniform_item_size ?? '—') . ')'
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

                        ReturnUniformItemLog::create([
                            'return_uniform_item_id' => $record->id,
                            'user_id'                => Auth::id(),
                            'action'                 => 'returned',
                            'status_from'            => 'pending',
                            'status_to'              => 'returned',
                            'note'                   => json_encode($noteRows),
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