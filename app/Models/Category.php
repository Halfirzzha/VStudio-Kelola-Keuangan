<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 * 
 * Represents a category for transactions, indicating whether it is an expense or income.
 *
 * @property string $name
 * @property bool $is_expense
 * @property string|null $image
 */
class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'is_expense',
        'image',
    ];
}