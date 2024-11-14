<?php

namespace App\Filament\Resources\WishTextResource\Pages;

use App\Filament\Resources\WishTextResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWishText extends EditRecord
{
    protected static string $resource = WishTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('キャンセル')
                ->action(fn () => $this->redirect($this->getResource()::getUrl('index')))
                ->color('danger'),
            Actions\Action::make('save')
                ->label('変更')
                ->action('save')
                ->color('primary'),
        ];
    }
}
