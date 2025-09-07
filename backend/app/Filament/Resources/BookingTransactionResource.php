<?php

namespace App\Filament\Resources;

use Dom\Text;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Cosmetic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\BookingTransaction;
use Filament\Forms\FormsComponent;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookingTransactionResource\Pages;
use App\Filament\Resources\BookingTransactionResource\RelationManagers;

class BookingTransactionResource extends Resource
{
    protected static ?string $model = BookingTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function updateTotals(Get $get, Set $set): void
    {   
        $selectedCosmetics = collect($get('transactionDetails'))->filter(fn($item)
        => !empty($item['cosmetic_id']) && !empty($item['quantity']) );

        $prices = Cosmetic::find($selectedCosmetics->pluck('cosmetic_id'))->pluck('price', 'id');
        
        $subtotal = $selectedCosmetics->reduce(function ($subtotal, $item) use ($prices){
            return $subtotal + ($prices[$item['cosmetic_id']] * $item['quantity']);
        }, 0);

        $total_tax_amount = round($subtotal * 0.11);

        $total_amount = round($subtotal + $total_tax_amount);

        $total_quantity = $selectedCosmetics->sum('quantity');

        $set('total_amount', number_format($total_amount, 0, '-', ''));

        $set('total_tax_amount', number_format($total_tax_amount, 0, '.', ''));

        $set('sub_total_amount', number_format($subtotal, 0, '.', ''));
        $set('quantity', $total_quantity);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //Komponen tambahan (plugin) di FilamentPHP yang memungkinkan kamu membuat form multi-step (bertingkat), seperti wizard atau langkah demi langkah (stepper).
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
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set){
                                self::updateTotals($get, $set);
                            })
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

                            TextInput::make('sub_total_amount')
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

                    ]),

                    Forms\Components\Wizard\Step::make('Customer Information')
                    ->completedIcon('heroicon-m-hand-thumb-up')
                    ->description('For our marketing')
                    ->schema([
                        Grid::make(2)
                        ->schema([
                            TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                            TextInput::make('phone')
                            ->required()
                            ->maxLength(255),

                            TextInput::make('email')
                            ->required()
                            ->maxLength(255),
                        ])
                    ]),

                    Forms\Components\Wizard\Step::make('Delivery Information')
                    ->completedIcon('heroicon-m-hand-thumb-up')
                    ->description('Put your correct address')
                    ->schema([
                        Grid::make(2)
                        ->schema([
                            TextInput::make('city')
                            ->required()
                            ->maxLength(255),

                            TextInput::make('post_code')
                            ->required()
                            ->maxLength(255),

                            Textarea::make('address')
                            ->required()
                            ->maxLength(255),
                        ])
                    ]),

                    Forms\Components\Wizard\Step::make('Payment Information')
                    ->completedIcon('heroicon-m-hand-thumb-up')
                    ->description('Review your payment')
                    ->schema([
                        Grid::make(3)
                        ->schema([
                            TextInput::make('booking_trx_id')
                            ->required()
                            ->maxLength(255),

                            ToggleButtons::make('is_paid')
                            ->label('Apakah sudah membayar?')
                            ->boolean()
                            ->grouped()
                            ->icons([
                                true => 'heroicon-o-pencil',
                                false => 'heroicon-o-clock',
                            ])
                            ->required(),

                            FileUpload::make('proof')
                            ->image()
                            ->required(),
                        ])
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
                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('booking_trx_id')
                ->searchable(),

                TextColumn::make('created_at'),

                IconColumn::make('is_paid')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Terverifikasi'),
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
