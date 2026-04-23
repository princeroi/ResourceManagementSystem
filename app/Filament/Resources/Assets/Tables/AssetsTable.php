<?php

namespace App\Filament\Resources\Assets\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property_tag')
                    ->label('Tag')
                    ->searchable()
                    ->sortable()
                    ->default('—'),

                ImageColumn::make('image')
                    ->circular(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->default('—'),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->sortable()
                    ->default('—'),

                TextColumn::make('brand')
                    ->searchable()
                    ->toggleable()
                    ->default('—'),

                TextColumn::make('model')
                    ->searchable()
                    ->toggleable()
                    ->default('—'),

                TextColumn::make('serial_number')
                    ->searchable()
                    ->toggleable()
                    ->default('—'),

                TextColumn::make('activeAssignment.location')
                    ->label('Location')
                    ->searchable()
                    ->toggleable()
                    ->default('—'),

                TextColumn::make('condition')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new', 'good'        => 'success',
                        'fair', 'for_repair' => 'warning',
                        'poor', 'condemned'  => 'danger',
                        default              => 'gray',
                    })
                    ->default('—'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available'         => 'success',
                        'assigned'          => 'info',
                        'under_maintenance' => 'warning',
                        'disposed'          => 'danger',
                        default             => 'gray',
                    })
                    ->default('—'),

                TextColumn::make('activeAssignment.assigned_to')
                    ->label('Assigned to')
                    ->default('—'),

                TextColumn::make('lifecycle_stage')
                    ->badge()
                    ->toggleable()
                    ->default('—'),

                TextColumn::make('acquisition_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('acquisition_cost')
                    ->money('PHP')
                    ->sortable()
                    ->toggleable()
                    ->default('—'),

                TextColumn::make('warranty_expiry_date')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'available'         => 'Available',
                        'assigned'          => 'Assigned',
                        'under_maintenance' => 'Under maintenance',
                        'disposed'          => 'Disposed',
                    ]),

                SelectFilter::make('condition')
                    ->options([
                        'new'        => 'New',
                        'good'       => 'Good',
                        'fair'       => 'Fair',
                        'poor'       => 'Poor',
                        'for_repair' => 'For repair',
                        'condemned'  => 'Condemned',
                    ]),

                SelectFilter::make('asset_category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),

                SelectFilter::make('lifecycle_stage')
                    ->options([
                        'active'      => 'Active',
                        'end_of_life' => 'End of life',
                        'disposed'    => 'Disposed',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),

                Action::make('return')
                    ->label('Return')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->visible(fn ($record) => $record->status === 'assigned')
                    ->requiresConfirmation()
                    ->modalHeading('Return Asset')
                    ->modalDescription(fn ($record) => 'Return "' . $record->name . '" from ' . ($record->activeAssignment?->assigned_to ?? 'current assignee') . '?')
                    ->modalSubmitActionLabel('Confirm Return')
                    ->action(function ($record): void {
                        $record->assignments()
                            ->whereNull('returned_date')
                            ->update(['returned_date' => now()]);

                        $record->update(['status' => 'available']);
                    }),

                Action::make('assign')
                    ->label('Assign')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn ($record) => $record->status !== 'assigned')
                    ->form(fn (Schema $schema) => $schema->components([

                        TextInput::make('assigned_to')
                            ->label('Assigned to (name)')
                            ->required(),

                        TextInput::make('department'),

                        TextInput::make('location'),

                        DatePicker::make('assigned_date')
                            ->default(now())
                            ->required(),

                        Textarea::make('remarks'),
                    ]))
                    ->action(function (array $data, $record): void {
                        // Close any active assignment
                        $record->assignments()
                            ->whereNull('returned_date')
                            ->update(['returned_date' => now()]);

                        // Create new assignment
                        $record->assignments()->create($data);

                        $record->update(['status' => 'assigned']);
                    }),

                Action::make('transfer')
                    ->label('Transfer')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'assigned')
                    ->fillForm(fn ($record): array => [
                        'transferred_from' => $record->activeAssignment?->assigned_to,
                        'from_location'    => $record->activeAssignment?->location ?? $record->location,
                    ])
                    ->form(fn (Schema $schema) => $schema->components([
                        TextInput::make('transferred_from')
                            ->label('From (person / dept)')
                            ->required(),

                        TextInput::make('transferred_to')
                            ->label('To (person / dept)')
                            ->required(),

                        TextInput::make('from_location')
                            ->label('From location'),

                        TextInput::make('to_location')
                            ->label('To location'),

                        DatePicker::make('transfer_date')
                            ->default(now())
                            ->required(),

                        TextInput::make('transferred_by')
                            ->label('Transferred by'),

                        Textarea::make('reason'),
                    ]))
                    ->action(function (array $data, $record): void {
                        $record->transfers()->create($data);

                        $record->assignments()
                            ->whereNull('returned_date')
                            ->update(['returned_date' => $data['transfer_date']]);

                        $record->assignments()->create([
                            'assigned_to'   => $data['transferred_to'],
                            'department'    => $data['transferred_to'],
                            'location'      => $data['to_location'] ?? $record->location,
                            'assigned_date' => $data['transfer_date'],
                            'remarks'       => 'Transferred from: ' . $data['transferred_from']
                                            . ($data['reason'] ? ' — ' . $data['reason'] : ''),
                        ]);

                        if (! empty($data['to_location'])) {
                            $record->update(['location' => $data['to_location']]);
                        }

                        $record->update(['status' => 'assigned']);
                    }),

                Action::make('maintenance')
                    ->label('Maintenance')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->color('success')
                    ->form(fn (Schema $schema) => $schema->components([
                        Select::make('type')
                            ->options([
                                'preventive'  => 'Preventive',
                                'corrective'  => 'Corrective',
                                'inspection'  => 'Inspection',
                                'calibration' => 'Calibration',
                            ])
                            ->required(),

                        DatePicker::make('maintenance_date')
                            ->default(now())
                            ->required(),

                        DatePicker::make('completed_date'),

                        TextInput::make('performed_by'),

                        TextInput::make('cost')
                            ->numeric()
                            ->prefix('₱'),

                        Select::make('status')
                            ->options([
                                'scheduled'   => 'Scheduled',
                                'in_progress' => 'In progress',
                                'completed'   => 'Completed',
                            ])
                            ->default('scheduled')
                            ->required(),

                        Textarea::make('description'),

                        Textarea::make('remarks'),
                    ]))
                    ->action(function (array $data, $record): void {
                        $record->maintenances()->create($data);

                        $record->update([
                            'status' => $data['status'] === 'completed'
                                ? 'available'
                                : 'under_maintenance',
                        ]);
                    }),

                Action::make('dispose')
                    ->label('Dispose')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form(fn (Schema $schema) => $schema->components([
                        Select::make('disposal_type')
                            ->options([
                                'auction'   => 'Auction',
                                'donation'  => 'Donation',
                                'scrap'     => 'Scrap',
                                'write_off' => 'Write-off',
                                'sale'      => 'Sale',
                            ])
                            ->required(),

                        DatePicker::make('disposal_date')
                            ->default(now())
                            ->required(),

                        TextInput::make('disposed_by')
                            ->required(),

                        TextInput::make('recipient')
                            ->helperText('Leave blank if scrapped or written off'),

                        TextInput::make('disposal_value')
                            ->numeric()
                            ->prefix('₱'),

                        Textarea::make('remarks'),
                    ]))
                    ->action(function (array $data, $record): void {
                        $record->disposals()->create($data);

                        $record->update([
                            'status'          => 'disposed',
                            'lifecycle_stage' => 'disposed',
                        ]);
                    }),

                Action::make('incident')
                    ->label('Incident')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('gray')
                    ->form(fn (Schema $schema) => $schema->components([
                        TextInput::make('reported_by')
                            ->required(),

                        DatePicker::make('reported_date')
                            ->default(now())
                            ->required(),

                        Select::make('severity')
                            ->options([
                                'low'      => 'Low',
                                'medium'   => 'Medium',
                                'high'     => 'High',
                                'critical' => 'Critical',
                            ])
                            ->default('medium')
                            ->required(),

                        Select::make('status')
                            ->options([
                                'open'        => 'Open',
                                'in_progress' => 'In progress',
                                'resolved'    => 'Resolved',
                            ])
                            ->default('open')
                            ->required(),

                        Textarea::make('description')
                            ->required(),

                        Textarea::make('resolution_notes'),

                        DatePicker::make('resolved_date'),
                    ]))
                    ->action(function (array $data, $record): void {
                        $record->incidents()->create($data);
                    }),

                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    BulkAction::make('print_property_tags')
                        ->label('Print Property Tags')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->action(function (Collection $records, \Livewire\Component $livewire): void {
                            $ids = $records->pluck('id')->join(',');
                            $livewire->dispatch('openPropertyTagPrint', ids: $ids);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}