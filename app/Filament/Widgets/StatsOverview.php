<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;

/**
 * Class StatsOverview
 * 
 * Widget class for displaying statistical overview of transactions.
 */
class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    /**
     * Get the statistics to be displayed in the widget.
     *
     * @return array
     */
    protected function getStats(): array
    {
        // Parse the start date from filters or set to null if not provided
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        // Parse the end date from filters or set to current date if not provided
        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        // Calculate total income within the date range
        $pemasukan = Transaction::incomes()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        // Calculate total expenses within the date range
        $pengeluaran = Transaction::expenses()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        // Return the statistics as an array of Stat objects
        return [
            Stat::make('Total Pemasukan', 'Rp. ' . number_format($pemasukan, 0, ',', '.')),
            Stat::make('Total Pengeluaran', 'Rp. ' . number_format($pengeluaran, 0, ',', '.')),
            Stat::make('Selisih', 'Rp. ' . number_format($pemasukan - $pengeluaran, 0, ',', '.')),
        ];
    }
}