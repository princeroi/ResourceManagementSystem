<?php

namespace App\Filament\Resources\OfficeSupplyRequests\Tables;

use App\Models\OfficeSupplyRequestItem;
use App\Models\OfficeSupplyItemVariant;
use App\Models\OfficeSupplyRequestLog;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class OfficeSupplyRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('request_number')
                    ->label('Request #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->badge()
                    ->color('info'),

                TextColumn::make('requested_by')
                    ->searchable(),

                TextColumn::make('request_date')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'approved'  => 'info',
                        'completed' => 'success',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions([

                // ─── COMPLETE ──────────────────────────────────────────────
                Action::make('markCompleted')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Completed')
                    ->modalDescription('This will mark the request as completed and deduct the quantities from the item variants. This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, Complete It')
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {

                            $record->load([
                                'items.item'    => fn($q) => $q->withTrashed(),
                                'items.variant' => fn($q) => $q->withTrashed(),
                            ]);

                            $noteItems = [];

                            foreach ($record->items as $requestItem) {
                                if (!$requestItem->item_variant_id) continue;

                                $variant = $requestItem->variant;
                                if (!$variant) continue;

                                $stockBefore = (int) $variant->office_supply_quantity;
                                $variant->decrement('office_supply_quantity', $requestItem->quantity);
                                $stockAfter = (int) ($stockBefore - $requestItem->quantity);

                                $itemName = $requestItem->item?->office_supply_name ?? '—';
                                $size     = $variant->office_supply_variant ?? '—';

                                $noteItems[] = [
                                    'label'        => "{$itemName} ({$size})",
                                    'qty'          => (int) $requestItem->quantity,
                                    'stock_before' => $stockBefore,
                                    'stock_after'  => $stockAfter,
                                ];
                            }

                            $record->update(['status' => 'completed']);

                            OfficeSupplyRequestLog::create([
                                'office_supply_request_id' => $record->id,
                                'user_id'                  => Auth::id(),
                                'action'                   => 'completed',
                                'status_from'              => 'pending',
                                'status_to'                => 'completed',
                                'note'                     => $noteItems,
                            ]);
                        });
                    }),

                // ─── REJECT ────────────────────────────────────────────────
                Action::make('markRejected')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Request')
                    ->modalDescription('Are you sure you want to reject this request?')
                    ->modalSubmitActionLabel('Yes, Reject')
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);

                        OfficeSupplyRequestLog::create([
                            'office_supply_request_id' => $record->id,
                            'user_id'                  => Auth::id(),
                            'action'                   => 'rejected',
                            'status_from'              => 'pending',
                            'status_to'                => 'rejected',
                            'note'                     => [],
                        ]);
                    }),

                // ─── LOGS ──────────────────────────────────────────────────
                Action::make('request_logs')
                    ->label('Logs')
                    ->color('gray')
                    ->icon('heroicon-o-clock')
                    ->modalWidth('3xl')
                    ->modalHeading(fn($record) => 'Request Logs — ' . $record->request_number)
                    ->modalContent(function ($record) {

                        $logs = OfficeSupplyRequestLog::with('user')
                            ->where('office_supply_request_id', $record->id)
                            ->orderByDesc('created_at')
                            ->get();

                        if ($logs->isEmpty()) {
                            return new HtmlString("
                                <div style='padding:40px;text-align:center;color:#9ca3af;font-size:13px;'>
                                    No logs found for this request.
                                </div>
                            ");
                        }

                        $rows = '';
                        foreach ($logs as $log) {
                            $date   = \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A');
                            $user   = e($log->user?->name ?? 'System');
                            $action = $log->action;

                            [$actionBg, $actionLabel] = match ($action) {
                                'completed' => ['#16a34a', 'COMPLETED'],
                                'rejected'  => ['#dc2626', 'REJECTED'],
                                'created'   => ['#2563eb', 'CREATED'],
                                'edited'    => ['#d97706', 'EDITED'],
                                default     => ['#6b7280', strtoupper(str_replace('_', ' ', $action))],
                            };

                            $statusHtml = '';
                            if ($log->status_from && $log->status_to) {
                                $statusHtml = "
                                    <div style='font-size:10px;color:#9ca3af;margin-top:3px;'>
                                        <span style='color:#374151;'>" . e(ucfirst($log->status_from)) . "</span>
                                        →
                                        <span style='color:#1e3a5f;font-weight:700;'>" . e(ucfirst($log->status_to)) . "</span>
                                    </div>";
                            }

                            // ── Per-item breakdown ──
                            $itemsHtml = '';
                            $noteItems = is_array($log->note) ? $log->note : [];
                            if (!empty($noteItems)) {
                                $itemRows = '';
                                foreach ($noteItems as $item) {
                                    $label       = e($item['label'] ?? '—');
                                    $qty         = (int) ($item['qty'] ?? 0);
                                    $stockBefore = $item['stock_before'] ?? null;
                                    $stockAfter  = $item['stock_after']  ?? null;

                                    $stockStr = ($stockBefore !== null && $stockAfter !== null)
                                        ? "<span style='color:#6b7280;font-size:10px;margin-left:6px;'>
                                               stock: <strong style='color:#374151;'>{$stockBefore}</strong>
                                               → <strong style='color:#dc2626;'>{$stockAfter}</strong>
                                           </span>"
                                        : '';

                                    $itemRows .= "
                                        <div style='display:flex;align-items:center;justify-content:space-between;
                                            padding:4px 8px;border-bottom:1px solid #f1f5f9;font-size:11px;'>
                                            <span style='color:#111827;'>{$label}</span>
                                            <span style='display:flex;align-items:center;gap:4px;'>
                                                <span style='background:#fef2f2;color:#dc2626;font-weight:800;
                                                    padding:2px 8px;border-radius:6px;font-size:11px;'>−{$qty}</span>
                                                {$stockStr}
                                            </span>
                                        </div>";
                                }
                                $itemsHtml = "
                                    <div style='margin-top:8px;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;'>
                                        {$itemRows}
                                    </div>";
                            }

                            $rows .= "
                                <tr>
                                    <td style='padding:10px 12px;border-bottom:1px solid #f1f5f9;vertical-align:top;min-width:130px;'>
                                        <div style='font-size:11px;color:#374151;white-space:nowrap;'>{$date}</div>
                                        <div style='font-size:10px;color:#9ca3af;margin-top:2px;'>{$user}</div>
                                    </td>
                                    <td style='padding:10px 12px;border-bottom:1px solid #f1f5f9;vertical-align:top;'>
                                        <span style='background:{$actionBg};color:#fff;font-size:9px;font-weight:700;
                                            padding:2px 8px;border-radius:999px;letter-spacing:.04em;'>
                                            {$actionLabel}
                                        </span>
                                        {$statusHtml}
                                    </td>
                                    <td style='padding:10px 12px;border-bottom:1px solid #f1f5f9;vertical-align:top;'>
                                        {$itemsHtml}
                                    </td>
                                </tr>";
                        }

                        $totalCount  = $logs->count();
                        $reqNumber   = e($record->request_number);
                        $requestedBy = e($record->requested_by ?? '—');

                        return new HtmlString("
                            <div style='font-family:\"DM Sans\",system-ui,sans-serif;'>

                                <!-- Summary strip -->
                                <div style='display:flex;justify-content:space-between;align-items:center;
                                    padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;
                                    border-radius:10px;margin-bottom:12px;'>
                                    <div>
                                        <div style='font-size:12px;font-weight:700;color:#1e3a5f;'>{$reqNumber}</div>
                                        <div style='font-size:11px;color:#9ca3af;margin-top:2px;'>Requested by: {$requestedBy}</div>
                                    </div>
                                    <div style='text-align:right;'>
                                        <div style='font-size:10px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px;'>Log Entries</div>
                                        <div style='font-size:20px;font-weight:900;color:#1e3a5f;letter-spacing:-.03em;'>{$totalCount}</div>
                                    </div>
                                </div>

                                <!-- Table -->
                                <div style='max-height:520px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;'>
                                    <table style='width:100%;border-collapse:collapse;'>
                                        <thead style='position:sticky;top:0;z-index:1;'>
                                            <tr style='background:#1e3a5f;'>
                                                <th style='padding:9px 12px;text-align:left;font-size:10.5px;font-weight:600;color:#e0f2fe;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;'>Date / By</th>
                                                <th style='padding:9px 12px;text-align:left;font-size:10.5px;font-weight:600;color:#e0f2fe;text-transform:uppercase;letter-spacing:.06em;'>Action</th>
                                                <th style='padding:9px 12px;text-align:left;font-size:10.5px;font-weight:600;color:#e0f2fe;text-transform:uppercase;letter-spacing:.06em;'>Items</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$rows}</tbody>
                                    </table>
                                </div>

                            </div>
                        ");
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}