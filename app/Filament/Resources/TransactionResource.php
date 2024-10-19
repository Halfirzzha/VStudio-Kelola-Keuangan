<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Class TransactionResource
 * 
 * Resource class for managing transactions in the Filament admin panel.
 */
class TransactionResource extends Resource
{
    /**
     * The model associated with the resource.
     *
     * @var string|null
     */
    protected static ?string $model = Transaction::class;

    /**
     * The icon used for navigation.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Define the form schema for the resource.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->label('Nama'),

            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->options(function () {
                    return \App\Models\Category::query()
                        ->orderByRaw("FIELD(is_expense, 0, 1)")
                        ->get()
                        ->mapWithKeys(function ($category) {
                            $type = $category->is_expense ? '( Pengeluaran )' : '( Pemasukan )';
                            return [$category->id => "$category->name - $type"];
                        });
                })
                ->required()
                ->label('Kategori'),

            Forms\Components\DatePicker::make('date_transaction')
                ->required()
                ->label('Tanggal Transaksi'),

            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->prefix('Rp')
                ->extraAttributes(['oninput' => 'formatCurrency(this)'])
                ->label('Jumlah'),

            Forms\Components\TextInput::make('note')
                ->required()
                ->maxLength(255)
                ->label('Catatan'),

            Forms\Components\FileUpload::make('image')
                ->image()
                ->required()
                ->label('Gambar'),
        ]);
    }

    /**
     * Define the table schema for the resource.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('category.image')
                ->label('Kategori')
                ->sortable(),

            Tables\Columns\TextColumn::make('category.name')
                ->description(fn (Transaction $record): string => $record->name)
                ->label('Transaksi'),

            Tables\Columns\IconColumn::make('category.is_expense')
                ->label('Tipe')
                ->trueIcon('heroicon-o-arrow-up-circle')
                ->falseIcon('heroicon-o-arrow-down-circle')
                ->trueColor('danger')
                ->falseColor('success')
                ->boolean(),

            Tables\Columns\TextColumn::make('date_transaction')
                ->date()
                ->label('Tanggal')
                ->sortable(),

            Tables\Columns\TextColumn::make('amount')
                ->label('Jumlah')
                ->sortable()
                ->formatStateUsing(fn (string $state): string => 'Rp ' . number_format((float) $state, 2, ',', '.')),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            // Define filters here
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    /**
     * Define the relations for the resource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            // Define relations here
        ];
    }

    /**
     * Define the pages for the resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}