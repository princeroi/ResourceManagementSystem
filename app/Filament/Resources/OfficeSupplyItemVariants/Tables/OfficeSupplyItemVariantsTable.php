<?php

namespace App\Filament\Resources\OfficeSupplyItemVariants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use App\Models\OfficeSupplyRequestLog;
use App\Models\OfficeSupplyRestockLog;
use App\Models\ReturnOfficeSupplyItemLog;

class OfficeSupplyItemVariantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.office_supply_name')
                    ->label('Item')
                    ->searchable(),
                TextColumn::make('office_supply_variant')
                    ->searchable(),
                TextColumn::make('office_supply_quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('moq')
                    ->label('MOQ')
                    ->badge()
                    ->color(fn ($record) =>
                        $record->office_supply_quantity == 0
                            ? 'danger'
                            : ($record->office_supply_quantity <= $record->moq
                                ? 'warning'
                                : 'success')
                    )
                    ->formatStateUsing(fn ($record) => $record->moq)
                    ->sortable(),
                TextColumn::make('stock_status')
                    ->badge()
                    ->color(fn ($record) =>
                        $record->office_supply_quantity == 0
                            ? 'danger'
                            : ($record->office_supply_quantity <= $record->moq
                                ? 'warning'
                                : 'success')
                    )
                    ->getStateUsing(fn ($record) =>
                        $record->office_supply_quantity == 0
                            ? 'No Stock'
                            : ($record->office_supply_quantity <= $record->moq
                                ? 'Low Stock'
                                : 'Enough Stock')
                    ),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([

                // ─── STOCK LOGS ────────────────────────────────────────────
                Action::make('stock_logs')
                    ->label('Logs')
                    ->color('gray')
                    ->icon('heroicon-o-clock')
                    ->modalWidth('3xl')
                    ->modalHeading(fn ($record) =>
                        'Stock Logs — '
                        . ($record->item?->office_supply_name ?? '—')
                        . ' ('
                        . ($record->office_supply_variant ?? '—')
                        . ')'
                    )
                    ->modalContent(function ($record) {

                        $itemName    = $record->item?->office_supply_name ?? '—';
                        $variant     = $record->office_supply_variant ?? '—';
                        $labelNeedle = "{$itemName} ({$variant})";

                        $allEntries = collect();

                        // ── Helper: safely decode note ─────────────────────
                        $decodeNote = function ($note): array {
                            if (is_array($note)) return $note;
                            if (empty($note))    return [];
                            $decoded = json_decode($note, true);
                            return is_array($decoded) ? $decoded : [];
                        };

                        // ── 1. REQUEST LOGS (approved → deducts stock) ─────
                        $requestLogs = OfficeSupplyRequestLog::with('user')
                            ->where('action', 'approved')
                            ->get();

                        foreach ($requestLogs as $log) {
                            $noteData = $decodeNote($log->note);
                            if (!is_array($noteData)) continue;

                            foreach ($noteData as $row) {
                                $rowLabel = $row['label'] ?? '';
                                if (strcasecmp(trim($rowLabel), trim($labelNeedle)) !== 0 &&
                                    !(str_contains($rowLabel, $itemName) && str_contains($rowLabel, $variant))) {
                                    continue;
                                }

                                $qty = (int) ($row['qty'] ?? 0);
                                if ($qty <= 0) continue;

                                $allEntries->push([
                                    'source'       => 'request',
                                    'action'       => 'request_approved',
                                    'label'        => e($rowLabel),
                                    'context'      => null,
                                    'qty'          => $qty,
                                    'direction'    => '-',
                                    'stock_before' => isset($row['stock_before']) ? (int) $row['stock_before'] : null,
                                    'stock_after'  => isset($row['stock_after'])  ? (int) $row['stock_after']  : null,
                                    'user'         => $log->user?->name ?? 'System',
                                    'date'         => $log->created_at,
                                    'reference'    => 'Request #' . $log->office_supply_request_id,
                                ]);
                            }
                        }

                        // ── 2. RESTOCK LOGS (delivered/partial → adds, returned → deducts) ──
                        $restockLogs = OfficeSupplyRestockLog::with('user')
                            ->whereIn('action', ['delivered', 'partial', 'returned'])
                            ->get();

                        foreach ($restockLogs as $log) {
                            $noteData = $decodeNote($log->note);
                            if (!is_array($noteData)) continue;

                            foreach ($noteData as $row) {
                                $rowLabel = $row['label'] ?? '';
                                if (!(str_contains($rowLabel, $itemName) && str_contains($rowLabel, $variant))) continue;

                                if ($log->action === 'returned') {
                                    $qty = (int) ($row['qty'] ?? 0);
                                    if ($qty <= 0) continue;
                                    $allEntries->push([
                                        'source'       => 'restock',
                                        'action'       => 'restock_returned',
                                        'label'        => e($rowLabel),
                                        'context'      => 'Reason: ' . e(ucfirst(str_replace('_', ' ', $row['reason'] ?? '—'))),
                                        'qty'          => $qty,
                                        'direction'    => '-',
                                        'stock_before' => isset($row['stock_before']) ? (int) $row['stock_before'] : null,
                                        'stock_after'  => isset($row['stock_after'])  ? (int) $row['stock_after']  : null,
                                        'user'         => $log->user?->name ?? 'System',
                                        'date'         => $log->created_at,
                                        'reference'    => 'Restock #' . $log->office_supply_restock_id,
                                    ]);
                                } else {
                                    $qty = (int) ($row['delivered'] ?? $row['qty'] ?? 0);
                                    if ($qty <= 0) continue;
                                    $allEntries->push([
                                        'source'       => 'restock',
                                        'action'       => $log->action === 'delivered' ? 'restock_delivered' : 'restock_partial',
                                        'label'        => e($rowLabel),
                                        'context'      => null,
                                        'qty'          => $qty,
                                        'direction'    => '+',
                                        'stock_before' => isset($row['stock_before']) ? (int) $row['stock_before'] : null,
                                        'stock_after'  => isset($row['stock_after'])  ? (int) $row['stock_after']  : null,
                                        'user'         => $log->user?->name ?? 'System',
                                        'date'         => $log->created_at,
                                        'reference'    => 'Restock #' . $log->office_supply_restock_id,
                                    ]);
                                }
                            }
                        }

                        // ── 3. RETURN LOGS (accepted → adds stock if add_to_stock) ──
                        $returnLogs = ReturnOfficeSupplyItemLog::with('user')
                            ->whereIn('action', ['returned', 'partial'])
                            ->get();

                        foreach ($returnLogs as $log) {
                            $noteData = $decodeNote($log->note);
                            if (!is_array($noteData)) continue;

                            foreach ($noteData as $row) {
                                $rowLabel = $row['label'] ?? '';
                                if (!(str_contains($rowLabel, $itemName) && str_contains($rowLabel, $variant))) continue;

                                $qty        = (int) ($row['accepted'] ?? $row['qty'] ?? 0);
                                $addToStock = (bool) ($row['add_to_stock'] ?? false);
                                if ($qty <= 0) continue;

                                if (!$addToStock) {
                                    $allEntries->push([
                                        'source'       => 'return',
                                        'action'       => 'return_no_stock',
                                        'label'        => e($rowLabel),
                                        'context'      => 'No stock update (disposal/record only)',
                                        'qty'          => $qty,
                                        'direction'    => '0',
                                        'stock_before' => null,
                                        'stock_after'  => null,
                                        'user'         => $log->user?->name ?? 'System',
                                        'date'         => $log->created_at,
                                        'reference'    => 'Return #' . $log->return_office_supply_item_id,
                                    ]);
                                } else {
                                    $allEntries->push([
                                        'source'       => 'return',
                                        'action'       => 'return_accepted',
                                        'label'        => e($rowLabel),
                                        'context'      => null,
                                        'qty'          => $qty,
                                        'direction'    => '+',
                                        'stock_before' => isset($row['stock_before']) ? (int) $row['stock_before'] : null,
                                        'stock_after'  => isset($row['stock_after'])  ? (int) $row['stock_after']  : null,
                                        'user'         => $log->user?->name ?? 'System',
                                        'date'         => $log->created_at,
                                        'reference'    => 'Return #' . $log->return_office_supply_item_id,
                                    ]);
                                }
                            }
                        }

                        // ── Sort newest first ──────────────────────────────
                        $allEntries = $allEntries->sortByDesc('date')->values();

                        if ($allEntries->isEmpty()) {
                            return new HtmlString("
                                <div style='padding:40px;text-align:center;color:#9ca3af;font-size:13px;'>
                                    No stock movement logs found for this variant.
                                </div>
                            ");
                        }

                        // ── Build rows ─────────────────────────────────────
                        $rows = '';
                        foreach ($allEntries as $entry) {
                            $date      = \Carbon\Carbon::parse($entry['date'])->timezone('Asia/Manila')->format('M d, Y h:i A');
                            $user      = e($entry['user']);
                            $reference = e($entry['reference']);
                            $label     = $entry['label'];
                            $context   = $entry['context'] ? e($entry['context']) : null;
                            $qty       = $entry['qty'];
                            $direction = $entry['direction'];
                            $action    = $entry['action'];
                            $source    = $entry['source'];

                            [$sourceBg, $sourceLabel] = match ($source) {
                                'request' => ['#b45309', 'REQUEST'],
                                'restock' => ['#16a34a', 'RESTOCK'],
                                'return'  => ['#7c3aed', 'RETURN'],
                                default   => ['#6b7280', strtoupper($source)],
                            };

                            [$actionBg, $actionLabel] = match ($action) {
                                'request_approved'  => ['#dc2626', 'APPROVED'],
                                'restock_delivered' => ['#16a34a', 'DELIVERED'],
                                'restock_partial'   => ['#d97706', 'PART. DELIVERY'],
                                'restock_returned'  => ['#dc2626', 'RETURNED'],
                                'return_accepted'   => ['#7c3aed', 'ACCEPTED'],
                                'return_no_stock'   => ['#9ca3af', 'NO STOCK CHG'],
                                default             => ['#6b7280', strtoupper(str_replace('_', ' ', $action))],
                            };

                            if ($direction === '+') {
                                $dirColor  = '#16a34a';
                                $dirSymbol = "+{$qty}";
                                $dirBg     = '#f0fdf4';
                            } elseif ($direction === '-') {
                                $dirColor  = '#dc2626';
                                $dirSymbol = "−{$qty}";
                                $dirBg     = '#fef2f2';
                            } else {
                                $dirColor  = '#9ca3af';
                                $dirSymbol = "±0";
                                $dirBg     = '#f9fafb';
                            }

                            $stockHtml = '';
                            if ($entry['stock_before'] !== null && $entry['stock_after'] !== null) {
                                $arrowColor = $direction === '+' ? '#16a34a' : ($direction === '-' ? '#dc2626' : '#9ca3af');
                                $stockHtml = "
                                    <div style='font-size:10.5px;color:#9ca3af;margin-top:3px;'>
                                        stock:
                                        <span style='color:#374151;font-weight:600;'>{$entry['stock_before']}</span>
                                        →
                                        <span style='color:{$arrowColor};font-weight:700;'>{$entry['stock_after']}</span>
                                    </div>";
                            }

                            $contextHtml = $context
                                ? "<div style='font-size:10.5px;color:#9ca3af;margin-top:2px;font-style:italic;'>{$context}</div>"
                                : '';

                            $rows .= "
                                <tr>
                                    <td style='padding:10px 12px;border-bottom:1px solid #f1f5f9;vertical-align:top;min-width:130px;'>
                                        <div style='font-size:11px;color:#374151;white-space:nowrap;'>{$date}</div>
                                        <div style='font-size:10px;color:#9ca3af;margin-top:2px;'>{$user}</div>
                                        <div style='font-size:10px;color:#6b7280;margin-top:3px;
                                            background:#f1f5f9;padding:2px 7px;border-radius:999px;display:inline-block;'>
                                            {$reference}
                                        </div>
                                    </td>
                                    <td style='padding:10px 12px;border-bottom:1px solid #f1f5f9;vertical-align:top;white-space:nowrap;'>
                                        <div>
                                            <span style='background:{$sourceBg};color:#fff;font-size:9px;font-weight:700;
                                                padding:2px 8px;border-radius:999px;letter-spacing:.04em;'>
                                                {$sourceLabel}
                                            </span>
                                        </div>
                                        <div style='margin-top:4px;'>
                                            <span style='background:{$actionBg};color:#fff;font-size:9px;font-weight:700;
                                                padding:2px 8px;border-radius:999px;letter-spacing:.04em;'>
                                                {$actionLabel}
                                            </span>
                                        </div>
                                    </td>
                                    <td style='padding:10px 12px;border-bottom:1px solid #f1f5f9;vertical-align:top;'>
                                        <div style='font-size:11.5px;color:#111827;font-weight:500;'>{$label}</div>
                                        {$contextHtml}
                                        {$stockHtml}
                                    </td>
                                    <td style='padding:10px 12px;border-bottom:1px solid #f1f5f9;vertical-align:top;text-align:center;'>
                                        <div style='display:inline-flex;align-items:center;justify-content:center;
                                            background:{$dirBg};color:{$dirColor};font-size:13px;font-weight:800;
                                            min-width:52px;padding:4px 10px;border-radius:8px;letter-spacing:-.02em;'>
                                            {$dirSymbol}
                                        </div>
                                    </td>
                                </tr>";
                        }

                        // ── Legend ─────────────────────────────────────────
                        $legendItems = [
                            ['bg' => '#b45309', 'label' => 'Request'],
                            ['bg' => '#16a34a', 'label' => 'Restock'],
                            ['bg' => '#7c3aed', 'label' => 'Return'],
                        ];
                        $legendHtml = '';
                        foreach ($legendItems as $li) {
                            $legendHtml .= "
                                <span style='display:inline-flex;align-items:center;gap:5px;margin-right:12px;'>
                                    <span style='width:8px;height:8px;border-radius:50%;background:{$li['bg']};display:inline-block;'></span>
                                    <span style='font-size:11px;color:#6b7280;'>{$li['label']}</span>
                                </span>";
                        }

                        $currentStock = (int) $record->office_supply_quantity;
                        $totalCount   = $allEntries->count();

                        return new HtmlString("
                            <div style='font-family:\"DM Sans\",system-ui,sans-serif;'>
                                <div style='display:flex;justify-content:space-between;align-items:center;
                                    padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;
                                    border-radius:10px;margin-bottom:12px;'>
                                    <div>
                                        <div style='font-size:12px;font-weight:700;color:#1e3a5f;'>
                                            {$itemName} · <span style='color:#6b7280;font-weight:400;'>{$variant}</span>
                                        </div>
                                        <div style='font-size:11px;color:#9ca3af;margin-top:2px;'>{$totalCount} log entr" . ($totalCount === 1 ? 'y' : 'ies') . "</div>
                                    </div>
                                    <div style='text-align:right;'>
                                        <div style='font-size:10px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px;'>Current Stock</div>
                                        <div style='font-size:20px;font-weight:900;color:#1e3a5f;letter-spacing:-.03em;'>{$currentStock}</div>
                                    </div>
                                </div>
                                <div style='padding:6px 0 10px;'>{$legendHtml}</div>
                                <div style='max-height:520px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:10px;'>
                                    <table style='width:100%;border-collapse:collapse;'>
                                        <thead style='position:sticky;top:0;z-index:1;'>
                                            <tr style='background:#1e3a5f;'>
                                                <th style='padding:9px 12px;text-align:left;font-size:10.5px;font-weight:600;color:#e0f2fe;text-transform:uppercase;letter-spacing:.06em;white-space:nowrap;'>Date / By</th>
                                                <th style='padding:9px 12px;text-align:left;font-size:10.5px;font-weight:600;color:#e0f2fe;text-transform:uppercase;letter-spacing:.06em;'>Source</th>
                                                <th style='padding:9px 12px;text-align:left;font-size:10.5px;font-weight:600;color:#e0f2fe;text-transform:uppercase;letter-spacing:.06em;'>Details</th>
                                                <th style='padding:9px 12px;text-align:center;font-size:10.5px;font-weight:600;color:#e0f2fe;text-transform:uppercase;letter-spacing:.06em;width:70px;'>Qty</th>
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}