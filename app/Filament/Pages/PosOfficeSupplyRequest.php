<?php

namespace App\Filament\Pages;

use App\Models\OfficeSupplyCategory;
use App\Models\OfficeSupplyRequest;
use App\Models\OfficeSupplyRequestLog;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use App\Models\User;

class PosOfficeSupplyRequest extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'fas-shopping-cart';
    protected static ?string $navigationLabel = 'Request Supplies';
    protected static ?string $title = 'Request Supplies (POS)';
    protected static ?string $slug = 'request-supplies';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.pos-office-supply-request';

    public static function getNavigationGroup(): ?string
    {
        return 'Distributions';
    }

    public string $requestedBy = '';
    public string $note = '';
    public string $activeCategory = '';
    public array $cart = [];

    #[Computed]
    public function categories(): array
    {
        return OfficeSupplyCategory::with([
            'items' => fn($q) => $q->withoutTrashed()->with('variants'),
        ])->get()->toArray();
    }

    public function mount(): void
    {
        $first = OfficeSupplyCategory::first();
        $this->activeCategory = $first ? (string) $first->id : '';
    }

    public function setCategory(string $categoryId): void
    {
        $this->activeCategory = $categoryId;
    }

    public function addItem(int $itemId, ?int $variantId, string $name, string $variant): void
    {
        $key = $itemId . '_' . ($variantId ?? 0);

        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty']++;
        } else {
            $this->cart[$key] = [
                'item_id'    => $itemId,
                'variant_id' => $variantId,
                'name'       => $name,
                'variant'    => $variant,
                'qty'        => 1,
            ];
        }
    }

    public function changeQty(string $key, int $delta): void
    {
        if (!isset($this->cart[$key])) return;

        $this->cart[$key]['qty'] += $delta;

        if ($this->cart[$key]['qty'] <= 0) {
            unset($this->cart[$key]);
        }
    }

    public function removeItem(string $key): void
    {
        unset($this->cart[$key]);
    }

    public function submit(): void
    {
        $this->validate([
            'requestedBy' => 'required|string|max:255',
            'cart'        => 'required|array|min:1',
        ]);

        DB::transaction(function () {
            $request = OfficeSupplyRequest::create([
                'requested_by' => $this->requestedBy,
                'request_date' => now()->timezone('Asia/Manila')->toDateString(),
                'note'         => $this->note,
                'status'       => 'pending',
            ]);

            foreach ($this->cart as $key => $entry) {
                [$itemId, $variantId] = explode('_', $key);

                $request->items()->create([
                    'item_id'         => $itemId,
                    'item_variant_id' => $variantId,
                    'quantity'        => $entry['qty'],
                ]);
            }

            // ── Write creation log ────────────────────────────────────────
            $noteItems = [];
            foreach ($this->cart as $entry) {
                $noteItems[] = [
                    'label' => $entry['name'] . ' (' . $entry['variant'] . ')',
                    'qty'   => (int) $entry['qty'],
                ];
            }

            OfficeSupplyRequestLog::create([
                'office_supply_request_id' => $request->id,
                'user_id'                  => Auth::id(),
                'action'                   => 'created',
                'status_from'              => null,
                'status_to'                => 'pending',
                'note'                     => $noteItems,
            ]);
        });

        $this->cart        = [];
        $this->requestedBy = '';
        $this->note        = '';

        Notification::make()
            ->title('Request submitted!')
            ->success()
            ->send();
    }

    public function getUsersProperty()
    {
        return User::orderBy('name')->get(['id', 'name']);
    }

    public string $search = '';

    public function getCategoriesProperty(): array
    {
        $search = trim($this->search);

        return OfficeSupplyCategory::with(['items' => function ($query) use ($search) {
            if ($search !== '') {
                $query->where('office_supply_name', 'like', '%' . $search . '%');
            }
        }, 'items.variants'])
        ->get()
        ->toArray();
    }
}