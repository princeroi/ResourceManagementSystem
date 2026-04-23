<?php

namespace App\Filament\Resources\SmeBillings\Tables;

use App\Models\Billing;
use App\Models\SmeBilling;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class SmeBillingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purchaseOrder.po_number')
                    ->label('PO Number')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('billed_to')
                    ->label('Billed To')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('billing_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'client' => 'info',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'client' => 'Client',
                        default  => 'Other',
                    }),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('PHP')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'billed'  => 'success',
                        'pending' => 'warning',
                        default   => 'gray',
                    }),

                TextColumn::make('billed_at')
                    ->label('Billed At')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('billing_type')
                    ->options([
                        'client' => 'Client',
                        'other'  => 'Other',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'billed'  => 'Billed',
                    ]),
            ])
            ->recordActions([

                // ─── VIEW ─────────────────────────────────────────────────
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalWidth('3xl')
                    ->modalHeading(fn (SmeBilling $record) => 'SME Billing — ' . ($record->billed_to ?? '—'))
                    ->modalContent(function (SmeBilling $record) {
                        $record->loadMissing('purchaseOrder', 'creator', 'billingDrs');

                        $billedTo    = e($record->billed_to ?? '—');
                        $typeLabel   = strtoupper($record->billing_type === 'client' ? 'Client' : 'Other');
                        $typeBg      = $record->billing_type === 'client' ? '#1d4ed8' : '#6b7280';
                        $statusLabel = strtoupper($record->status);
                        $statusColor = $record->status === 'billed' ? '#16a34a' : '#d97706';
                        $total       = number_format((float) $record->total_price, 2);
                        $billedAt    = $record->billed_at?->format('F d, Y') ?? '—';
                        $createdBy   = e($record->creator?->name ?? '—');
                        $poNumber    = e($record->purchaseOrder?->po_number ?? '—');

                        // ── Billing items ──
                        $raw   = $record->billing_items;
                        $items = is_array($raw)
                            ? $raw
                            : (is_string($raw) ? json_decode($raw, true) ?? [] : []);

                        $itemRows = '';
                        if (empty($items)) {
                            $itemRows = "<tr><td colspan='5' style='padding:16px;text-align:center;color:#9ca3af;font-size:13px;'>No billing items found.</td></tr>";
                        }

                        foreach ($items as $i => $item) {
                            $name      = e($item['item_name']  ?? '—');
                            $size      = e($item['size']       ?? '—');
                            $qty       = (int)   ($item['quantity']   ?? 0);
                            $unitPrice = (float) ($item['unit_price']  ?? 0);
                            $sub       = $qty * $unitPrice;
                            $bg        = $i % 2 === 0 ? '#ffffff' : '#f8fafc';

                            $itemRows .= "
                                <tr style='background:{$bg};'>
                                    <td style='padding:9px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;color:#111827;'>{$name}</td>
                                    <td style='padding:9px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;color:#374151;text-align:center;'>{$size}</td>
                                    <td style='padding:9px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;font-weight:700;text-align:center;color:#1d4ed8;'>{$qty}</td>
                                    <td style='padding:9px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;text-align:right;color:#374151;'>&#x20B1;" . number_format($unitPrice, 2) . "</td>
                                    <td style='padding:9px 14px;border-bottom:1px solid #e5e7eb;font-size:13px;font-weight:700;text-align:right;color:#111827;'>&#x20B1;" . number_format($sub, 2) . "</td>
                                </tr>";
                        }

                        // ── DR records ──
                        $drHtml = '';
                        if ($record->billingDrs->isNotEmpty()) {
                            $drRows = '';
                            foreach ($record->billingDrs as $dr) {
                                $empName  = e($dr->employee_name ?? '—');
                                $drNum    = e($dr->dr_number     ?? '—');
                                $dateSig  = $dr->date_signed
                                    ? \Carbon\Carbon::parse($dr->date_signed)->format('M d, Y')
                                    : '—';
                                $remarks  = e($dr->remarks ?? '—');
                                $url      = $dr->dr_image
                                    ? route('private.image', [
                                        'disk' => 'local',
                                        'path' => base64_encode($dr->dr_image),
                                    ])
                                    : null;
                                $fileLink = $url
                                    ? "<a href='{$url}' target='_blank'
                                            style='color:#2563eb;font-size:12px;font-weight:600;text-decoration:none;
                                                background:#eff6ff;padding:3px 10px;border-radius:999px;border:1px solid #bfdbfe;'>
                                            &#8599; View
                                        </a>"
                                    : "<span style='color:#9ca3af;font-size:12px;'>No file</span>";

                                $drRows .= "
                                    <tr>
                                        <td style='padding:9px 14px;font-size:12.5px;color:#111827;border-bottom:1px solid #f1f5f9;font-weight:500;'>
                                            {$empName}
                                        </td>
                                        <td style='padding:9px 14px;font-size:12.5px;color:#1d4ed8;font-weight:700;border-bottom:1px solid #f1f5f9;'>
                                            {$drNum}
                                        </td>
                                        <td style='padding:9px 14px;font-size:12px;color:#374151;text-align:center;border-bottom:1px solid #f1f5f9;'>
                                            {$dateSig}
                                        </td>
                                        <td style='padding:9px 14px;font-size:12px;color:#6b7280;border-bottom:1px solid #f1f5f9;'>
                                            {$remarks}
                                        </td>
                                        <td style='padding:9px 14px;text-align:center;border-bottom:1px solid #f1f5f9;'>
                                            {$fileLink}
                                        </td>
                                    </tr>";
                            }

                            $drHtml = "
                                <div style='border:1px solid #bbf7d0;border-radius:10px;overflow:hidden;margin-bottom:16px;
                                    box-shadow:0 1px 3px rgba(0,0,0,.04);'>
                                    <div style='background:#f0fdf4;padding:10px 14px;display:flex;align-items:center;gap:8px;'>
                                        <span style='width:7px;height:7px;border-radius:50%;background:#16a34a;display:inline-block;flex-shrink:0;'></span>
                                        <span style='font-size:11px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.06em;'>
                                            Delivery Receipts
                                        </span>
                                        <span style='background:#dcfce7;color:#166534;font-size:10px;font-weight:600;
                                            padding:2px 8px;border-radius:999px;margin-left:4px;'>
                                            {$record->billingDrs->count()}
                                        </span>
                                    </div>
                                    <table style='width:100%;border-collapse:collapse;'>
                                        <thead>
                                            <tr style='background:#f8fafc;'>
                                                <th style='padding:8px 14px;font-size:10.5px;color:#6b7280;text-align:left;
                                                    font-weight:600;text-transform:uppercase;letter-spacing:.06em;'>Employee</th>
                                                <th style='padding:8px 14px;font-size:10.5px;color:#6b7280;text-align:left;
                                                    font-weight:600;text-transform:uppercase;letter-spacing:.06em;'>DR #</th>
                                                <th style='padding:8px 14px;font-size:10.5px;color:#6b7280;text-align:center;
                                                    font-weight:600;text-transform:uppercase;letter-spacing:.06em;'>Date Signed</th>
                                                <th style='padding:8px 14px;font-size:10.5px;color:#6b7280;text-align:left;
                                                    font-weight:600;text-transform:uppercase;letter-spacing:.06em;'>Remarks</th>
                                                <th style='padding:8px 14px;font-size:10.5px;color:#6b7280;text-align:center;
                                                    font-weight:600;text-transform:uppercase;letter-spacing:.06em;'>File</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$drRows}</tbody>
                                    </table>
                                </div>";
                        } else {
                            $drHtml = "
                                <div style='padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;
                                    font-size:12px;color:#9ca3af;text-align:center;margin-bottom:16px;'>
                                    No delivery receipts attached.
                                </div>";
                        }

                        return new \Illuminate\Support\HtmlString("
                            <div style='font-family:\"DM Sans\",system-ui,sans-serif;'>

                                <!-- Header card -->
                                <div style='background:linear-gradient(135deg,#1e3a5f 0%,#1e40af 100%);
                                    border-radius:12px;padding:18px 20px;margin-bottom:16px;'>
                                    <div style='display:flex;justify-content:space-between;align-items:flex-start;gap:10px;'>
                                        <div>
                                            <div style='font-size:17px;font-weight:800;color:#fff;letter-spacing:-0.02em;'>
                                                {$billedTo}
                                            </div>
                                            <div style='font-size:12px;color:#93c5fd;margin-top:3px;'>
                                                PO #{$poNumber}
                                            </div>
                                        </div>
                                        <div style='display:flex;gap:6px;flex-shrink:0;'>
                                            <span style='background:{$typeBg};color:#fff;font-size:10px;font-weight:700;
                                                padding:3px 12px;border-radius:999px;letter-spacing:.04em;'>{$typeLabel}</span>
                                            <span style='background:{$statusColor};color:#fff;font-size:10px;font-weight:700;
                                                padding:3px 12px;border-radius:999px;letter-spacing:.04em;'>{$statusLabel}</span>
                                        </div>
                                    </div>
                                    <div style='display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-top:14px;'>
                                        <div style='background:rgba(255,255,255,.1);border-radius:8px;padding:10px 14px;'>
                                            <div style='font-size:10px;color:#93c5fd;font-weight:600;text-transform:uppercase;
                                                letter-spacing:.06em;margin-bottom:3px;'>Total</div>
                                            <div style='font-size:15px;font-weight:800;color:#fff;'>&#x20B1;{$total}</div>
                                        </div>
                                        <div style='background:rgba(255,255,255,.1);border-radius:8px;padding:10px 14px;'>
                                            <div style='font-size:10px;color:#93c5fd;font-weight:600;text-transform:uppercase;
                                                letter-spacing:.06em;margin-bottom:3px;'>Billed At</div>
                                            <div style='font-size:13px;font-weight:600;color:#fff;'>{$billedAt}</div>
                                        </div>
                                        <div style='background:rgba(255,255,255,.08);border-radius:8px;padding:10px 14px;'>
                                            <div style='font-size:10px;color:#93c5fd;font-weight:600;text-transform:uppercase;
                                                letter-spacing:.06em;margin-bottom:3px;'>Created By</div>
                                            <div style='font-size:13px;font-weight:600;color:#fff;'>{$createdBy}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- DR section -->
                                {$drHtml}

                                <!-- Items table -->
                                <div style='border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;
                                    box-shadow:0 1px 3px rgba(0,0,0,.04);'>
                                    <div style='background:#f1f5f9;padding:10px 14px;display:flex;align-items:center;gap:8px;'>
                                        <span style='font-size:11px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;'>
                                            Billing Items
                                        </span>
                                        <span style='background:#e2e8f0;color:#64748b;font-size:10px;font-weight:600;
                                            padding:2px 8px;border-radius:999px;'>
                                            " . count($items) . " item(s)
                                        </span>
                                    </div>
                                    <table style='width:100%;border-collapse:collapse;'>
                                        <thead>
                                            <tr style='background:#1e3a5f;'>
                                                <th style='padding:8px 14px;text-align:left;font-size:10px;font-weight:700;
                                                    color:#e0f2fe;text-transform:uppercase;letter-spacing:.05em;'>Item</th>
                                                <th style='padding:8px 14px;text-align:center;font-size:10px;font-weight:700;
                                                    color:#e0f2fe;text-transform:uppercase;letter-spacing:.05em;width:70px;'>Size</th>
                                                <th style='padding:8px 14px;text-align:center;font-size:10px;font-weight:700;
                                                    color:#93c5fd;text-transform:uppercase;letter-spacing:.05em;width:60px;'>Qty</th>
                                                <th style='padding:8px 14px;text-align:right;font-size:10px;font-weight:700;
                                                    color:#e0f2fe;text-transform:uppercase;letter-spacing:.05em;width:110px;'>Unit Price</th>
                                                <th style='padding:8px 14px;text-align:right;font-size:10px;font-weight:700;
                                                    color:#fcd34d;text-transform:uppercase;letter-spacing:.05em;width:100px;'>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>{$itemRows}</tbody>
                                        <tfoot>
                                            <tr style='background:#eff6ff;border-top:2px solid #93c5fd;'>
                                                <td colspan='4' style='padding:10px 14px;font-size:12px;font-weight:600;
                                                    color:#374151;text-align:right;'>Grand Total</td>
                                                <td style='padding:10px 14px;font-size:15px;font-weight:800;
                                                    color:#1d4ed8;text-align:right;'>&#x20B1;{$total}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                        ");
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                // ─── ADD TO BILLING ───────────────────────────────────────
                Action::make('addToBilling')
                    ->label('Add to Billing')
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->visible(
                        fn (SmeBilling $record): bool =>
                            $record->status === 'pending' &&
                            $record->billing_type === 'client'
                    )
                    ->form([
                        Select::make('billing_id')
                            ->label('Select Pending Billing')
                            ->options(function (SmeBilling $record) {
                                // Get the client from the PO's site
                                $clientId = $record->purchaseOrder?->site?->client_id;

                                return Billing::where('status', 'pending')
                                    ->when($clientId, fn ($q) => $q->where('client_id', $clientId))
                                    ->with('client')
                                    ->get()
                                    ->mapWithKeys(fn ($billing) => [
                                        $billing->id => implode(' | ', array_filter([
                                            $billing->billing_title ?? 'No Title',
                                            $billing->client?->client_name,
                                            $billing->invoice_number ? "Inv #{$billing->invoice_number}" : null,
                                            $billing->billing_date?->format('M d, Y'),
                                        ])),
                                    ]);
                            })
                            ->native(true)
                            ->searchable()
                            ->required()
                            ->placeholder('Choose a pending billing record')
                            ->helperText(function (SmeBilling $record) {
                                $clientName = $record->purchaseOrder?->site?->client?->client_name;
                                return $clientName
                                    ? "Showing billings for client: {$clientName}"
                                    : "No client found — showing all pending billings.";
                            }),
                    ])
                    ->modalHeading('Add to Client Billing')
                    ->modalDescription('Select a pending billing record to attach this SME billing to.')
                    ->modalSubmitActionLabel('Add to Billing')
                    ->action(function ($record, array $data): void {
                        $billing = Billing::findOrFail($data['billing_id']);

                        DB::table('billings')
                            ->where('id', $billing->id)
                            ->update([
                                'total_amount' => DB::raw('COALESCE(total_amount, 0) + ' . (float) $record->total_price),
                            ]);

                        // ── Record the include log ──────────────────────────────
                        \App\Models\BillingInclude::create([
                            'billing_id'         => $billing->id,
                            'includeable_type'   => get_class($record),   // SmeBilling or UniformIssuanceBilling
                            'includeable_id'     => $record->id,
                            'amount'             => $record->total_price,
                            'label'              => $record->billed_to,
                            'included_at'      => now(),
                        ]);

                        $record->update([
                            'status'    => 'billed',
                            'billed_at' => now()->toDateString(),
                        ]);

                        Notification::make()
                            ->title('Added to Billing')
                            ->body('Successfully added ₱' . number_format((float) $record->total_price, 2) . ' to "' . $billing->billing_title . '".')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
} 