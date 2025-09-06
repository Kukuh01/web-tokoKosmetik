<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\BookingTransaction;
use Filament\Forms\FormsComponent;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;
use App\Models\Cosmetic;
use Dom\Text;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Wizard::make([

                    Forms\Components\Wizard\Step::make('Product and Price')
                    ->completedIcon('heroicon-m-hand-thumb-up')
                    ->description('Add your product item')
                    ->schema([

                        Grid::make(2)
                        ->schema([

                            Repeater::make('transactionDetails')
                            ->relationship('transactionDetails')
                            ->schema([

                                Select::make('cosmetic_id')
                                ->relationship('Cosmetics','name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->label('Select Product')
                                ->live() // Memberi tahu kepada filament updated
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $cosmetic = Cosmetic::find($state); //Pencarian
                                    $set('price', $cosmetic ? $cosmetic->price : 0); // set kolom price
                                }),

                                TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->readOnly()
                                ->label('price')
                                ->hint('Price will be filled automatically based on product section'),

                                TextInput::make('quantity')
                                ->integer()
                                ->default(1)
                                ->required(),

                            ])
                            ->minItems(1)
                            ->columnSpan('full')
                            ->label('Choose Products'),

                        ]),

                        Grid::make(4)
                        ->schema([
                            TextInput::make('quantity')
                            ->integer()
                            ->label('Total Quantity')
                            ->readOnly()
                            ->default(1)
                            ->required(),

                            TextInput::make('sub_total_amout')
                            ->numeric()
                            ->readOnly()
                            ->label('Sub Total Amount'),

                            TextInput::make('total_amount')
                            ->numeric()
                            ->readOnly()
                            ->label('Total Amount'),

                            TextInput::make('total_tax_amount')
                            ->numeric()
                            ->readOnly()
                            ->label('Total Tax (11%)'),
                        ]),

                    ])

                ])
                // add style or adjust wizard
                ->columnSpan('full') //Use full width for the wizard
                ->columns(1) // make sure the form has a single column layout
                ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingTransactions::route('/'),
            'create' => Pages\CreateBookingTransaction::route('/create'),
            'edit' => Pages\EditBookingTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
