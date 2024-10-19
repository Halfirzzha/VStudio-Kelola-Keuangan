<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

/**
 * Class WidgetIncomeChart
 * 
 * Widget class for displaying income trends in a chart.
 */
class WidgetIncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    /**
     * The heading of the widget.
     *
     * @var string|null
     */
    protected static ?string $heading = 'Pemasukan';

    /**
     * The color of the widget.
     *
     * @var string
     */
    protected static string $color = 'success';

    /**
     * Get the data for the chart.
     *
     * @return array
     */
    protected function getData(): array
    {
        // Parse the start date from filters or set to null if not provided
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        // Parse the end date from filters or set to current date if not provided
        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        // Query the income data and aggregate it per month
        $data = Trend::query(Transaction::incomes()->newQuery())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('amount');

        // Return the chart data
        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan Per Bulan',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Transparent green background color
                    'borderColor' => 'rgba(75, 192, 192, 1)', // Green border color
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    /**
     * Get the type of the chart.
     *
     * @return string
     */
    protected function getType(): string
    {
        return 'bar';
    }
}