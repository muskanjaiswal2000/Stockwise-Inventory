<?php

namespace Modules\Inventory\Http\Controllers\Stock;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Inventory\Services\StockReportQuery;

class History extends Component
{
    use WithPagination;

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $movementType = '';

    public function updated($property): void
    {
        if (in_array($property, ['dateFrom', 'dateTo', 'movementType'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['dateFrom', 'dateTo', 'movementType']);
        $this->resetPage();
    }

    public function render(StockReportQuery $reportQuery)
    {
        return view('inventory::livewire.stock.history', [
            'movements' => $reportQuery->build(
                $this->dateFrom ?: null,
                $this->dateTo ?: null,
                $this->movementType ?: null,
            )->paginate(15),
        ])->layout('livewire.layout.app');
    }
}
