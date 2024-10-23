<?php

namespace App\Filament\Resources\TemplateVideoResource\Pages;

use App\Filament\Resources\TemplateVideoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTemplateVideo extends CreateRecord
{
    // label
    protected static ?string $label = "新規動画テンプレート作成";
    // breadcrumb
    protected static ?string $breadcrumb = "新規動画テンプレート作成";
    // description
    protected static ?string $title = '新規動画テンプレート作成';

    protected static string $resource = TemplateVideoResource::class;

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('キャンセル')
                ->action(fn () => $this->redirect($this->getResource()::getUrl('index')))
                ->color('danger'),
            Actions\Action::make('save')
                ->label('作成')
                ->action('create')
                ->color('primary'),
        ];
    }
}
