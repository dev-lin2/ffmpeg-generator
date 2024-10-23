<?php

namespace App\Filament\Resources\BirthDayUserResource\Pages;

use App\Filament\Resources\BirthDayUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBirthDayUser extends EditRecord
{
    protected static string $resource = BirthDayUserResource::class;

    protected static ?string $breadcrumb = 'ユーザーを編集';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('表示'),
            Actions\DeleteAction::make()->label('削除'),
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
                ->label('保存')
                ->action('save')
                ->color('primary'),
        ];
    }

    public function getTitle(): string
    {
        return 'ユーザーを編集';
    }
}
