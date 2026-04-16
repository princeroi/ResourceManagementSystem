<?php

namespace App\Filament\Resources\ReturnUniformItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use App\Models\UniformItemVariants;
use App\Models\ReturnUniformItemLog;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ReturnUniformItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site.site_name')
                    ->label('Site')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('returned_by')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('received_by')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'warning',
                        'partial'   => 'info',
                        'returned'  => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('status_date')
                    ->label('Date')
                    ->date()
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderByRaw("
                            CASE status
                                WHEN 'pending'   THEN pending_at
                                WHEN 'partial'   THEN partial_at
                                WHEN 'returned'  THEN returned_at
                                WHEN 'cancelled' THEN cancelled_at
                                ELSE NULL
                            END {$direction}
                        ");
                    })
                    ->getStateUsing(fn ($record) => match ($record->status) {
                        'pending'   => $record->pending_at,
                        'partial'   => $record->partial_at,
                        'returned'  => $record->returned_at,
                        'cancelled' => $record->cancelled_at,
                        default     => null,
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
            ->filters([
                //
            ])
            ->recordActions([

                // ─── VIEW ──────────────────────────────────────────────────
                Action::make('view')
                    ->label('View')
                    ->color('gray')
                    ->icon('heroicon-o-eye')
                    ->modalWidth('3xl')
                    ->modalHeading(fn ($record) => 'Return — ' . ($record->site?->site_name ?? '—'))
                    ->modalContent(function ($record) {
                        $record->loadMissing(
                            'returnUniformItemLine.uniformItem',
                            'returnUniformItemLine.uniformItemVariant',
                            'site'
                        );

                        $returnedBy = e($record->returned_by);
                        $receivedBy = e($record->received_by);
                        $notes      = e($record->notes ?? '—');
                        $siteName   = e($record->site?->site_name ?? '—');

                        $statusColor = match ($record->status) {
                            'returned'  => '#16a34a',
                            'partial'   => '#d97706',
                            'pending'   => '#2563eb',
                            'cancelled' => '#dc2626',
                            default     => '#6b7280',
                        };
                        $statusLabel = strtoupper($record->status ?? 'PENDING');

                        $statusDate = match ($record->status) {
                            'pending'   => $record->pending_at,
                            'partial'   => $record->partial_at,
                            'returned'  => $record->returned_at,
                            'cancelled' => $record->cancelled_at,
                            default     => null,
                        };
                        $statusDateFormatted = $statusDate
                            ? \Carbon\Carbon::parse($statusDate)->format('F d, Y')
                            : '—';

                        // ── Line item rows ────────────────────────────────
                        $rows     = '';
                        $totalQty = 0;
                        $totalRet = 0;
                        $totalRem = 0;

                        foreach ($record->returnUniformItemLine as $i => $line) {
                            $itemName   = e($line->uniformItem?->uniform_item_name ?? '—');
                            $size       = e($line->uniformItemVariant?->uniform_item_size ?? '—');
                            $employee   = e($line->employee_name ?? '—');
                            $condition  = ucfirst($line->condition ?? '—');
                            $reason     = ucfirst(str_replace('_', ' ', $line->reason ?? '—'));
                            $qty        = (int) $line->quantity;
                            $returned   = (int) $line->returned_quantity;
                            $remaining  = (int) $line->remaining_quantity;
                            $addToStock = (bool) $line->add_to_stock;

                            $totalQty += $qty;
                            $totalRet += $returned;
                            $totalRem += $remaining;

                            $retColor = $returned  > 0 ? '#16a34a' : '#9ca3af';
                            $remColor = $remaining > 0 ? '#d97706' : '#9ca3af';
                            $bg       = $i % 2 === 0 ? '#ffffff' : '#f8fafc';

                            $conditionColor = match ($line->condition) {
                                'good'      => '#16a34a',
                                'damaged'   => '#d97706',
                                'defective' => '#dc2626',
                                default     => '#6b7280',
                            };

                            $stockBadge = $addToStock
                                ? "<span style='background:#dcfce7;color:#166534;font-size:9px;font-weight:700;
                                    padding:2px 7px;border-radius:999px;white-space:nowrap;'>+ STOCK</span>"
                                : "<span style='background:#fef9c3;color:#854d0e;font-size:9px;font-weight:700;
                                    padding:2px 7px;border-radius:999px;white-space:nowrap;'>NO STOCK</span>";

                            $rows .= "
                                <tr style='background:{$bg};'>
                                    <td style='padding:9px 12px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#111827;font-weight:500;'>
                                        {$itemName}
                                        <div style='font-size:10px;color:#9ca3af;margin-top:2px;'>{$employee}</div>
                                    </td>
                                    <td style='padding:9px 12px;border-bottom:1px solid #e5e7eb;font-size:12px;text-align:center;color:#374151;'>{$size}</td>
                                    <td style='padding:9px 12px;border-bottom:1px solid #e5e7eb;font-size:11px;text-align:center;'>
                                        <span style='color:{$conditionColor};font-weight:600;'>{$condition}</span>
                                        <div style='margin-top:3px;'>{$stockBadge}</div>
                                    </td>
                                    <td style='padding:9px 12px;border-bottom:1px solid #e5e7eb;font-size:12px;font-weight:700;text-align:center;color:#1d4ed8;'>{$qty}</td>
                                    <td style='padding:9px 12px;border-bottom:1px solid #e5e7eb;font-size:12px;font-weight:700;text-align:center;color:{$retColor};'>{$returned}</td>
                                    <td style='padding:9px 12px;border-bottom:1px solid #e5e7eb;font-size:12px;font-weight:700;text-align:center;color:{$remColor};'>{$remaining}</td>
                                </tr>";
                        }

                        // totals footer
                        $rows .= "
                            <tr style='background:#eff6ff;border-top:2px solid #93c5fd;'>
                                <td colspan='3' style='padding:7px 12px;font-size:11px;font-weight:700;color:#374151;text-align:right;'>TOTAL</td>
                                <td style='padding:7px 12px;font-size:14px;font-weight:900;color:#1d4ed8;text-align:center;'>{$totalQty}</td>
                                <td style='padding:7px 12px;font-size:14px;font-weight:900;color:#16a34a;text-align:center;'>{$totalRet}</td>
                                <td style='padding:7px 12px;font-size:14px;font-weight:900;color:#d97706;text-align:center;'>{$totalRem}</td>
                            </tr>";

                        return new HtmlString("
                            <div style='font-family:\"DM Sans\",system-ui,sans-serif;'>

                                <!-- Header card -->
                                <div style='background:linear-gradient(135deg,#1e3a5f 0%,#1e40af 100%);
                                    border-radius:12px;padding:18px 20px;margin-bottom:16px;'>
                                    <div style='display:flex;justify-content:space-between;align-items:flex-start;'>
                                        <div>
                                            <div style='font-size:18px;font-weight:800;color:#fff;letter-spacing:-0.02em;'>
                                                {$siteName}
                                            </div>
                                            <div style='font-size:12px;color:#93c5fd;margin-top:4px;'>Uniform Return</div>
                                        </div>
                                        <span style='background:{$statusColor};color:#fff;font-size:11px;font-weight:700;
                                            padding:4px 14px;border-radius:999px;letter-spacing:.04em;white-space:nowrap;'>
                                            {$statusLabel}
                                        </span>
                                    </div>
                                    <div style='display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:14px;'>
                                        <div style='background:rgba(255,255,255,.1);border-radius:8px;padding:10px 14px;'>
                                            <div style='font-size:10px;color:#93c5fd;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;'>Returned By</div>
                                            <div style='font-size:13px;font-weight:600;color:#fff;'>{$returnedBy}</div>
                                        </div>
                                        <div style='background:rgba(255,255,255,.1);border-radius:8px;padding:10px 14px;'>
                                            <div style='font-size:10px;color:#93c5fd;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;'>Received By</div>
                                            <div style='font-size:13px;font-weight:600;color:#fff;'>{$receivedBy}</div>
                                        </div>
                                        <div style='background:rgba(255,255,255,.08);border-radius:8px;padding:10px 14px;'>
                                            <div style='font-size:10px;color:#93c5fd;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;'>Status Date</div>
                                            <div style='font-size:13px;font-weight:600;color:#fff;'>{$statusDateFormatted}</div>
                                        </div>
                                        <div style='background:rgba(255,255,255,.08);border-radius:8px;padding:10px 14px;'>
                                            <div style='font-size:10px;color:#93c5fd;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;'>Notes</div>
                                            <div style='font-size:12px;font-weight:500;color:#fff;'>{$notes}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grand total bar -->
                                <div style='border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:16px;'>
                                    <div style='background:#f1f5f9;padding:9px 14px;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;'>
                                        Summary
                                    </div>
                                    <div style='display:grid;grid-template-columns:1fr 1fr 1fr;'>
                                        <div style='padding:12px 14px;text-align:center;border-right:1px solid #f1f5f9;'>
                                            <div style='font-size:10px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;'>Total Qty</div>
                                            <div style='font-size:20px;font-weight:900;color:#1d4ed8;'>{$totalQty}</div>
                                        </div>
                                        <div style='padding:12px 14px;text-align:center;border-right:1px solid #f1f5f9;'>
                                            <div style='font-size:10px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;'>Returned</div>
                                            <div style='font-size:20px;font-weight:900;color:#16a34a;'>{$totalRet}</div>
                                        </div>
                                        <div style='padding:12px 14px;text-align:center;'>
                                            <div style='font-size:10px;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;'>Remaining</div>
                                            <div style='font-size:20px;font-weight:900;color:#d97706;'>{$totalRem}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Line items table -->
                                <div style='border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;'>
                                    <div style='background:#f1f5f9;padding:9px 14px;font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;'>
                                        Return Items
                                    </div>
                                    <table style='width:100%;border-collapse:collapse;'>
                                        <thead>
                                            <tr style='background:#1e3a5f;'>
                                                <th style='padding:7px 12px;text-align:left;font-size:10px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.05em;'>Item / Employee</th>
                                                <th style='padding:7px 12px;text-align:center;font-size:10px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.05em;width:55px;'>Size</th>
                                                <th style='padding:7px 12px;text-align:center;font-size:10px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:.05em;width:90px;'>Condition</th>
                                                <th style='padding:7px 12px;text-align:center;font-size:10px;font-weight:700;color:#93c5fd;text-transform:uppercase;letter-spacing:.05em;width:55px;'>Qty</th>
                                                <th style='padding:7px 12px;text-align:center;font-size:10px;font-weight:700;color:#86efac;text-transform:uppercase;letter-spacing:.05em;width:75px;'>Returned</th>
                                                <th style='padding:7px 12px;text-align:center;font-size:10px;font-weight:700;color:#fcd34d;text-transform:uppercase;letter-spacing:.05em;width:80px;'>Remaining</th>
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

                // ─── EDIT: only when pending ───────────────────────────────
                EditAction::make()
                    ->visible(fn ($record) => $record->status === 'pending'),

                // ─── ACCEPT / PROCESS RETURN: pending or partial ───────────
                Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->modalWidth('2xl')
                    ->modalHeading('Accept Return Items')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'partial']))
                    ->form(function ($record) {
                        $fields = [];

                        foreach ($record->returnUniformItemLine as $line) {
                            $remaining = (int) $line->remaining_quantity;
                            if ($remaining <= 0) continue;

                            $itemName   = $line->uniformItem?->uniform_item_name ?? '—';
                            $size       = $line->uniformItemVariant?->uniform_item_size ?? '—';
                            $employee   = $line->employee_name ? " [{$line->employee_name}]" : '';
                            $addToStock = (bool) $line->add_to_stock;

                            $stockNote = $addToStock
                                ? ' ✅ adds to stock'
                                : ' ⚠️ NO stock update';

                            $fields[] = Placeholder::make("line_{$line->id}_info")
                                ->label('')
                                ->content(new HtmlString(
                                    "<div style='padding:6px 10px;margin-top:4px;border-radius:6px;font-size:12px;font-weight:500;
                                        background:" . ($addToStock ? '#f0fdf4' : '#fef9c3') . ";
                                        border:1px solid " . ($addToStock ? '#bbf7d0' : '#fde68a') . ";
                                        color:" . ($addToStock ? '#166534' : '#854d0e') . ";'>
                                        {$stockNote}
                                    </div>"
                                ))
                                ->columnSpanFull();

                            $fields[] = TextInput::make("line_{$line->id}_accept")
                                ->label("{$itemName} — {$size}{$employee} (remaining: {$remaining})")
                                ->numeric()
                                ->default($remaining)
                                ->minValue(0)
                                ->maxValue($remaining)
                                ->required();
                        }

                        return $fields;
                    })
                    ->action(function ($record, array $data, Action $action) {
                        $totalReturned  = 0;
                        $totalRemaining = 0;
                        $note           = [];

                        foreach ($record->returnUniformItemLine as $line) {
                            $acceptQty  = (int) ($data["line_{$line->id}_accept"] ?? 0);
                            $addToStock = (bool) $line->add_to_stock;

                            if ($acceptQty > 0) {
                                $line->update([
                                    'returned_quantity'  => (int) $line->returned_quantity + $acceptQty,
                                    'remaining_quantity' => max(0, (int) $line->remaining_quantity - $acceptQty),
                                ]);

                                $stockBefore = null;
                                $stockAfter  = null;

                                // ── Only add to stock if flag is true ──────
                                if ($addToStock) {
                                    $variant = UniformItemVariants::find($line->uniform_item_variant_id);
                                    if ($variant) {
                                        $stockBefore = (int) $variant->uniform_item_quantity;
                                        $variant->increment('uniform_item_quantity', $acceptQty);
                                        $stockAfter = $stockBefore + $acceptQty;
                                    }
                                }

                                $note[] = [
                                    'label'        => ($line->uniformItem?->uniform_item_name ?? '—')
                                                    . ' (' . ($line->uniformItemVariant?->uniform_item_size ?? '—') . ')'
                                                    . ($line->employee_name ? ' — ' . $line->employee_name : ''),
                                    'accepted'     => $acceptQty,
                                    'add_to_stock' => $addToStock,
                                    'stock_before' => $stockBefore,
                                    'stock_after'  => $stockAfter,
                                ];
                            }

                            $line->refresh();
                            $totalReturned  += (int) $line->returned_quantity;
                            $totalRemaining += (int) $line->remaining_quantity;
                        }

                        // ── Determine new status ───────────────────────────
                        if ($totalRemaining === 0) {
                            $newStatus = 'returned';
                        } elseif ($totalReturned === 0) {
                            $newStatus = 'pending';
                        } else {
                            $newStatus = 'partial';
                        }

                        $record->update([
                            'status'      => $newStatus,
                            'returned_at' => $newStatus === 'returned' ? now()->toDateString() : null,
                            'partial_at'  => $newStatus === 'partial'  ? now()->toDateString() : null,
                        ]);

                        ReturnUniformItemLog::create([
                            'return_uniform_item_id' => $record->id,
                            'user_id'                => Auth::id(),
                            'action'                 => $newStatus,
                            'status_from'            => $record->getOriginal('status'),
                            'status_to'              => $newStatus,
                            'note'                   => json_encode($note),
                        ]);

                        if ($newStatus === 'returned') {
                            Notification::make()
                                ->title('Fully Returned')
                                ->body('All items have been accepted and processed.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Partially Accepted')
                                ->body('Some items accepted. Remaining items still pending.')
                                ->warning()
                                ->send();
                        }
                    }),

                // ─── CANCEL: only when pending ─────────────────────────────
                Action::make('cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update([
                            'status'       => 'cancelled',
                            'cancelled_at' => now()->toDateString(),
                        ]);

                        ReturnUniformItemLog::create([
                            'return_uniform_item_id' => $record->id,
                            'user_id'                => Auth::id(),
                            'action'                 => 'cancelled',
                            'status_from'            => $record->status,
                            'status_to'              => 'cancelled',
                            'note'                   => 'Return was cancelled.',
                        ]);

                        Notification::make()
                            ->title('Cancelled')
                            ->body('Return has been cancelled.')
                            ->danger()
                            ->send();
                    }),

                // ─── LOGS ──────────────────────────────────────────────────
                Action::make('view_logs')
                    ->label('Logs')
                    ->color('gray')
                    ->icon('heroicon-o-clock')
                    ->modalWidth('2xl')
                    ->modalHeading('Return Logs')
                    ->modalContent(function ($record) {
                        $logs = ReturnUniformItemLog::where('return_uniform_item_id', $record->id)
                            ->with('user')
                            ->latest()
                            ->get();

                        $rows = $logs->map(function ($log) {
                            $user   = $log->user?->name ?? 'System';
                            $date   = \Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A');
                            $from   = $log->status_from ?? '—';
                            $to     = $log->status_to   ?? '—';
                            $action = strtoupper($log->action);

                            $badgeColor = match ($log->action) {
                                'returned'  => '#16a34a',
                                'partial'   => '#d97706',
                                'cancelled' => '#dc2626',
                                'created'   => '#7c3aed',
                                'edited'    => '#0891b2',
                                default     => '#6b7280',
                            };

                            $itemsHtml = '';

                            if (!empty($log->note)) {
                                $noteData = json_decode($log->note, true);

                                if (is_array($noteData)) {
                                    if (in_array($log->action, ['returned', 'partial'])) {
                                        $itemsHtml  = "<div style='margin-top:6px;padding:6px 8px;background:#f0fdf4;border-radius:6px;border:1px solid #bbf7d0;'>";
                                        $itemsHtml .= "<div style='font-size:10px;font-weight:700;color:#166534;margin-bottom:4px;'>ITEMS ACCEPTED:</div>";

                                        foreach ($noteData as $row) {
                                            $label      = e($row['label'] ?? '—');
                                            $accepted   = (int) ($row['accepted'] ?? 0);
                                            $addToStock = (bool) ($row['add_to_stock'] ?? false);
                                            $stockBefore = isset($row['stock_before']) ? (int) $row['stock_before'] : null;
                                            $stockAfter  = isset($row['stock_after'])  ? (int) $row['stock_after']  : null;

                                            // Stock movement HTML
                                            $stockHtml = '';
                                            if ($addToStock && $stockBefore !== null && $stockAfter !== null) {
                                                $stockHtml = "
                                                    <span style='font-size:10px;color:#9ca3af;margin-left:6px;'>
                                                        stock: <span style='color:#374151;font-weight:600;'>{$stockBefore}</span>
                                                        → <span style='color:#16a34a;font-weight:700;'>{$stockAfter}</span>
                                                        <span style='color:#9ca3af;'>(+{$accepted})</span>
                                                    </span>";
                                            }

                                            // Stock flag badge
                                            $stockFlagBadge = $addToStock
                                                ? "<span style='background:#dcfce7;color:#166534;font-size:9px;font-weight:700;
                                                    padding:1px 6px;border-radius:999px;margin-left:4px;'>+ STOCK</span>"
                                                : "<span style='background:#fef9c3;color:#854d0e;font-size:9px;font-weight:700;
                                                    padding:1px 6px;border-radius:999px;margin-left:4px;'>NO STOCK</span>";

                                            $itemsHtml .= "
                                                <div style='font-size:11px;color:#374151;padding:4px 0;border-bottom:1px dashed #d1fae5;'>
                                                    <div style='display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:4px;'>
                                                        <span>{$label}{$stockFlagBadge}</span>
                                                        <span style='font-weight:700;color:#16a34a;'>×{$accepted}</span>
                                                    </div>
                                                    <div>{$stockHtml}</div>
                                                </div>";
                                        }

                                        $itemsHtml .= "</div>";

                                    } else {
                                        $itemsHtml = "<div style='margin-top:4px;font-size:11px;color:#6b7280;font-style:italic;'>"
                                            . e($log->note) . "</div>";
                                    }
                                } else {
                                    $itemsHtml = "<div style='margin-top:4px;font-size:11px;color:#6b7280;font-style:italic;'>"
                                        . e($log->note) . "</div>";
                                }
                            }

                            return "
                                <tr>
                                    <td style='padding:10px;border-bottom:1px solid #e5e7eb;vertical-align:top;'>
                                        <div style='font-size:11px;color:#374151;white-space:nowrap;'>{$date}</div>
                                        <div style='font-size:10px;color:#9ca3af;margin-top:2px;'>{$user}</div>
                                    </td>
                                    <td style='padding:10px;border-bottom:1px solid #e5e7eb;vertical-align:top;'>
                                        <span style='background:{$badgeColor};color:#fff;font-size:10px;font-weight:700;
                                            padding:2px 8px;border-radius:999px;'>{$action}</span>
                                    </td>
                                    <td style='padding:10px;border-bottom:1px solid #e5e7eb;vertical-align:top;'>
                                        <div style='font-size:11px;color:#374151;'>{$from} → {$to}</div>
                                        {$itemsHtml}
                                    </td>
                                </tr>";
                        })->implode('');

                        return new HtmlString("
                            <div style='max-height:500px;overflow-y:auto;'>
                                <table style='width:100%;border-collapse:collapse;'>
                                    <thead style='position:sticky;top:0;z-index:1;'>
                                        <tr style='background:#1e3a5f;'>
                                            <th style='padding:8px 10px;text-align:left;font-size:11px;color:#fff;white-space:nowrap;'>Date / By</th>
                                            <th style='padding:8px 10px;text-align:left;font-size:11px;color:#fff;'>Action</th>
                                            <th style='padding:8px 10px;text-align:left;font-size:11px;color:#fff;'>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>{$rows}</tbody>
                                </table>
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