<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

/**
 * Class WidgetExpenseChart
 * 
 * Widget class for displaying expense trends in a chart.
 */
class WidgetExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;

    /**
     * The heading of the widget.
     *
     * @var string|null
     */
    protected static ?string $heading = 'Pengeluaran';

    /**
     * The color of the widget.
     *
     * @var string
     */
    protected static string $color = 'danger';

    /**
     * Get the data for the chart.
     *
     * @return array
     */
    protected function getData(): array
    {
        // Parse the start date from filters or set to the start of the current month if not provided
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            Carbon::now()->startOfMonth();

        // Parse the end date from filters or set to the current date if not provided
        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        // Query the expense data and aggregate it per day
        $data = Trend::query(Transaction::expenses()->newQuery())
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->sum('amount');

        // Calculate the trend direction
        $trendDirection = $data->map(fn (TrendValue $value) => $value->aggregate)->toArray();
        $sortedTrendDirection = $trendDirection;
        sort($sortedTrendDirection);
        $isIncreasing = $trendDirection === $sortedTrendDirection;

        // Set chart color based on trend direction
        $backgroundColor = $isIncreasing ? 'rgba(75, 192, 192, 0.2)' : 'rgba(255, 99, 132, 0.2)'; // Green if increasing, red if decreasing
        $borderColor = $isIncreasing ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)'; // Green if increasing, red if decreasing

        // Return the chart data
        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran Per Hari',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
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
        return 'line';
    }
}