<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTransactionsTable
 * 
 * Migration class for creating the transactions table.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Create the transactions table with the specified columns and constraints.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->date('date_transaction');
            $table->integer('amount');
            $table->string('note');
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Drop the transactions table.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};