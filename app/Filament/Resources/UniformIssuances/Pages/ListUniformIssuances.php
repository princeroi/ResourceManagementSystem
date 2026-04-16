<?php

namespace App\Filament\Resources\UniformIssuances\Pages;

use App\Filament\Resources\UniformIssuances\UniformIssuancesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\UniformIssuanceLog;
use App\Models\UniformItemVariants;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ListUniformIssuances extends ListRecords
{
    protected static string $resource = UniformIssuancesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->extraAttributes([
                    'style' => 'color: #ffffff;'
                ])
                ->after(function ($record) {
                    UniformIssuancesResource::syncQuantities($record);

                    $record->loadMissing(
                        'uniformIssuanceRecipient.uniformIssuanceItem.uniformItem',
                        'uniformIssuanceRecipient.uniformIssuanceItem.uniformItemVariant'
                    );

                    // ── Always log creation ────────────────────────────────
                    UniformIssuanceLog::create([
                        'uniform_issuance_id' => $record->id,
                        'user_id'             => Auth::id(),
                        'action'              => 'created',
                        'status_from'         => null,
                        'status_to'           => $record->uniform_issuance_status,
                        'note'                => 'Issuance was created.',
                    ]);

                    // ── If created as issued, deduct stock + log ───────────
                    if ($record->uniform_issuance_status === 'issued') {
                        $noteRows = [];

                        foreach ($record->uniformIssuanceRecipient as $recipient) {
                            foreach ($recipient->uniformIssuanceItem as $item) {
                                $qty = (int) $item->quantity;
                                if ($qty <= 0) continue;

                                // Ensure released/remaining are correctly set
                                $item->update([
                                    'released_quantity'  => $qty,
                                    'remaining_quantity' => 0,
                                ]);

                                $variant = UniformItemVariants::find($item->uniform_item_variant_id);

                                if ($variant) {
                                    $stockBefore = (int) $variant->uniform_item_quantity;
                                    $variant->decrement('uniform_item_quantity', $qty);
                                    $stockAfter = $stockBefore - $qty;
                                } else {
                                    $stockBefore = null;
                                    $stockAfter  = null;
                                }

                                $noteRows[] = [
                                    'label'        => ($item->uniformItem?->uniform_item_name ?? '—')
                                                   . ' (' . ($item->uniformItemVariant?->uniform_item_size ?? '—') . ')'
                                                   . ' — ' . ($recipient->employee_name ?? '—'),
                                    'released'     => $qty,
                                    'stock_before' => $stockBefore,
                                    'stock_after'  => $stockAfter,
                                ];
                            }
                        }

                        // Ensure issued_at is set
                        $record->update([
                            'issued_at' => $record->issued_at ?? now()->toDateString(),
                        ]);

                        UniformIssuanceLog::create([
                            'uniform_issuance_id' => $record->id,
                            'user_id'             => Auth::id(),
                            'action'              => 'issued',
                            'status_from'         => 'pending',
                            'status_to'           => 'issued',
                            'note'                => json_encode($noteRows),
                        ]);

                        $totalReleased = collect($noteRows)->sum('released');

                        Notification::make()
                            ->title('Issuance Created & Issued')
                            ->body("All items have been deducted from inventory. ({$totalReleased} pcs total)")
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}