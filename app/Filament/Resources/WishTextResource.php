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

class WishTextResource extends Resource
{
    protected static ?string $model = WishText::class;
    protected static ?string $label = 'ウィッシュ テキスト';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPluralLabel(): ?string
    {
        return 'ウィッシュ テキスト';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                // Wish texts 1 2 3
                Forms\Components\Fieldset::make('ウィッシュ A')
                    ->schema([
                        Textarea::make('wish_1_text_1')
                            ->label('ウィッシュ A テキスト 1')
                            ->required(),
                        Textarea::make('wish_1_text_2')
                            ->label('ウィッシュ A テキスト 2')
                            ->required(),
                        Textarea::make('wish_1_text_3')
                            ->label('ウィッシュ A テキスト 3')
                            ->required(),
                    ]),

                Forms\Components\Fieldset::make('ウィッシュ B')
                    ->schema([
                        Textarea::make('wish_2_text_1')
                            ->label('ウィッシュ B テキスト 1')
                            ->required(),
                        Textarea::make('wish_2_text_2')
                            ->label('ウィッシュ B テキスト 2')
                            ->required(),
                        Textarea::make('wish_2_text_3')
                            ->label('ウィッシュ B テキスト 3')
                            ->required(),
                    ]),

                Forms\Components\Fieldset::make('ウィッシュ C')
                    ->schema([
                        Textarea::make('wish_3_text_1')
                            ->label('ウィッシュ 3 テキスト 1')
                            ->required(),
                        Textarea::make('wish_3_text_2')
                            ->label('ウィッシュ 3 テキスト 2')
                            ->required(),
                        Textarea::make('wish_3_text_3')
                            ->label('ウィッシュ 3 テキスト 3')
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
                    ->label('ウィッシュ 1 テキスト A'),
                Tables\Columns\TextColumn::make('wish_1_text_2')
                    ->label('ウィッシュ 1 テキスト B'),
                Tables\Columns\TextColumn::make('wish_1_text_3')
                    ->label('ウィッシュ 1 テキスト C'),

                Tables\Columns\TextColumn::make('wish_2_text_1')
                    ->label('ウィッシュ 2 テキスト A'),
                Tables\Columns\TextColumn::make('wish_2_text_2')
                    ->label('ウィッシュ 2 テキスト B'),
                Tables\Columns\TextColumn::make('wish_2_text_3')
                    ->label('ウィッシュ 2 テキスト C'),

                Tables\Columns\TextColumn::make('wish_3_text_1')
                    ->label('ウィッシュ 3 テキスト A'),
                Tables\Columns\TextColumn::make('wish_3_text_2')
                    ->label('ウィッシュ 3 テキスト B'),
                Tables\Columns\TextColumn::make('wish_3_text_3')
                    ->label('ウィッシュ 3 テキスト C'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
