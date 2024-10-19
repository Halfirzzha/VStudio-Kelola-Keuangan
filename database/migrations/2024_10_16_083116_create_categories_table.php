<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateCategoriesTable
 * 
 * Migration class for creating the categories table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Create the categories table with the specified columns.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_expense')->default(true);
            $table->string('image');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Drop the categories table.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};