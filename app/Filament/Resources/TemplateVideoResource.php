<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateVideoResource\Pages;
use App\Filament\Resources\TemplateVideoResource\RelationManagers;
use App\Models\TemplateVideo;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateVideoResource extends Resource
{
    protected static ?string $model = TemplateVideo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static ?string $label = '動画テンプレート一覧';

    public static function getPluralLabel(): ?string
    {
        return '動画テンプレート一覧';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('動画名'),
                TextInput::make('video_url')
                    ->label('動画URL'),
                Textarea::make('wish_text_1')
                    ->label('テキスト1'),
                Textarea::make('wish_text_2')
                    ->label('テキスト2'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('動画名')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('wish_text_1')
                    ->label('テキスト1')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('wish_text_2')
                    ->label('テキスト2')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('video_url')
                    ->label('動画URL')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('編集'),
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
            'index' => Pages\ListTemplateVideos::route('/'),
            'create' => Pages\CreateTemplateVideo::route('/create'),
            'edit' => Pages\EditTemplateVideo::route('/{record}/edit'),
        ];
    }
}
