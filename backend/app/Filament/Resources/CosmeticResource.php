<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Cosmetic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CosmeticResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CosmeticResource\RelationManagers;
use App\Filament\Resources\CosmeticResource\RelationManagers\CosmeticTestimonialsRelationManager;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class CosmeticResource extends Resource
{
    protected static ?string $model = Cosmetic::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //Input data
                Fieldset::make('Details') //Create new section
                ->schema([
                    //Inputan
                    Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->required(),

                    Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->required(),

                    Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('IDR')
                    ->required(),

                    Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->prefix('Qtys')
                    ->required(),
                ]),

                Fieldset::make('Additional')
                ->schema([
                    Repeater::make('benefits') // To repeat create input
                    ->relationship('cosmeticBenefits') // add benefit relation
                    ->schema([ //Schema to be run
                        TextInput::make('name')
                        ->required(),
                    ]),

                    Repeater::make('photos')
                    ->relationship('cosmeticPhotos')
                    ->schema([
                        FileUpload::make('photo')
                        ->image()
                        ->required(),
                    ]),

                    Textarea::make('about')
                    ->required(),

                    Select::make('is_popular')
                    ->options([
                        true => 'Popular',
                        false => 'Not Popular'
                    ])
                    ->required(),

                    Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                    Select::make('brand_id')
                    ->relationship('brand','name')
                    ->searchable()
                    ->preload()
                    ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //Display data
                ImageColumn::make('thumbnail'),

                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('category.name'),
                TextColumn::make('brand.name'),

                IconColumn::make('is_popular')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Popular'),
            ])
            ->filters([
                //To make filter
                SelectFilter::make('category_id')
                ->label('Category')
                ->relationship('category', 'name'),

                SelectFilter::make('brand_id')
                ->label('brand')
                ->relationship('brand','name'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            CosmeticTestimonialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCosmetics::route('/'),
            'create' => Pages\CreateCosmetic::route('/create'),
            'edit' => Pages\EditCosmetic::route('/{record}/edit'),
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
