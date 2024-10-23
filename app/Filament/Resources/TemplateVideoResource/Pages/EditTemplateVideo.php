<?php

namespace App\Filament\Resources\TemplateVideoResource\Pages;

use App\Filament\Resources\TemplateVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplateVideo extends EditRecord
{
    protected static string $resource = TemplateVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('削除'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('キャンセル')
                ->action(fn() => $this->redirect($this->getResource()::getUrl('index')))
                ->color('danger'),
            Actions\Action::make('save')
                ->label('保存')
                ->action('save')
                ->color('primary'),
        ];
    }
}
