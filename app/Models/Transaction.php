<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Class Transaction
 * 
 * Represents a financial transaction, which can be either an expense or an income.
 *
 * @property string $name
 * @property int $category_id
 * @property Carbon $date_transaction
 * @property float $amount
 * @property string|null $note
 * @property string|null $image
 * @property-read string $formatted_amount
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category_id',
        'date_transaction',
        'amount',
        'note',
        'image',
    ];

    /**
     * Get the category that owns the transaction.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to only include expense transactions.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeExpenses(Builder $query): Builder
    {
        return $query->whereHas('category', function (Builder $query) {
            $query->where('is_expense', true);
        });
    }

    /**
     * Scope a query to only include income transactions.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIncomes(Builder $query): Builder
    {
        return $query->whereHas('category', function (Builder $query) {
            $query->where('is_expense', false);
        });
    }

    /**
     * Get the formatted amount attribute.
     *
     * @return string
     */
    public function getFormattedAmountAttribute(): string
{
    // Ensure the amount is a valid number
    $amount = $this->amount ?? 0;

    // Format the amount to Indonesian Rupiah currency format
    return 'Rp ' . number_format($amount, 2, ',', '.');
}

    /**
     * Set the date_transaction attribute.
     *
     * @param string $value
     * @return void
     */
    public function setDateTransactionAttribute($value): void
    {
        $this->attributes['date_transaction'] = Carbon::parse($value);
    }

    /**
     * Get the date_transaction attribute.
     *
     * @param string $value
     * @return Carbon
     */
    public function getDateTransactionAttribute($value): Carbon
    {
        return Carbon::parse($value);
    }
}