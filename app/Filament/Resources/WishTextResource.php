<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WishTextResource\Pages;
use App\Filament\Resources\WishTextResource\RelationManagers;
use App\Models\WishText;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WishTextResource extends Resource
{
    protected static ?string $model = WishText::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                // Wish texts 1 2 3
                Forms\Components\Fieldset::make('Wish 1')
                    ->schema([
                        Textarea::make('wish_1_text_1')
                            ->label('Wish 1 Text A')
                            ->required(),
                        Textarea::make('wish_1_text_2')
                            ->label('Wish 1 Text B')
                            ->required(),
                        Textarea::make('wish_1_text_3')
                            ->label('Wish 1 Text C')
                            ->required(),
                    ]),

                Forms\Components\Fieldset::make('Wish 2')
                    ->schema([
                        Textarea::make('wish_2_text_1')
                            ->label('Wish 2 Text A')
                            ->required(),
                        Textarea::make('wish_2_text_2')
                            ->label('Wish 2 Text B')
                            ->required(),
                        Textarea::make('wish_2_text_3')
                            ->label('Wish 2 Text C')
                            ->required(),
                    ]),

                Forms\Components\Fieldset::make('Wish 3')
                    ->schema([
                        Textarea::make('wish_3_text_1')
                            ->label('Wish 3 Text A')
                            ->required(),
                        Textarea::make('wish_3_text_2')
                            ->label('Wish 3 Text B')
                            ->required(),
                        Textarea::make('wish_3_text_3')
                            ->label('Wish 3 Text C')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                // Wish texts 1 2 3
                Tables\Columns\TextColumn::make('wish_1_text_1')
                    ->label('Wish 1 Text A'),
                Tables\Columns\TextColumn::make('wish_1_text_2')
                    ->label('Wish 1 Text B'),
                Tables\Columns\TextColumn::make('wish_1_text_3')
                    ->label('Wish 1 Text C'),

                Tables\Columns\TextColumn::make('wish_2_text_1')
                    ->label('Wish 2 Text A'),
                Tables\Columns\TextColumn::make('wish_2_text_2')
                    ->label('Wish 2 Text B'),
                Tables\Columns\TextColumn::make('wish_2_text_3')
                    ->label('Wish 2 Text C'),

                Tables\Columns\TextColumn::make('wish_3_text_1')
                    ->label('Wish 3 Text A'),
                Tables\Columns\TextColumn::make('wish_3_text_2')
                    ->label('Wish 3 Text B'),
                Tables\Columns\TextColumn::make('wish_3_text_3')
                    ->label('Wish 3 Text C'),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWishTexts::route('/'),
            'create' => Pages\CreateWishText::route('/create'),
            'edit' => Pages\EditWishText::route('/{record}/edit'),
        ];
    }
}
